<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum','is_instructor','throttle:finance',])->prefix('finance')->group(function () {
        Route::get('/wallet',[FinanceController::class, 'wallet']);
        Route::get('/earnings',[FinanceController::class, 'earnings']);
        Route::get('/transactions',[FinanceController::class, 'transactions']);
        Route::post('/payout-request',[FinanceController::class, 'requestPayout']);
});