<?php

use Illuminate\Support\Facades\Route;
use App\Domains\Finance\Controllers\FinanceController;
use App\Domains\Finance\Controllers\PayoutAccountController;
use App\Domains\Finance\Controllers\PayoutReceiptController;

Route::middleware(['auth:sanctum', 'is_instructor', 'throttle:finance'])
    ->prefix('finance')
    ->group(function () {
        Route::get('/wallet', [FinanceController::class, 'wallet']);
        Route::get('/earnings', [FinanceController::class, 'earnings']);
        Route::get('/transactions', [FinanceController::class, 'transactions']);
        Route::post('/payout-request', [FinanceController::class, 'requestPayout']);
        Route::get('/payout-requests', [FinanceController::class, 'payoutRequests']);
        Route::get('/payout-requests/{id}', [FinanceController::class, 'payoutRequest']);
        Route::get('/payout-account', [PayoutAccountController::class, 'show']);
        Route::post('/payout-account', [PayoutAccountController::class, 'store']);
        Route::get('/payout-receipts', [PayoutReceiptController::class, 'index']);
        Route::get('/payout-receipts/{id}', [PayoutReceiptController::class, 'show']);
        Route::get('/payout-receipts/{id}/download', [PayoutReceiptController::class, 'download']);
    });
