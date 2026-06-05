<?php

use Illuminate\Support\Facades\Route;
use App\Domains\Analytics\Controllers\StatsController;

Route::get('/stats', [StatsController::class, 'index']);

Route::middleware(['auth:sanctum', 'is_admin'])->prefix('analytics')->group(function () {
    // Route::get('/dashboard', [AnalyticsController::class, 'dashboard']);
    // Route::get('/metrics', [AnalyticsController::class, 'metrics']);
});
