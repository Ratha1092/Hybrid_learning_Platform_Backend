<?php

use App\Domains\System\Controllers\SettingsController;
use App\Domains\System\Controllers\ContactController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:courses')->get('/settings/public', [SettingsController::class, 'public']);
Route::middleware('throttle:api')->post('/contact', [ContactController::class, 'store']);
