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

class CrawlTenderDateJob implements ShouldQueue
{
    use Queueable;

    public $tries = 5;

    public $timeout = 120;

    public $backoff = [10, 30, 60];

    public function __construct(
        protected string $date,
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

            Log::warning(
                'Date page already processing',
                [
                    'task_id' => $this->taskId,
                    'date' => $this->date,
                    'page' => $page,
                ]
            );

            $tracker->jobFinished($task->id);

            return;
        }

        $startedAt = microtime(true);

        try {
            Log::info('DATE CRAWL START', [

                'task_id' => $task->id,

                'date' => $this->date,

                'page' => $page,
            ]);

            $data = $service->crawlDailyPage(
                page: $page,
                date: $date
            );

            $items = $data['content'] ?? [];
            $totalPages = $data['total_pages'] ?? 0;
            $totalElements = $data['total_elements'] ?? 0;

            if ($page === 0) {

                CrawlTask::where(
                    'id',
                    $task->id
                )->update([

                    'total_pages' => DB::raw(
                        "COALESCE(total_pages, 0) + {$totalPages}"
                    ),

                    'total_items' => DB::raw(
                        "COALESCE(total_items, 0) + {$totalElements}"
                    ),
                ]);
            }
            Log::info('DAILY API META', [
                'task_id' => $this->taskId,

                'date' => $this->date,

                'page' => $page,

                'total_pages'
                => $data['total_pages'] ?? null,

                'total_elements'
                => $data['total_elements'] ?? null,

                'items_in_page'
                => count($items),
            ]);

            if (empty($items)) {

                Log::info('DATE CRAWL FINISHED', [

                    'task_id' => $task->id,

                    'date' => $this->date,

                    'page' => $page,
                ]);

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

            Log::info('DATE PAGE DONE', [

                'task_id' => $task->id,

                'date' => $this->date,

                'page' => $page,

                'items' => $itemCount,

                'duration_seconds' => $duration,

                'items_per_second' => $duration > 0
                    ? round(
                        $itemCount / $duration,
                        2
                    )
                    : 0,
            ]);

            foreach ($tenders as $tender) {

                dispatch(
                    new CrawlTenderDetailJob(
                        $tender->id,
                        $this->taskId
                    )
                )->onQueue('detail');

                $tracker->jobDispatched($task->id);

                if ((int) $tender->num_petition > 0) {

                    dispatch(
                        new CrawlTenderSubResourceJob(
                            $tender->id,
                            'kn',
                            $this->taskId
                        )
                    )->onQueue('sub');

                    $tracker->jobDispatched($task->id);
                }


                if ((int) $tender->num_clarify_req > 0) {

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

            Log::error('DATE CRAWL FAILED', [

                'task_id' => $this->taskId,

                'date' => $this->date,

                'page' => $page,

                'message' => $e->getMessage(),
            ]);

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

            'finished_at' => now(),
        ]);

        app(CrawlTracker::class)
            ->jobFinished(
                $this->taskId
            );

        Log::error(
            'CrawlTenderDateJob permanently failed',
            [

                'task_id' => $this->taskId,

                'date' => $this->date,

                'page' => $this->page,

                'error' => $e->getMessage(),
            ]
        );
    }
}
