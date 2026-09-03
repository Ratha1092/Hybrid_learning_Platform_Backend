<?php

use Illuminate\Support\Facades\Route;

use App\Domains\Community\Controllers\CommunityPostController;

// Site-wide Community (any authenticated user, not scoped to a course)
Route::middleware(['auth:sanctum', 'throttle:community'])
    ->prefix('community')
    ->group(function () {
        Route::get('/posts', [CommunityPostController::class, 'index']);
        Route::post('/posts', [CommunityPostController::class, 'store']);
        Route::post('/posts/{post}/like', [CommunityPostController::class, 'like']);
        Route::delete('/posts/{post}', [CommunityPostController::class, 'destroy']);
        Route::post('/posts/{post}/report', [CommunityPostController::class, 'report']);
    });
