<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $unreadCount = Notification::unread()->count();

        $notifications = Notification::latest()
            ->limit(20)
            ->get()
            ->map(function ($n) {
                return [
                    'id' => $n->id,
                    'crawl_task_id' => $n->crawl_task_id,
                    'type' => $n->type,
                    'message' => $n->message,
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
