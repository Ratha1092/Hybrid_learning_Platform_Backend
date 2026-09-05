<?php

namespace App\Domains\Notifications\Controllers;

use App\Http\Controllers\Controller;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $notifications = $user
            ->notifications()
            ->latest()
            ->paginate(20)
            ->through(
                fn ($notification) => (function () use ($notification) {
                    $data = $notification->data;
                    $action = $data['actions'][0] ?? [];

                    return [
                    'id' => $notification->id,
                    'title' => $data['title'] ?? '',
                    'message' => $data['message']
                        ?? $data['body']
                        ?? '',
                    'type' => $data['type'] ?? '',
                    'link' => $data['link']
                        ?? $data['action_url']
                        ?? $action['url']
                        ?? null,
                    'action_text' => $data['action_text']
                        ?? $action['label']
                        ?? null,
                    'receipt_id' => $data['receipt_id'] ?? null,
                    'read' => $notification->read_at !== null,
                    'created_at' => $notification->created_at,
                    ];
                })()
            );

        return ApiResponse::success([
            'notifications' => $notifications,
            'unread_count' => $user->unreadNotifications()->count(),
        ], 'Notifications retrieved successfully');
    }

    public function markAsRead(Request $request, string $id): JsonResponse
    {
        $request->user()
            ->notifications()
            ->findOrFail($id)
            ->markAsRead();

        return ApiResponse::success(null, 'Notification marked as read');
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        $request->user()
            ->unreadNotifications()
            ->update(['read_at' => now()]);

        return ApiResponse::success(null, 'All notifications marked as read');
    }
}
