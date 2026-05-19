<?php

use Illuminate\Support\Facades\Route;
use App\Domains\Users\Controllers\ProfileController;
use App\Domains\Users\Controllers\InstructorVerificationController;

Route::middleware(['auth:sanctum','throttle:auth',])->prefix('users')->group(function () {
        Route::get('/me',[ProfileController::class, 'me']);
        Route::put('/profile',[ProfileController::class, 'update']);
        Route::post('/instructor/apply',[InstructorVerificationController::class, 'store']);
});