<?php

namespace App\Http\Middleware;

use App\Domains\System\Models\Setting;
use Closure;
use Illuminate\Http\Request;

/**
 * Redirects to HTTPS when the force_https setting is enabled. Skipped in
 * local/testing so it never breaks local development over plain HTTP.
 */
class ForceHttps
{
    public function handle(Request $request, Closure $next)
    {
        if (
            $request->secure()
            || app()->environment('local', 'testing')
            || !Setting::get('force_https', false)
        ) {
            return $next($request);
        }

        return redirect()->secure($request->getRequestUri());
    }
}
