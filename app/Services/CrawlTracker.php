<?php

namespace App\Services;

use App\Models\CrawlTask;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;


class CrawlTracker
{
    protected string $prefix = 'crawl_tracker';

    public function start(int $taskId): void
    {
        Cache::put(
            $this->key($taskId, 'started_at'),
            microtime(true)
        );

        Cache::put(
            $this->key($taskId, 'outstanding'),
            0
        );

        Cache::put(
            $this->key($taskId, 'producer_done'),
            false
        );

        Cache::put(
            $this->key($taskId, 'completed_logged'),
            false
        );

        Log::info(
            'CRAWL TRACKER STARTED',
            [
                'task_id' => $taskId
            ]
        );
    }

    public function jobDispatched(
        int $taskId
    ): void {

        Cache::increment(
            $this->key(
                $taskId,
                'outstanding'
            )
        );
    }

    public function jobFinished(
        int $taskId
    ): void {

        Cache::decrement(
            $this->key(
                $taskId,
                'outstanding'
            )
        );

        $this->checkCompletion(
            $taskId
        );
    }

    public function markProducerDone(
        int $taskId
    ): void {

        Cache::put(
            $this->key(
                $taskId,
                'producer_done'
            ),
            true
        );

        $this->checkCompletion(
            $taskId
        );
    }

    protected function checkCompletion(
        int $taskId
    ): void {

        $lock = Cache::lock(
            $this->key(
                $taskId,
                'completion_lock'
            ),
            10
        );

        try {

            if (!$lock->get()) {
                return;
            }

            if (
                Cache::get(
                    $this->key(
                        $taskId,
                        'completed_logged'
                    )
                )
            ) {
                return;
            }

            $outstanding = Cache::get(
                $this->key(
                    $taskId,
                    'outstanding'
                ),
                0
            );

            $producerDone = Cache::get(
                $this->key(
                    $taskId,
                    'producer_done'
                ),
                false
            );

            if (
                $producerDone &&
                $outstanding <= 0
            ) {

                Cache::put(
                    $this->key(
                        $taskId,
                        'completed_logged'
                    ),
                    true
                );

                $duration = round(
                    microtime(true)
                        - Cache::get(
                            $this->key(
                                $taskId,
                                'started_at'
                            )
                        ),
                    2
                );

                CrawlTask::where(
                    'id',
                    $taskId
                )->update([
                    'status' => 'completed',
                    'finished_at' => now(),
                ]);

                Log::info(
                    '===================='
                );

                Log::info(
                    'CRAWL FULLY COMPLETED',
                    [
                        'task_id' => $taskId,
                    ]
                );

                Log::info(
                    'REAL DURATION',
                    [
                        'seconds' => $duration
                    ]
                );

                Log::info(
                    '===================='
                );

                $this->cleanup(
                    $taskId
                );
            }
        } finally {

            optional($lock)
                ->release();
        }
    }

    protected function cleanup(
        int $taskId
    ): void {

        Cache::forget(
            $this->key(
                $taskId,
                'started_at'
            )
        );

        Cache::forget(
            $this->key(
                $taskId,
                'outstanding'
            )
        );

        Cache::forget(
            $this->key(
                $taskId,
                'producer_done'
            )
        );

        Cache::forget(
            $this->key(
                $taskId,
                'completed_logged'
            )
        );
    }

    protected function key(
        int $taskId,
        string $key
    ): string {

        return sprintf(
            '%s:task_%s:%s',
            $this->prefix,
            $taskId,
            $key
        );
    }
}
