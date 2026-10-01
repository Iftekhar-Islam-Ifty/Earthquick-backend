<?php

namespace App\Console\Commands;

use App\Models\OutboundMessage;
use App\Services\OutboundMailService;
use Illuminate\Console\Command;

class EarthquickMailHealth extends Command
{
    protected $signature = 'earthquick:mail-health';

    protected $description = 'Show safe email transport and outbox health information without sending email';

    public function handle(OutboundMailService $outbox): int
    {
        $this->table(['Check', 'Value'], [
            ['Notifications enabled', config('communications.email_enabled') ? 'yes' : 'no'],
            ['Mailer', (string) config('mail.default')],
            ['Transport ready', $outbox->transportReady() ? 'yes' : 'no'],
            ['Pending', OutboundMessage::where('status', 'pending')->count()],
            ['Stale sending (>10 min)', OutboundMessage::where('status', 'sending')->where('last_attempt_at', '<=', now()->subMinutes(10))->count()],
            ['Failed (needs staff review)', OutboundMessage::where('status', 'failed')->count()],
            ['SMTP handed off', OutboundMessage::where('status', 'sent')->count()],
        ]);
        $this->line('SMTP handoff is not proof of inbox delivery. No email was sent by this command.');

        return self::SUCCESS;
    }
}
