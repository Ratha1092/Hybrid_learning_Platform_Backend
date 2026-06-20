<?php

namespace App\Http\Middleware;

use App\Domains\System\Models\Setting;
use Closure;
use Illuminate\Http\Request;

/**
 * Blocks the public API while maintenance_mode is enabled. The admin panel (/admin)
 * is untouched — it's registered under a separate Filament route/middleware group,
 * so staff can always log in to flip this back off regardless of this check.
 */
class CheckMaintenanceMode
{
    public function handle(Request $request, Closure $next)
    {
        if (!Setting::get('maintenance_mode', false)) {
            return $next($request);
        }

        return response()->json([
            'success' => false,
            'message' => 'The platform is currently undergoing maintenance. Please check back shortly.',
        ], 503);
    }
}
