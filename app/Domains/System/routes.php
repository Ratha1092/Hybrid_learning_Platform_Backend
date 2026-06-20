<?php

use App\Domains\System\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:courses')->get('/settings/public', [SettingsController::class, 'public']);
