<?php

use App\Domains\Billing\Controllers\BillingAddressController;
use App\Domains\Billing\Controllers\InvoiceController;
use App\Domains\Billing\Controllers\ReceiptController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'throttle:auth'])
    ->prefix('billing')
    ->group(function () {

        Route::get('/invoices', [InvoiceController::class, 'index']);
        Route::get('/invoices/{id}', [InvoiceController::class, 'show']);
        Route::get('/invoices/{id}/download', [InvoiceController::class, 'download']);

        Route::get('/receipts', [ReceiptController::class, 'index']);
        Route::get('/receipts/{id}', [ReceiptController::class, 'show']);
        Route::get('/receipts/{id}/download', [ReceiptController::class, 'download']);

        Route::get('/addresses', [BillingAddressController::class, 'index']);
        Route::post('/addresses', [BillingAddressController::class, 'store']);
        Route::put('/addresses/{id}', [BillingAddressController::class, 'update']);
        Route::delete('/addresses/{id}', [BillingAddressController::class, 'destroy']);
        Route::post('/addresses/{id}/default', [BillingAddressController::class, 'setDefault']);
    });
