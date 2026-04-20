<?php

namespace App\Jobs;

use App\Services\TenderCrawlerService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Bus;
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
        $lock = Cache::lock($lockKey, 300);

        if (!$lock->get()) {
            Log::warning("Page {$page} is being processed. Skip.");
            return;
        }

        try {
            $data = $service->crawlPage($page);
            $items = $data['page']['content'] ?? [];

            if (empty($items)) {
                Log::info("Stop at page: {$page}");
                return;
            }

            $tenders = $service->saveItems($items);

            $jobs = [];

            foreach ($tenders as $tender) {
                $jobs[] = (new CrawlTenderDetailJob($tender->id))
                    ->onQueue('detail');
            }

            $jobs[] = (new CrawlTenderJob($page + 1))
                ->onQueue('default');

            Bus::chain($jobs)->dispatch();
        } catch (\Exception $e) {
            Log::error("Error page {$page}: " . $e->getMessage());
            throw $e;
        } finally {
            optional($lock)->release();
        }
    }
}
