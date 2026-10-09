<?php

namespace App\Console\Commands;

use App\Mail\EarthquickNotice;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Throwable;

class TestEarthquickMail extends Command
{
    protected $signature = 'earthquick:mail-test {to : Inbox for one controlled test email}';

    protected $description = 'Send one controlled Rthquick email without creating an order or inquiry';

    public function handle(): int
    {
        $to = (string) $this->argument('to');
        $mailer = config('mail.default');
        $from = config('mail.from.address');

        if (! filter_var($to, FILTER_VALIDATE_EMAIL)) {
            $this->error('Enter a valid destination email address.');
            return self::FAILURE;
        }

        if (in_array($mailer, ['log', 'array', 'failover'], true)
            || ! filter_var($from, FILTER_VALIDATE_EMAIL)
            || $from === 'hello@example.com') {
            $this->error('Configure a real mail transport and sender in .env, then refresh the config cache.');
            return self::FAILURE;
        }

        try {
            Mail::to($to)->send(new EarthquickNotice(
                'Rthquick: Email delivery test',
                'This is a controlled delivery test. No customer order or support inquiry was created.'
            ));
        } catch (Throwable $exception) {
            $this->error('Mail transport failed ('.$exception::class.'). Check SMTP settings and server logs; no delivery is confirmed.');
            return self::FAILURE;
        }

        $this->info('Mail transport accepted the test message. Confirm arrival in the inbox/spam folder before enabling notifications.');
        return self::SUCCESS;
    }
}
