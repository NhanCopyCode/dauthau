<?php

namespace App\Observers;

use App\Models\CrawlTask;
use App\Models\Notification;

class CrawlTaskObserver
{
    public function updated(CrawlTask $task): void
    {
        // Notify when task reaches a terminal status or a completed-with-errors state
        if ($task->wasChanged('status') && in_array($task->status, ['completed', 'failed', 'completed_with_errors'])) {
            $type = $task->status;

            // If task is marked completed but has failed items, treat as completed_with_errors
            if ($type === 'completed' && (int) ($task->failed_items ?? 0) > 0) {
                $type = 'completed_with_errors';
            }

            if ($type === 'completed_with_errors') {
                $message = "Crawl #{$task->id} hoàn tất (có lỗi) - failed: " . (int) ($task->failed_items ?? 0);
            } elseif ($type === 'completed') {
                $message = "Crawl #{$task->id} hoàn thành";
            } else { // failed
                $message = "Crawl #{$task->id} thất bại" . ($task->error ? ': ' . $task->error : '');
            }

            Notification::create([
                'crawl_task_id' => $task->id,
                'type' => $type,
                'message' => $message,
                'failed_items' => (int) ($task->failed_items ?? 0),
            ]);
        }
    }
}
