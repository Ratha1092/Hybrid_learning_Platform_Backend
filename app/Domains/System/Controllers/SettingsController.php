<?php

namespace App\Domains\System\Controllers;

use App\Domains\System\Models\Setting;
use App\Http\Controllers\Controller;
use App\Support\ApiResponse;
use Illuminate\Support\Facades\Cache;

class SettingsController extends Controller
{
    public function public()
    {
        $settings = Cache::remember('settings.public', now()->addMinutes(5), fn () => Setting::allPublic());

        return ApiResponse::success($settings, 'Public settings retrieved successfully');
    }
}
