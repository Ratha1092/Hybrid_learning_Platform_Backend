<?php

use Illuminate\Support\Facades\Route;
use App\Domains\Users\Controllers\ProfileController;
use App\Domains\Users\Controllers\InstructorVerificationController;

Route::middleware(['auth:sanctum','throttle:auth',])->prefix('users')->group(function () {
        Route::get('/me',[ProfileController::class, 'me']);
        Route::put('/profile',[ProfileController::class, 'update']);
        Route::post('/instructor/apply',[InstructorVerificationController::class, 'store']);

        Route::prefix('notifications')->group(function () {
            Route::get('/', function () {
                $user = auth()->user();

                $notifications = $user->notifications->map(fn($n) => [
                    'id'          => $n->id,
                    'title'       => $n->data['title'] ?? '',
                    'message'     => $n->data['message'] ?? '',
                    'type'        => $n->data['type'] ?? '',
                    'link'        => $n->data['link'] ?? null,
                    'action_text' => $n->data['action_text'] ?? null,
                    'read'        => !is_null($n->read_at),
                    'created_at'  => $n->created_at,
                ]);

                return response()->json([
                    'data'         => $notifications,
                    'unread_count' => $user->unreadNotifications()->count(),
                ]);
            });
            Route::post('/{id}/read', function (string $id) {
                auth()->user()->notifications()->findOrFail($id)->markAsRead();
                return response()->json(['message' => 'Marked as read']);
            });
            Route::post('/read-all', function () {
                auth()->user()->unreadNotifications->markAsRead();
                return response()->json(['message' => 'All marked as read']);
            });
        });
});