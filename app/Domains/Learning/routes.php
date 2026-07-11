<?php

use Illuminate\Support\Facades\Route;

use App\Domains\Learning\Controllers\ContentReportController;
use App\Domains\Learning\Controllers\LessonProgressController;

Route::middleware([
    'auth:sanctum',
    'throttle:learning',
])
    ->group(function () {
        Route::get('lessons/{lesson}/progress',[LessonProgressController::class, 'show']);
        Route::post('lessons/{lesson}/progress',[LessonProgressController::class, 'update']);
        Route::post('reports', [ContentReportController::class, 'store']);
    });