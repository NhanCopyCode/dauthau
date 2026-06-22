<?php

namespace App\Jobs;

use App\Models\CrawlTask;
use App\Services\CrawlTracker;
use App\Services\TenderCrawlerService;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\CrawlLogger;

class CrawlTenderDateJob implements ShouldQueue
{
    use Queueable;


    public $timeout = 120;

    public $tries = 5;
    public $backoff = [10, 30, 60];
    // public $tries = 1;  // thay vì 5

    // public $backoff = [1];  // thay vì [10, 30, 60]

    public function __construct(
        protected string $date,
        protected int $page,
        protected int $taskId
    ) {}

    public function handle(
        TenderCrawlerService $service
    ): void {

        // throw new \RuntimeException('Test fail');

        $tracker = app(CrawlTracker::class);
        $logger = app(CrawlLogger::class);


        $task = CrawlTask::findOrFail(
            $this->taskId
        );

        $page = $this->page;

        $date = Carbon::parse(
            $this->date
        );

        $lockKey = sprintf(
            'crawl_date_lock:task_%s_date_%s_page_%s',
            $this->taskId,
            $date->format('Ymd'),
            $page
        );

        $lock = Cache::lock(
            $lockKey,
            600
        );

        if (!$lock->get()) {

            $logger->warning(
                $this->taskId,
                'Date page already processing',
                [
                    'date' => $this->date,
                    'page' => $page,
                ],
                'crawl'
            );

            $tracker->jobFinished($task->id);

            return;
        }

        $startedAt = microtime(true);

        try {
            $logger->info($this->taskId, 'DATE CRAWL START', [
                'date' => $this->date,
                'page' => $page,
            ], 'crawl');

            $data = $service->crawlDailyPage(
                page: $page,
                date: $date
            );


            $items = $data['content'] ?? [];
            $totalPages = $data['total_pages'] ?? 0;
            $totalElements = $data['total_elements'] ?? 0;

            $currentApiTotal = (int) $task->api_total_items;

            // For range tasks we need to sum totals across each date producer.
            // Only the first page (page 0) contains the API's total count for that date,
            // so increment the task api_total_items atomically to avoid double-counting.
            if ($task->type === 'range') {
                if ($page === 0) {
                    CrawlTask::where('id', $task->id)
                        ->update([
                            'total_pages' => DB::raw(
                                "COALESCE(total_pages, 0) + {$totalPages}"
                            ),
                            'api_total_items' => DB::raw(
                                "COALESCE(api_total_items, 0) + {$totalElements}"
                            ),
                        ]);
                }
            } else {
                // For single-day or full-like date crawls, ensure we reflect increases
                // in the API's reported total elements into api_total_items.
                if ($totalElements > $currentApiTotal) {
                    CrawlTask::where('id', $task->id)
                        ->update([
                            'total_pages' => max(
                                (int) $task->total_pages,
                                $totalPages
                            ),
                            'api_total_items' => $totalElements,
                        ]);
                }
            }
            $logger->info($this->taskId, 'DAILY API META', [
                'date' => $this->date,
                'page' => $page,
                'total_pages' => $data['total_pages'] ?? null,
                'total_elements' => $data['total_elements'] ?? null,
                'items_in_page' => count($items),
            ], 'crawl');

            if (empty($items)) {

                $logger->info($this->taskId, 'DATE CRAWL FINISHED', [
                    'date' => $this->date,
                    'page' => $page,
                ], 'crawl');

                $tracker->markProducerDone($task->id);

                $tracker->jobFinished($task->id);

                return;
            }

            $tenders = $service->saveItems(
                $items
            );

            $itemCount = count($items);

            $task->update([
                'processed_pages' => DB::raw(
                    'processed_pages + 1'
                ),

                'processed_items' => DB::raw(
                    "processed_items + {$itemCount}"
                )
            ]);

            $duration = round(
                microtime(true) - $startedAt,
                3
            );

            $logger->info($this->taskId, 'DATE PAGE DONE', [
                'date' => $this->date,
                'page' => $page,
                'items' => $itemCount,
                'duration_seconds' => $duration,
                'items_per_second' => $duration > 0 ? round($itemCount / $duration, 2) : 0,
            ], 'crawl');

            $additional = 0;
            foreach ($tenders as $tender) {
                // detail job
                dispatch(new CrawlTenderDetailJob($tender->id, $this->taskId))->onQueue('detail');
                $tracker->jobDispatched($task->id);
                $additional += 1; // detail

                // sub resources
                if ((int) $tender->num_petition > 0) {
                    dispatch(new CrawlTenderSubResourceJob($tender->id, 'kn', $this->taskId))->onQueue('sub');
                    $tracker->jobDispatched($task->id);
                    $additional += 1;
                }

                if ((int) $tender->num_clarify_req > 0) {
                    dispatch(new CrawlTenderSubResourceJob($tender->id, 'yclr', $this->taskId))->onQueue('sub');
                    $tracker->jobDispatched($task->id);
                    $additional += 1;
                }

                // hntdt always
                dispatch(new CrawlTenderSubResourceJob($tender->id, 'hntdt', $this->taskId))->onQueue('sub');
                $tracker->jobDispatched($task->id);
                $additional += 1;
            }

            if ($additional > 0) {
                CrawlTask::where('id', $task->id)
                    ->update([
                        'total_items' => DB::raw("COALESCE(total_items, 0) + {$additional}"),
                    ]);
            }

            dispatch(
                new self(
                    date: $this->date,
                    page: $page + 1,
                    taskId: $this->taskId
                )
            )->onQueue('crawl');

            $tracker->jobDispatched($task->id);

            $tracker->jobFinished($task->id);
        } catch (\Throwable $e) {

            $logger->error($this->taskId, 'DATE CRAWL FAILED', [
                'date' => $this->date,
                'page' => $page,
                'message' => $e->getMessage(),
                'exception' => $e,
            ], 'crawl');

            // KHÔNG set status='failed' ở đây!
            // Để CrawlTracker::checkCompletion() quyết định final status dựa trên
            // failed_items vs processed_items.
            // Việc set status ở đây gây bug: nếu 1 date job fail trong range task
            // thì đánh dấu toàn bộ task là failed, và nếu incrementProcessedAndFailedIfNeeded()
            // trong failed() throw (deadlock), jobFinished() không được gọi → task treo running.

            throw $e;
        } finally {

            optional($lock)->release();
        }
    }

    public function failed(
        \Throwable $e
    ): void {

        $logger = app(CrawlLogger::class);

        // Ghi error log TRƯỚC để đảm bảo luôn có log dù DB/cache operations sau có throw
        $logger->error(
            $this->taskId,
            'CrawlTenderDateJob permanently failed',
            [
                'date' => $this->date,
                'page' => $this->page,
                'error' => $e->getMessage(),
                'exception' => $e,
            ],
            'crawl'
        );

        try {
            // increment both processed_items and failed_items atomically (like CrawlTenderDetailJob)
            $this->incrementProcessedAndFailedIfNeeded();
        } catch (\Throwable $ignored) {
            $logger->error($this->taskId, 'DateJob failed() - incrementProcessedAndFailedIfNeeded failed', [
                'error' => $ignored->getMessage(),
            ], 'crawl');
        }

        try {
            app(CrawlTracker::class)
                ->jobFinished(
                    $this->taskId
                );
        } catch (\Throwable $ignored) {
            $logger->error($this->taskId, 'DateJob failed() - jobFinished failed', [
                'error' => $ignored->getMessage(),
            ], 'crawl');
        }
    }

    private function incrementProcessedAndFailedIfNeeded(): void
    {
        DB::transaction(function () {
            $task = DB::table('crawl_tasks')
                ->where('id', $this->taskId)
                ->lockForUpdate()
                ->first();

            if (!$task) {
                return;
            }

            $processed = (int) ($task->processed_items ?? 0);
            $failed = (int) ($task->failed_items ?? 0);

            DB::table('crawl_tasks')
                ->where('id', $this->taskId)
                ->update([
                    'processed_items' => $processed + 1,
                    'failed_items' => $failed + 1,
                ]);
        });
    }
}
