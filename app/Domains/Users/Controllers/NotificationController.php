<?php

namespace App\Domains\Users\Controllers;

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
                fn ($notification) => [
                    'id' => $notification->id,
                    'title' => $notification->data['title'] ?? '',
                    'message' => $notification->data['message']
                        ?? $notification->data['body']
                        ?? '',
                    'type' => $notification->data['type'] ?? '',
                    'link' => $notification->data['link']
                        ?? $notification->data['action_url']
                        ?? null,
                    'action_text' => $notification->data['action_text'] ?? null,
                    'read' => $notification->read_at !== null,
                    'created_at' => $notification->created_at,
                ]
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
