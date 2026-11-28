<?php

namespace App\Services;

use App\Mail\RthquickNotice;
use App\Models\OutboundMessage;
use App\Models\NewsletterSubscription;
use App\Models\NewsletterCampaign;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Throwable;

class OutboundMailService
{
    public const MAX_ATTEMPTS = 5;

    public function queueAndSend(string $email, string $subject, string $body, array $context): void
    {
        try {
            $this->deliver($this->enqueue($email, $subject, $body, $context));
        } catch (Throwable $exception) {
            // An email subsystem failure must not undo a committed order/inquiry.
            Log::warning('Rthquick email could not be recorded', $context + ['exception' => $exception::class]);
        }
    }

    public function enqueue(string $email, string $subject, string $body, array $context): int
    {
        return OutboundMessage::create([
            'recipient_hint' => $this->mask($email),
            'recipient' => $email,
            'subject' => $subject,
            'body' => $body,
            'context' => $context,
            'status' => 'pending',
            'next_attempt_at' => now(),
        ])->id;
    }

    public function deliver(int $id): bool
    {
        if (! $this->transportReady()) {
            return false;
        }
        $candidate = OutboundMessage::find($id);
        $event = $candidate?->context['event'] ?? '';
        if (str_starts_with($event, 'newsletter_') && ! app(NewsletterCampaignService::class)->senderReady()) {
            return false;
        }

        $message = DB::transaction(function () use ($id) {
            $message = OutboundMessage::query()->lockForUpdate()->find($id);
            if (! $message || $message->attempts >= self::MAX_ATTEMPTS) {
                return null;
            }
            $due = $message->status === 'pending' && $message->next_attempt_at?->lte(now());
            $stale = $message->status === 'sending' && $message->last_attempt_at?->lte(now()->subMinutes(10));
            if (! $due && ! $stale) {
                return null;
            }
            $message->update([
                'status' => 'sending',
                'attempts' => $message->attempts + 1,
                'last_attempt_at' => now(),
                'next_attempt_at' => null,
            ]);

            return $message;
        });
        if (! $message) {
            return false;
        }

        if (($message->context['event'] ?? null) === 'newsletter_campaign') {
            $subscription = NewsletterSubscription::find($message->context['subscription_id'] ?? null);
            $campaign = NewsletterCampaign::find($message->context['campaign_id'] ?? null);
            if (! $campaign || $campaign->status === 'cancelled'
                || ! $subscription || ! $subscription->confirmed_at || $subscription->unsubscribed_at) {
                // Retry jobs must respect an unsubscribe that happened after enqueue.
                OutboundMessage::whereKey($id)->where('status', 'sending')->update([
                    'status' => 'skipped', 'recipient' => null, 'subject' => null, 'body' => null,
                    'next_attempt_at' => null, 'updated_at' => now(),
                ]);

                return false;
            }
        }

        try {
            Mail::to($message->recipient)->send(new RthquickNotice($message->subject, $message->body, $message->context['event'] ?? null));
            // Clear recoverable recipient/content after successful handoff.
            OutboundMessage::whereKey($id)->where('status', 'sending')->update([
                'status' => 'sent',
                'recipient' => null,
                'subject' => null,
                'body' => null,
                'sent_at' => now(),
                'last_error_class' => null,
                'updated_at' => now(),
            ]);

            return true;
        } catch (Throwable $exception) {
            $attempts = $message->attempts;
            $delays = [5, 15, 60, 360];
            OutboundMessage::whereKey($id)->where('status', 'sending')->update([
                'status' => $attempts >= self::MAX_ATTEMPTS ? 'failed' : 'pending',
                'next_attempt_at' => $attempts >= self::MAX_ATTEMPTS ? null : now()->addMinutes($delays[$attempts - 1]),
                'last_error_class' => $exception::class,
                'updated_at' => now(),
            ]);
            Log::warning('Rthquick email delivery failed; outbox retained for retry', ($message->context ?? []) + [
                'message_id' => $id,
                'attempts' => $attempts,
                'exception' => $exception::class,
            ]);

            return false;
        }
    }

    public function retryDue(int $limit = 25): array
    {
        if (! $this->transportReady()) {
            return ['processed' => 0, 'sent' => 0];
        }
        // A worker can die after claiming its final attempt. Do not leave that
        // message in "sending" forever or silently dispatch a sixth attempt.
        OutboundMessage::query()->where('status', 'sending')
            ->where('attempts', '>=', self::MAX_ATTEMPTS)
            ->where('last_attempt_at', '<=', now()->subMinutes(10))
            ->update(['status' => 'failed', 'next_attempt_at' => null, 'updated_at' => now()]);
        $ids = OutboundMessage::query()->where(function ($query) {
            $query->where(fn ($q) => $q->where('status', 'pending')->where('next_attempt_at', '<=', now()))
                ->orWhere(fn ($q) => $q->where('status', 'sending')->where('last_attempt_at', '<=', now()->subMinutes(10)));
        })->where('attempts', '<', self::MAX_ATTEMPTS)->orderBy('id')->limit(min(max($limit, 1), 100))->pluck('id');

        $sent = 0;
        foreach ($ids as $id) {
            $sent += $this->deliver($id) ? 1 : 0;
        }

        return ['processed' => $ids->count(), 'sent' => $sent];
    }

    public function requeueFailed(int $id): bool
    {
        if (! $this->transportReady()) {
            throw ValidationException::withMessages(['email' => 'Enable a real mail transport before retrying.']);
        }
        DB::transaction(function () use ($id) {
            $message = OutboundMessage::query()->lockForUpdate()->findOrFail($id);
            if ($message->status !== 'failed' || ! $message->recipient || ! $message->subject || ! $message->body) {
                throw ValidationException::withMessages(['email' => 'Only failed messages with saved content can be retried.']);
            }
            $message->update(['status' => 'pending', 'attempts' => 0, 'next_attempt_at' => now(), 'last_error_class' => null]);
        });

        return $this->deliver($id);
    }

    public function transportReady(): bool
    {
        return (bool) config('communications.email_enabled')
            && is_string(config('mail.default'))
            && config('mail.default') !== ''
            && ! in_array(config('mail.default'), ['log', 'array', 'failover'], true);
    }

    private function mask(string $email): string
    {
        [$local, $domain] = explode('@', $email, 2);

        return mb_substr($local, 0, 1).'***@'.$domain;
    }
}
