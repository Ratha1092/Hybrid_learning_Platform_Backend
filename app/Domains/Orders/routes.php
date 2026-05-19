<?php

use Illuminate\Support\Facades\Route;
use App\Domains\Orders\Controllers\OrderController;

Route::middleware(['auth:sanctum','throttle:payments',])->post('/checkout',[OrderController::class, 'store']);

Route::middleware(['auth:sanctum','throttle:payments',])->prefix('orders')->group(function () {
        Route::post('/',[OrderController::class, 'store']);
        Route::get('/{id}',[OrderController::class, 'show']);
        Route::get('/',[OrderController::class, 'index']);
});