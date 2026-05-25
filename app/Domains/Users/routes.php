<?php

use Illuminate\Support\Facades\Route;
use App\Domains\Users\Controllers\ProfileController;
use App\Domains\Users\Controllers\InstructorVerificationController;
use App\Domains\Users\Controllers\NotificationController;

Route::middleware(['auth:sanctum', 'throttle:auth'])->prefix('users')->group(function () {
        Route::get('/me', [ProfileController::class, 'me']);
        Route::put('/profile', [ProfileController::class, 'update']);
        Route::prefix('instructor')->group(function () {
            Route::get('/application',[InstructorVerificationController::class, 'status']);
            Route::post('/apply',[InstructorVerificationController::class, 'store']);
        });
        Route::prefix('notifications')->group(function () {
            Route::get('/', [NotificationController::class, 'index']);
            Route::post('/read-all',[NotificationController::class, 'markAllAsRead']);
            Route::post('/{id}/read',[NotificationController::class, 'markAsRead']);
        });
    });
