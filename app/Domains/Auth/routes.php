<?php

use Illuminate\Support\Facades\Route;

use App\Domains\Auth\Controllers\AuthController;
use App\Domains\Auth\Controllers\EmailVerificationController;
use App\Domains\Auth\Controllers\PasswordResetController;
use App\Domains\Auth\Controllers\TwoFactorAuthController;
use App\Domains\Auth\Controllers\OAuthController;

Route::prefix('auth')->group(function () {

    //Public Authentication
    Route::post('/register',[AuthController::class, 'register'])->middleware('throttle:auth');
    Route::post('/login',[AuthController::class, 'login'])->middleware('throttle:login');

    //Password Reset
    Route::post('/forgot-password',[PasswordResetController::class, 'forgotPassword'])->middleware('throttle:auth');
    Route::post('/reset-password/verify',[PasswordResetController::class, 'verifyToken'])->middleware('throttle:auth');
    Route::post('/reset-password',[PasswordResetController::class, 'resetPassword'])->middleware('throttle:auth');

    //Email Verification
    Route::post('/email/verify',[EmailVerificationController::class, 'verify'])->middleware('throttle:auth');

    //Two Factor Authentication
    Route::post('/2fa/code',[TwoFactorAuthController::class, 'sendCode'])->middleware('throttle:auth');
    Route::post('/2fa/verify',[TwoFactorAuthController::class, 'verifyCode'])->middleware('throttle:auth');

    //OAuth
    Route::post('/oauth/google',[OAuthController::class, 'handleGoogleCallback'])->middleware('throttle:auth');
    Route::post('/oauth/github',[OAuthController::class, 'handleGithubCallback'])->middleware('throttle:auth');

    //Protected Routes
    Route::middleware(['auth:sanctum','throttle:auth',])->group(function () {
        Route::post('/logout',[AuthController::class, 'logout']
        );

        //Email Management
        Route::prefix('email')->group(function () {
            Route::post('/send',[EmailVerificationController::class, 'send']);
            Route::get('/status',[EmailVerificationController::class, 'status']);
        });

        //Two Factor Management
        Route::prefix('2fa')->group(function () {
            Route::post('/enable',[TwoFactorAuthController::class, 'enable']);
            Route::post('/verify-enable',[TwoFactorAuthController::class, 'verifyAndEnable']);
            Route::post('/disable',[TwoFactorAuthController::class, 'disable']);
            Route::get('/status',[TwoFactorAuthController::class, 'status']);
        });
        //OAuth Account Management
        Route::prefix('oauth')->group(function () {
            Route::get('/accounts',[OAuthController::class, 'linkedAccounts']
            );
            Route::post('/link',[OAuthController::class, 'linkOAuthAccount']
            );
            Route::delete('/{provider}',[OAuthController::class, 'unlinkOAuthAccount']
            );
        });
    });
});