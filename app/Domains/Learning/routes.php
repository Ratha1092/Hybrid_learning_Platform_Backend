<?php

use Illuminate\Support\Facades\Route;
use App\Domains\Learning\Controllers\ContentReportController;
use App\Domains\Learning\Controllers\LessonCommentController;
use App\Domains\Learning\Controllers\LessonProgressController;

Route::middleware([
    'auth:sanctum',
    'throttle:learning',
])
    ->group(function () {
        Route::get('lessons/{lesson}/progress',[LessonProgressController::class, 'show']);
        Route::post('lessons/{lesson}/progress',[LessonProgressController::class, 'update']);
        Route::post('reports', [ContentReportController::class, 'store']);

        Route::post('lessons/{lesson}/comments', [LessonCommentController::class, 'store']);
        Route::post('comments/{comment}/like', [LessonCommentController::class, 'like']);
    });

// Public read (guests can view preview-lesson comments; LessonPolicy::view gates the rest)
Route::middleware(['optional_auth', 'throttle:learning'])
    ->get('lessons/{lesson}/comments', [LessonCommentController::class, 'index']);