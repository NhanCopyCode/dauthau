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

        Cache::put(
            $this->key($taskId, 'previous_success'),
            0
        );

        Log::info(
            'CRAWL TRACKER STARTED',
            [
                'task_id' => $taskId
            ]
        );
    }

    public function setPreviousSuccessCount(
        int $taskId,
        int $count
    ): void {
        Cache::put(
            $this->key($taskId, 'previous_success'),
            $count
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

        // ── No Cache::lock here ────────────────────────────────────
        // Cache locks are unreliable with the `database` driver (GET_LOCK
        // can silently fail under concurrent queue workers). Instead we
        // use the `completed_logged` flag + task DB status as guards.
        // This is safe because the final DB update is idempotent.

        // Fast-path: completed_logged already true → verify task state
        $alreadyLogged = Cache::get(
            $this->key($taskId, 'completed_logged'),
            false
        );
        if ($alreadyLogged) {
            $taskCheck = CrawlTask::find($taskId);
            if ($taskCheck && !in_array($taskCheck->status, [
                CrawlTask::STATUS_COMPLETED,
                CrawlTask::STATUS_COMPLETED_WITH_ERRORS,
                CrawlTask::STATUS_FAILED,
            ], true)) {
                // Task was reset to a non-terminal state → allow re-completion
                Cache::put($this->key($taskId, 'completed_logged'), false);
            } else {
                return; // Already properly completed
            }
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

            // Double-check completed_logged again (race-safe after our reset above)
            if (Cache::get($this->key($taskId, 'completed_logged'), false)) {
                return;
            }

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

            $taskModel = CrawlTask::find($taskId);
            $status = CrawlTask::STATUS_COMPLETED;

            if ($taskModel) {
                $failedItems = (int) ($taskModel->failed_items ?? 0);
                $processedItems = (int) ($taskModel->processed_items ?? 0);
                $previousSuccess = (int) Cache::get(
                    $this->key($taskId, 'previous_success'),
                    0
                );

                if ($failedItems > 0) {
                    // When a retry resets processed_items → 0 and failed_items → 0,
                    // we lose track of items that succeeded in earlier runs.
                    // previous_success preserves that count so we can distinguish:
                    //   - truly all-failed (never any success)       → STATUS_FAILED
                    //   - some earlier successes + retry failures   → COMPLETED_WITH_ERRORS
                    $allFailed = ($previousSuccess === 0) && ($processedItems === $failedItems);
                    $status = $allFailed
                        ? CrawlTask::STATUS_FAILED
                        : CrawlTask::STATUS_COMPLETED_WITH_ERRORS;
                }
            }

            $taskModel?->update([
                'status' => $status,
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
