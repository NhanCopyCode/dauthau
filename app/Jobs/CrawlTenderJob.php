<?php

namespace App\Jobs;

use App\Models\CrawlTask;
use App\Services\CrawlTracker;
use App\Services\TenderCrawlerService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;



class CrawlTenderJob implements ShouldQueue
{
    use Queueable;
    public $tries = 5;
    public $timeout = 120;
    public $backoff = [10, 30, 60];

    public function __construct(
        protected int $page,
        protected int $taskId
    ) {}

    public function handle(
        TenderCrawlerService $service
    ): void {

        $tracker = app(CrawlTracker::class);

        $task = CrawlTask::findOrFail(
            $this->taskId
        );

        $page = $this->page;

        $initLock = Cache::lock(
            "crawl_init_lock:task_{$this->taskId}",
            10
        );

        try {

            if (
                $page === 0 &&
                !Cache::has(
                    "crawl_started_at:{$this->taskId}"
                )
            ) {

                $initLock->block(3);

                Cache::put(
                    "crawl_started_at:{$this->taskId}",
                    microtime(true)
                );

                Cache::put(
                    "crawl_total_items:{$this->taskId}",
                    0
                );

                Cache::put(
                    "crawl_total_pages:{$this->taskId}",
                    0
                );

                Log::info(
                    '============= CRAWL START =============',
                    [
                        'task_id' => $this->taskId
                    ]
                );
            }
        } finally {

            optional($initLock)->release();
        }

        $lockKey =
            "crawl_page_lock:task_{$this->taskId}_page_{$page}";

        $lock = Cache::lock(
            $lockKey,
            600
        );

        if (!$lock->get()) {

            Log::warning(
                "Page {$page} is already processing.",
                [
                    'task_id' => $this->taskId
                ]
            );

            $tracker->jobFinished($task->id);

            return;
        }

        $pageStartedAt = microtime(true);

        try {

            Log::info(
                'CRAWL PAGE START',
                [
                    'task_id' => $task->id,
                    'page' => $page,
                    'type' => $task->type,
                ]
            );

            $data = $service->crawlPage($page);

            $items =
                $data['content']
                ?? [];

            $totalPages =
                $data['total_pages']
                ?? 0;

            $totalElements =
                $data['total_elements']
                ?? 0;

            if ($page === 0) {

                $task->update([
                    'total_pages' => $totalPages,
                    'total_items' => $totalElements,
                ]);
            }
            $isLastPage =
                $page >= ($totalPages - 1);

            Log::info(
                'CRAWL API META',
                [
                    'task_id' => $task->id,
                    'page' => $page,
                    'total_pages' => $totalPages,
                    'total_elements' => $totalElements,
                    'current_items' => count($items),
                ]
            );

            if (empty($items)) {

                Log::warning(
                    'Empty items returned',
                    [
                        'task_id' => $task->id,
                        'page' => $page,
                    ]
                );

                if ($isLastPage) {

                    $tracker->markProducerDone($task->id);

                    $task->update([
                        'status' => 'completed',
                        'finished_at' => now(),
                    ]);
                }

                $tracker->jobFinished($task->id);

                return;
            }

            $tenders = $service->saveItems(
                $items
            );

            $itemCount = count($items);

            DB::table('crawl_tasks')
                ->where('id', $task->id)
                ->update([
                    'processed_pages' => DB::raw(
                        'processed_pages + 1'
                    ),

                    'processed_items' => DB::raw(
                        'processed_items + ' . (int) $itemCount
                    ),
                ]);

            Cache::increment(
                "crawl_total_items:{$this->taskId}",
                $itemCount
            );

            Cache::increment(
                "crawl_total_pages:{$this->taskId}"
            );

            $pageDuration = round(
                microtime(true) - $pageStartedAt,
                3
            );

            Log::info(
                'PAGE DONE',
                [
                    'task_id' => $task->id,
                    'page' => $page,
                    'items' => $itemCount,
                    'time_seconds' => $pageDuration,
                    'items_per_second' => $pageDuration > 0
                        ? round(
                            $itemCount / $pageDuration,
                            2
                        )
                        : 0,
                ]
            );

            foreach ($tenders as $tender) {

                dispatch(
                    new CrawlTenderDetailJob(
                        $tender->id,
                        $this->taskId
                    )
                )->onQueue('detail');

                $tracker->jobDispatched($task->id);

                if (
                    (int) $tender->num_petition > 0
                ) {

                    dispatch(
                        new CrawlTenderSubResourceJob(
                            $tender->id,
                            'kn',
                            $this->taskId
                        )
                    )->onQueue('sub');

                    $tracker->jobDispatched($task->id);
                }

                if (
                    (int) $tender->num_clarify_req > 0
                ) {

                    dispatch(
                        new CrawlTenderSubResourceJob(
                            $tender->id,
                            'yclr',
                            $this->taskId
                        )
                    )->onQueue('sub');

                    $tracker->jobDispatched($task->id);
                }

                dispatch(
                    new CrawlTenderSubResourceJob(
                        $tender->id,
                        'hntdt',
                        $this->taskId
                    )
                )->onQueue('sub');

                $tracker->jobDispatched($task->id);
            }

            if (!$isLastPage) {

                dispatch(
                    new self(
                        page: $page + 1,
                        taskId: $this->taskId
                    )
                )->onQueue('crawl');

                $tracker->jobDispatched($task->id);
            }

            if ($isLastPage) {

                Log::info(
                    'CRAWL FINISHED',
                    [
                        'task_id' => $task->id,
                        'page' => $page,
                        'total_pages' => $totalPages,
                        'total_elements' => $totalElements,
                    ]
                );

                $tracker->markProducerDone($task->id);
            }

            $tracker->jobFinished($task->id);
        } catch (\Throwable $e) {

            Log::error(
                "Crawl page {$page} failed",
                [
                    'task_id' => $this->taskId,
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]
            );

            $task->update([
                'status' => 'failed',
                'error' => $e->getMessage(),
            ]);

            throw $e;
        } finally {

            optional($lock)->release();
        }
    }
    public function failed(
        \Throwable $e
    ): void {

        CrawlTask::where(
            'id',
            $this->taskId
        )->update([

            'status' => 'failed',

            'error' => $e->getMessage(),
        ]);

        app(CrawlTracker::class)
            ->jobFinished(
                $this->taskId
            );

        Log::error(
            'CrawlTenderJob permanently failed',
            [

                'task_id' => $this->taskId,

                'page' => $this->page,

                'error' => $e->getMessage(),
            ]
        );
    }
}

