<?php

namespace App\Jobs;

use App\Services\TenderCrawlerService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CrawlTenderJob implements ShouldQueue
{
    use Queueable;

    public $tries = 5;
    public $timeout = 120;

    protected $page;

    public function __construct($page)
    {
        $this->page = $page;
    }


    public function handle(TenderCrawlerService $service)
    {
        $page = $this->page;

        Log::info("Crawling page: {$page}");

        $lockKey = "crawl_page_{$page}";

        if (Cache::has($lockKey)) {
            Log::warning("Page {$page} already processed. Skip.");
            return;
        }

        Cache::put($lockKey, true, now()->addHours(6));

        try {
            $data = $service->crawlPage($page);
            $items = $data['page']['content'] ?? [];

            if (empty($items)) {
                Log::info("Stop at page: {$page}");
                return;
            }

            $service->saveItems($items);

            CrawlTenderJob::dispatch($page + 1)
                ->delay(now()->addSeconds(2));
        } catch (\Exception $e) {

            Log::error("Error page {$page}: " . $e->getMessage());

            throw $e;
        }
    }
}
