<?php

namespace App\Console\Commands;

use App\Services\OutboundMailService;
use Illuminate\Console\Command;

class RetryEarthquickMail extends Command
{
    protected $signature = 'earthquick:mail-retry {--limit=25 : Maximum due messages to process (1-100)}';

    protected $description = 'Retry due Rthquick transactional emails from the durable outbox';

    public function handle(OutboundMailService $outbox): int
    {
        if (! $outbox->transportReady()) {
            $this->warn('Email notifications or real mail transport are not enabled; no message was sent.');
            return self::FAILURE;
        }
        $limit = filter_var($this->option('limit'), FILTER_VALIDATE_INT);
        if ($limit === false || $limit < 1 || $limit > 100) {
            $this->error('Limit must be an integer from 1 to 100.');
            return self::FAILURE;
        }
        $result = $outbox->retryDue($limit);
        $this->info("Processed {$result['processed']} due messages; SMTP handed off {$result['sent']}.");

        return self::SUCCESS;
    }
}
