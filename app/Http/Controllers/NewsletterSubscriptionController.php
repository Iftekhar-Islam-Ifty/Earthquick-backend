<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscription;
use App\Services\OutboundMailService;
use App\Services\NewsletterCampaignService;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class NewsletterSubscriptionController extends Controller
{
    private const STORE_SLUG = 'nous-telos';

    public function store(Request $request, OutboundMailService $outbox, NewsletterCampaignService $mailer): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'website' => ['nullable', 'string', 'max:100'],
        ]);
        $return = redirect()->to(route('stores.show', self::STORE_SLUG).'#newsletter-section');
        if (! empty($data['website'])) {
            return $return->with('newsletter_status', 'If this address can receive mail, a confirmation link will arrive shortly.');
        }
        // Newsletter mail must use the designated NOC mailbox, not a leftover local sender.
        if (! $mailer->senderReady()) {
            return $return->with('newsletter_error', 'Email signup is temporarily unavailable. Please try again later.');
        }

        $email = Str::lower(trim($data['email']));
        $token = Str::random(64);
        try {
            $subscription = DB::transaction(function () use ($email, $token) {
                $subscription = NewsletterSubscription::query()->where('store_slug', self::STORE_SLUG)
                    ->where('email', $email)->lockForUpdate()->first();
                if ($subscription && ! $subscription->unsubscribed_at && $subscription->confirmed_at) {
                    return null;
                }
                // Limit repeated confirmation mail for the same address, including pending signups.
                if ($subscription && ! $subscription->unsubscribed_at && $subscription->updated_at->gt(now()->subMinutes(15))) {
                    return null;
                }
                $values = [
                    'token_hash' => hash('sha256', $token),
                    'confirmation_expires_at' => now()->addHours(48),
                    'confirmed_at' => null,
                    'unsubscribed_at' => null,
                ];
                if ($subscription) {
                    $subscription->update($values);
                } else {
                    $subscription = NewsletterSubscription::create($values + [
                        'store_slug' => self::STORE_SLUG,
                        'email' => $email,
                    ]);
                }

                return $subscription;
            });
        } catch (UniqueConstraintViolationException) {
            // A simultaneous request already created this unique store/email pair.
            $subscription = null;
        }

        if ($subscription) {
            $url = route('newsletter.confirm', ['subscription' => $subscription->id, 'token' => $token]);
            $outbox->queueAndSend($email, 'Confirm your Nous Telos newsletter signup',
                "You asked to receive Nous Telos updates through Rthquick. Confirm your email within 48 hours using this link:\n{$url}\n\nIf this was not you, you can ignore this message.",
                ['event' => 'newsletter_confirmation', 'subscription_id' => $subscription->id]);
        }

        return $return->with('newsletter_status', 'If this address can receive mail, a confirmation link will arrive shortly.');
    }

    public function confirmForm(int $subscription, string $token): View
    {
        $record = NewsletterSubscription::findOrFail($subscription);
        abort_unless(hash_equals($record->token_hash, hash('sha256', $token)), 404);

        return view('newsletter.confirm', ['subscription' => $record, 'token' => $token]);
    }

    public function confirm(int $subscription, string $token, OutboundMailService $outbox, NewsletterCampaignService $mailer): RedirectResponse
    {
        $record = NewsletterSubscription::findOrFail($subscription);
        abort_unless(hash_equals($record->token_hash, hash('sha256', $token)), 404);
        if ($record->unsubscribed_at || $record->confirmation_expires_at?->isPast()) {
            return redirect()->to(route('stores.show', self::STORE_SLUG).'#newsletter-section')
                ->with('newsletter_error', 'This confirmation link has expired. Please sign up again.');
        }
        if (! $mailer->senderReady()) {
            return redirect()->to(route('stores.show', self::STORE_SLUG).'#newsletter-section')
                ->with('newsletter_error', 'Email confirmation is temporarily unavailable. Please try again later.');
        }
        $newlyConfirmed = NewsletterSubscription::query()->whereKey($record->id)
            ->whereNull('confirmed_at')->whereNull('unsubscribed_at')
            ->where('confirmation_expires_at', '>', now())
            ->update(['confirmed_at' => now(), 'confirmation_expires_at' => null]);
        if ($newlyConfirmed) {
            $unsubscribeUrl = $record->fresh()->unsubscribeUrl();
            $outbox->queueAndSend($record->email, 'Welcome to the Nous Telos newsletter',
                "Your Nous Telos newsletter subscription is confirmed. You can unsubscribe any time here:\n{$unsubscribeUrl}",
                ['event' => 'newsletter_welcome', 'subscription_id' => $record->id]);
            $recipient = config('communications.support_email');
            if ($recipient && filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
                $outbox->queueAndSend($recipient, 'Rthquick: New Nous Telos newsletter subscriber',
                    "A new Nous Telos newsletter subscription was confirmed: {$record->email}.",
                    ['event' => 'newsletter_admin', 'subscription_id' => $record->id]);
            }
        }

        return redirect()->to(route('stores.show', self::STORE_SLUG).'#newsletter-section')
            ->with('newsletter_status', 'Your email is confirmed. You are subscribed to Nous Telos updates.');
    }

    public function unsubscribeForm(Request $request, int $subscription): View
    {
        $record = NewsletterSubscription::findOrFail($subscription);
        abort_unless($record->confirmed_at && (string) $record->confirmed_at->timestamp === (string) $request->query('version'), 404);

        return view('newsletter.unsubscribe', ['subscription' => $record]);
    }

    public function unsubscribe(Request $request, int $subscription): RedirectResponse
    {
        $record = NewsletterSubscription::findOrFail($subscription);
        abort_unless($record->confirmed_at && (string) $record->confirmed_at->timestamp === (string) $request->query('version'), 404);
        $record->update(['unsubscribed_at' => $record->unsubscribed_at ?? now()]);

        return redirect()->to(route('stores.show', self::STORE_SLUG).'#newsletter-section')
            ->with('newsletter_status', 'You have unsubscribed from Nous Telos updates.');
    }
}
