<?php

use App\Domains\Promotions\Controllers\CouponController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'throttle:payments'])->post('/coupons/validate',
    [CouponController::class, 'validateCode']
);
