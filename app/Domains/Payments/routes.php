<?php

use Illuminate\Support\Facades\Route;
use App\Domains\Payments\Controllers\PaymentController;

Route::middleware(['auth:sanctum','throttle:payments',])->prefix('payments')->group(function () {
        Route::get('/{payment}/status',[PaymentController::class, 'status']);
        Route::post('/verify',[PaymentController::class, 'verify']);
});