<?php

use Illuminate\Support\Facades\Route;
use App\Domains\Users\Controllers\ProfileController;
use App\Domains\Users\Controllers\InstructorVerificationController;

Route::middleware(['auth:sanctum', 'throttle:auth'])->prefix('users')->group(function () {
        Route::get('/me', [ProfileController::class, 'me']);
        Route::put('/profile', [ProfileController::class, 'update']);
        Route::get('/courses', [ProfileController::class, 'enrolledCourses']);
        Route::get('/courses/{courseId}/enrollment-status', [ProfileController::class, 'checkEnrollment']);
        Route::prefix('instructor')->group(function () {
            Route::get('/application',[InstructorVerificationController::class, 'status']);
            Route::post('/apply', [InstructorVerificationController::class, 'store']);
        });
    });
