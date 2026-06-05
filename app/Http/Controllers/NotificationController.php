<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\CrawlTask;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $unreadCount = Notification::unread()->count();

        $notifications = Notification::with('crawlTask')
            ->latest()
            ->limit(20)
            ->get()
            ->map(function ($n) {
                $crawl = null;
                $message = $n->message;

                if ($n->crawlTask) {
                    $t = $n->crawlTask;

                    $from = $t->from_date ? $t->from_date->format('j/n/Y') : ($t->started_at ? $t->started_at->format('j/n/Y') : null);
                    $to = $t->to_date ? $t->to_date->format('j/n/Y') : null;

                    // For range type always show full range even if same date
                    if ($from && $to) {
                        $dateRange = $t->type === CrawlTask::TYPE_RANGE
                            ? "{$from} - {$to}"
                            : ($from === $to ? $from : "{$from} - {$to}");
                    } elseif ($from) {
                        $dateRange = $from;
                    } else {
                        $dateRange = null;
                    }

                    // For daily crawls, include time (hours:minutes:seconds) to distinguish multiple runs
                    if ($t->type === CrawlTask::TYPE_DAILY) {
                        if ($t->started_at) {
                            $dateRange = $t->started_at->format('j/n/Y H:i:s');
                        } elseif ($dateRange) {
                            // fallback: keep the date-only string
                            $dateRange = $dateRange;
                        }
                    }


                    // Human-friendly type labels (Vietnamese)
                    $typeLabels = [
                        CrawlTask::TYPE_DAILY => 'Daily',
                        CrawlTask::TYPE_RANGE => 'Range',
                        CrawlTask::TYPE_FULL => 'Full',
                    ];

                    $typeLabel = $typeLabels[$t->type] ?? ucfirst($t->type);

                    $crawl = [
                        'id' => $t->id,
                        'type' => $t->type,
                        'label' => $typeLabel,
                        'date_range' => $dateRange,
                        'from_raw' => $t->from_date ? $t->from_date->format('Y-m-d') : null,
                        'to_raw' => $t->to_date ? $t->to_date->format('Y-m-d') : null,
                        'duration' => (function () use ($t) {
                            if (!$t->started_at || !$t->finished_at) return '--';
                            $seconds = $t->started_at->diffInSeconds($t->finished_at);

                            if ($seconds < 60) {
                                return "{$seconds} giây";
                            }

                            $minutes = floor($seconds / 60);
                            $remaining = $seconds % 60;

                            if ($minutes < 60) {
                                return $remaining > 0 ? "{$minutes} phút {$remaining} giây" : "{$minutes} phút";
                            }

                            $hours = floor($minutes / 60);
                            $minutesRem = $minutes % 60;
                            return $minutesRem > 0 ? "{$hours} giờ {$minutesRem} phút" : "{$hours} giờ";
                        })(),
                        'total_items' => $t->processed_items ?? $t->total_items ?? null,
                        'failed_items' => (int) ($t->failed_items ?? 0),
                        'status' => $t->status ?? null,
                        'crawl_url' => url("/crawl-tasks/{$t->id}"),
                    ];

                    // Compose clearer message for list and toast
                    $message = $dateRange ? "Crawl {$typeLabel} — {$dateRange}" : "Crawl {$typeLabel}";
                }

                return [
                    'id' => $n->id,
                    'crawl_task_id' => $n->crawl_task_id,
                    'type' => $n->type,
                    'message' => $message,
                    'crawl' => $crawl,
                    'read' => $n->read_at !== null,
                    'created_at' => $n->created_at->diffForHumans(),
                    'created_raw' => $n->created_at->format('Y-m-d H:i:s'),
                ];
            });


        return response()->json([
            'unread_count' => $unreadCount,
            'notifications' => $notifications,
        ]);
    }

    public function markAsRead(Notification $notification): JsonResponse
    {
        $notification->markAsRead();
        return response()->json(['success' => true]);
    }

    public function markAllAsRead(): JsonResponse
    {
        Notification::unread()->update(['read_at' => now()]);
        return response()->json(['success' => true]);
    }
}
