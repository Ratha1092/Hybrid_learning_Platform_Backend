<?php

namespace App\Http\Middleware;

use App\Domains\System\Models\Setting;
use Closure;
use Illuminate\Http\Request;

/**
 * Overrides config('session.lifetime') from the session_timeout setting.
 * Must run before Illuminate\Session\Middleware\StartSession, which reads
 * this config value when building the session handler for the request.
 */
class ApplySessionTimeoutSetting
{
    public function handle(Request $request, Closure $next)
    {
        $minutes = (int) Setting::get('session_timeout', config('session.lifetime'));

        if ($minutes > 0) {
            config(['session.lifetime' => $minutes]);
        }

        return $next($request);
    }
}
