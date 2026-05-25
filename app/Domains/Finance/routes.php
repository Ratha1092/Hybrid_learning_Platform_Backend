<?php

use Illuminate\Support\Facades\Route;
use App\Domains\Finance\Controllers\FinanceController;

Route::middleware(['auth:sanctum','is_instructor','throttle:finance',])->prefix('finance')->group(function () {
        Route::get('/wallet',[FinanceController::class, 'wallet']);
        Route::get('/earnings',[FinanceController::class, 'earnings']);
        Route::get('/transactions',[FinanceController::class, 'transactions']);
        Route::post('/payout-request',[FinanceController::class, 'requestPayout']);
});