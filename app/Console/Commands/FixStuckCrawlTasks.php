<?php

namespace App\Console\Commands;

use App\Models\CrawlTask;
use App\Services\CrawlTracker;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class FixStuckCrawlTasks extends Command
{
    protected $signature = 'crawl:fix-stuck
        {--dry-run : Only list stuck tasks, do not modify}
        {--status= : Only fix tasks with a specific status (running, completed_with_errors, failed)}';

    protected $description = 'Re-evaluate and fix crawl tasks stuck in a non-terminal state';

    public function handle(): int
    {
        $statusFilter = $this->option('status');
        $dryRun = $this->option('dry-run');

        $query = CrawlTask::query();

        if ($statusFilter) {
            $query->where('status', $statusFilter);
        } else {
            // Default: find tasks stuck in 'running' with no outstanding jobs
            $query->whereIn('status', [
                CrawlTask::STATUS_RUNNING,
                CrawlTask::STATUS_COMPLETED_WITH_ERRORS,
                CrawlTask::STATUS_FAILED,
            ]);
        }

        $tasks = $query->get();

        if ($tasks->isEmpty()) {
            $this->info('No stuck tasks found.');
            return 0;
        }

        $this->line("Found {$tasks->count()} task(s) to evaluate.");
        $fixed = 0;

        foreach ($tasks as $task) {
            $this->line("  Task #{$task->id} — status: {$task->status}, failed_items: {$task->failed_items}");

            if ($task->status === CrawlTask::STATUS_RUNNING) {
                // Check tracker state
                $outstanding = Cache::get("crawl_tracker:task_{$task->id}:outstanding", 0);
                $producerDone = Cache::get("crawl_tracker:task_{$task->id}:producer_done", false);
                $completedLogged = Cache::get("crawl_tracker:task_{$task->id}:completed_logged", false);

                $this->line("    Tracker: outstanding={$outstanding}, producer_done=" . ($producerDone ? 'true' : 'false') . ", completed_logged=" . ($completedLogged ? 'true' : 'false'));

                if ($producerDone && $outstanding <= 0) {
                    // Jobs are done but task wasn't marked as completed
                    $newStatus = CrawlTask::STATUS_COMPLETED;
                    if ((int) ($task->failed_items ?? 0) > 0) {
                        $newStatus = CrawlTask::STATUS_COMPLETED_WITH_ERRORS;
                    }

                    if ($dryRun) {
                        $this->warn("    Would fix: running → {$newStatus}");
                    } else {
                        $task->update([
                            'status' => $newStatus,
                            'finished_at' => $task->finished_at ?? now(),
                        ]);
                        Cache::put("crawl_tracker:task_{$task->id}:completed_logged", true);
                        $this->info("    Fixed: running → {$newStatus}");
                        $fixed++;
                    }
                } elseif (!$producerDone && $outstanding <= 0) {
                    // Producer never finished — something went wrong
                    if ($dryRun) {
                        $this->warn("    Would fix: producer never done — marking as completed_with_errors");
                    } else {
                        $task->update([
                            'status' => CrawlTask::STATUS_COMPLETED_WITH_ERRORS,
                            'finished_at' => $task->finished_at ?? now(),
                        ]);
                        $this->info("    Fixed: running → completed_with_errors (producer never done)");
                        $fixed++;
                    }
                } else {
                    $this->line("    Still has {$outstanding} outstanding job(s) — skipping");
                }
            } elseif (in_array($task->status, [CrawlTask::STATUS_COMPLETED_WITH_ERRORS, CrawlTask::STATUS_FAILED], true)) {
                // Tasks in terminal state that user wants to re-evaluate
                $this->line("    Terminal state — no automatic fix. Use retry instead.");
            }
        }

        if ($dryRun) {
            $this->warn("Dry-run mode: no changes were made. Re-run without --dry-run to apply fixes.");
        } else {
            $this->info("Fixed {$fixed} task(s).");
        }

        return 0;
    }
}
