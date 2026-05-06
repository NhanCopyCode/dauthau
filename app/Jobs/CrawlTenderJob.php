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

        if ($page === 1 && !Cache::has('crawl_start_time')) {
            Cache::put('crawl_start_time', now());
            Cache::put('crawl_total_items', 0);
            Cache::put('crawl_total_pages', 0);

            Log::info("CRAWL START");
        }

        $lockKey = "crawl_page_{$page}";
        $lock = Cache::lock($lockKey, 300);

        if (!$lock->get()) {
            Log::warning("Page {$page} is being processed. Skip.");
            return;
        }

        $pageStart = microtime(true);

        try {
            $data = $service->crawlPage($page);
            $items = $data['page']['content'] ?? [];

            if (empty($items)) {

                $start = Cache::get('crawl_start_time');
                $end = now();

                $totalItems = Cache::get('crawl_total_items', 0);
                $totalPages = Cache::get('crawl_total_pages', 0);

                $duration = $start ? $end->diffInSeconds($start) : 0;

                Log::info("CRAWL DONE", [
                    'start' => $start,
                    'end' => $end,
                    'duration_seconds' => $duration,
                    'total_items' => $totalItems,
                    'total_pages' => $totalPages,
                    'items_per_second' => $duration > 0 ? round($totalItems / $duration, 2) : 0,
                ]);

                return;
            }

            $tenders = $service->saveItems($items);

            $count = count($items);

            Cache::increment('crawl_total_items', $count);
            Cache::increment('crawl_total_pages');

            $pageEnd = microtime(true);

            Log::info("PAGE DONE", [
                'page' => $page,
                'items' => $count,
                'time_seconds' => round($pageEnd - $pageStart, 3),
            ]);

            foreach ($tenders as $tender) {

                dispatch(new CrawlTenderDetailJob($tender->id))
                    ->onQueue('detail');

                dispatch(new CrawlTenderSubResourceJob($tender->id, 'yclr'))
                    ->onQueue('sub');

                dispatch(new CrawlTenderSubResourceJob($tender->id, 'hntdt'))
                    ->onQueue('sub');

                dispatch(new CrawlTenderSubResourceJob($tender->id, 'kn'))
                    ->onQueue('sub');
            }

            dispatch(new self($page + 1))
                ->onQueue('default');
        } catch (\Throwable $e) {
            Log::error("Error page {$page}: " . $e->getMessage());
            throw $e;
        } finally {
            optional($lock)->release();
        }
    }
}
