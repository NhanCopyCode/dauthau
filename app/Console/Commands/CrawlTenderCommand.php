<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\CrawlTenderJob;

class CrawlTenderCommand extends Command
{
    protected $signature = 'crawl:tenders';
    protected $description = 'Crawl tenders data';

    public function handle()
    {
        CrawlTenderJob::dispatch(0);

        $this->info("Started crawling from page 0");
    }
}
