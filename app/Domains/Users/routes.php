<?php

use Illuminate\Support\Facades\Route;
use App\Domains\Users\Controllers\ProfileController;
use App\Domains\Users\Controllers\InstructorVerificationController;
use App\Domains\Users\Controllers\InstructorListController;

Route::middleware('throttle:courses')->get('/instructors', [InstructorListController::class, 'index']);
Route::middleware(['auth:sanctum', 'throttle:profile'])
    ->prefix('users')
    ->group(function () {
        Route::get('/profile/dashboard', [ProfileController::class, 'dashboard']);
    });

Route::middleware(['auth:sanctum', 'throttle:auth'])->prefix('users')->group(function () {
        Route::get('/me', [ProfileController::class, 'me']);
        Route::put('/profile', [ProfileController::class, 'update']);
        Route::post('/avatar', [ProfileController::class, 'uploadAvatar']);
        Route::get('/courses', [ProfileController::class, 'enrolledCourses']);
        Route::get('/courses/{courseId}/enrollment-status', [ProfileController::class, 'checkEnrollment']);
        Route::prefix('instructor')->group(function () {
            Route::get('/application',[InstructorVerificationController::class, 'status']);
            Route::post('/apply', [InstructorVerificationController::class, 'store']);
        });
    });
