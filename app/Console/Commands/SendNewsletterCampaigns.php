<?php

namespace App\Console\Commands;

use App\Services\NewsletterCampaignService;
use Illuminate\Console\Command;

class SendNewsletterCampaigns extends Command
{
    protected $signature = 'rthquick:newsletter-send {--limit=5}';

    protected $description = 'Send the next small batch of confirmed Nous Telos newsletter campaign messages';

    public function handle(NewsletterCampaignService $service): int
    {
        $sent = $service->pump((int) $this->option('limit'));
        $this->info("Processed {$sent} campaign recipients.");

        return self::SUCCESS;
    }
}
