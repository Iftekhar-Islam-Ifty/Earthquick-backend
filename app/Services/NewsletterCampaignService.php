<?php

namespace App\Services;

use App\Models\NewsletterCampaign;
use App\Models\NewsletterCampaignRecipient;
use App\Models\NewsletterSubscription;
use App\Models\OutboundMessage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class NewsletterCampaignService
{
    public function senderReady(): bool
    {
        $address = config('communications.support_email');

        return app(OutboundMailService::class)->transportReady()
            && config('mail.default') === 'smtp'
            && filter_var($address, FILTER_VALIDATE_EMAIL)
            && strcasecmp((string) config('mail.from.address'), (string) $address) === 0
            && strcasecmp((string) config('mail.mailers.smtp.username'), (string) $address) === 0;
    }

    public function launch(NewsletterCampaign $campaign): int
    {
        if (! $this->senderReady()) {
            throw ValidationException::withMessages(['campaign' => 'Configure the NOC SMTP sender before sending a newsletter.']);
        }

        return DB::transaction(function () use ($campaign) {
            $campaign = NewsletterCampaign::query()->lockForUpdate()->findOrFail($campaign->id);
            if ($campaign->status !== 'draft') {
                throw ValidationException::withMessages(['campaign' => 'This campaign has already been launched.']);
            }
            $ids = NewsletterSubscription::query()->where('store_slug', $campaign->store_slug)
                ->whereNotNull('confirmed_at')->whereNull('unsubscribed_at')->pluck('id');
            if ($ids->isEmpty()) {
                throw ValidationException::withMessages(['campaign' => 'There are no confirmed active subscribers yet.']);
            }
            foreach ($ids->chunk(500) as $chunk) {
                NewsletterCampaignRecipient::insert($chunk->map(fn ($id) => [
                    'newsletter_campaign_id' => $campaign->id,
                    'newsletter_subscription_id' => $id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])->all());
            }
            $campaign->update(['status' => 'sending', 'launched_at' => now()]);

            return $ids->count();
        });
    }

    public function cancel(NewsletterCampaign $campaign): void
    {
        DB::transaction(function () use ($campaign) {
            $campaign = NewsletterCampaign::query()->lockForUpdate()->findOrFail($campaign->id);
            if ($campaign->status !== 'sending') {
                throw ValidationException::withMessages(['campaign' => 'Only a sending campaign can be stopped.']);
            }
            $campaign->update(['status' => 'cancelled', 'completed_at' => now()]);
            $campaign->recipients()->whereNull('outbound_message_id')->whereNull('skipped_at')
                ->update(['skipped_at' => now()]);
        });
    }

    public function pump(int $limit = 5): int
    {
        if (! $this->senderReady()) {
            return 0;
        }
        $ids = NewsletterCampaignRecipient::query()
            ->whereNull('outbound_message_id')->whereNull('skipped_at')
            ->whereHas('subscription')
            ->whereHas('campaign', fn ($query) => $query->where('status', 'sending'))
            ->orderBy('id')->limit(min(max($limit, 1), 20))->pluck('id');

        $processed = 0;
        foreach ($ids as $id) {
            $messageId = DB::transaction(function () use ($id) {
                $recipient = NewsletterCampaignRecipient::query()->lockForUpdate()->find($id);
                if (! $recipient || $recipient->outbound_message_id || $recipient->skipped_at) {
                    return null;
                }
                $campaign = NewsletterCampaign::find($recipient->newsletter_campaign_id);
                $subscription = NewsletterSubscription::find($recipient->newsletter_subscription_id);
                if (! $campaign || $campaign->status !== 'sending' || ! $subscription
                    || ! $subscription->confirmed_at || $subscription->unsubscribed_at) {
                    $recipient->update(['skipped_at' => now()]);
                    return null;
                }
                $body = trim($campaign->body)."\n\nUnsubscribe from Nous Telos updates:\n".$subscription->unsubscribeUrl();
                $messageId = app(OutboundMailService::class)->enqueue(
                    $subscription->email, $campaign->subject, $body,
                    ['event' => 'newsletter_campaign', 'campaign_id' => $campaign->id, 'subscription_id' => $subscription->id]
                );
                $recipient->update(['outbound_message_id' => $messageId]);

                return $messageId;
            });
            if ($messageId) {
                app(OutboundMailService::class)->deliver($messageId);
            }
            $processed++;
        }

        NewsletterCampaign::query()->where('status', 'sending')->get()->each(function ($campaign) {
            $waiting = NewsletterCampaignRecipient::query()->where('newsletter_campaign_id', $campaign->id)
                ->whereNull('outbound_message_id')->whereNull('skipped_at')->exists();
            $inFlight = OutboundMessage::query()->where('context->campaign_id', $campaign->id)
                ->whereIn('status', ['pending', 'sending'])->exists();
            if (! $waiting && ! $inFlight) {
                $campaign->update(['status' => 'completed', 'completed_at' => now()]);
            }
        });

        return $processed;
    }
}
