<?php

use Illuminate\Support\Facades\Route;

// Placeholder for Analytics routes - add controllers as needed
Route::middleware(['auth:sanctum', 'is_admin'])->prefix('analytics')->group(function () {
    // Route::get('/dashboard', [AnalyticsController::class, 'dashboard']);
    // Route::get('/metrics', [AnalyticsController::class, 'metrics']);
});
