<?php

namespace App\Observers;

use App\Models\CrawlTask;
use App\Models\Notification;

class CrawlTaskObserver
{
    public function updated(CrawlTask $task): void
    {
        if ($task->wasChanged('status') && in_array($task->status, ['completed', 'failed'])) {
            $message = $task->status === 'completed'
                ? "Crawl #{$task->id} hoàn thành"
                : "Crawl #{$task->id} thất bại" . ($task->error ? ': ' . $task->error : '');

            Notification::create([
                'crawl_task_id' => $task->id,
                'type' => $task->status,
                'message' => $message,
            ]);
        }
    }
}
