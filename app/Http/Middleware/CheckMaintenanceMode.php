<?php

namespace App\Http\Middleware;

use App\Domains\System\Models\Setting;
use Closure;
use Illuminate\Http\Request;

/**
 * Blocks customer-facing API access while maintenance_mode is enabled.
 *
 * Staff API requests may continue, and the Filament admin panel is untouched so
 * staff can always log in and turn maintenance mode back off.
 */
class CheckMaintenanceMode
{
    public function handle(Request $request, Closure $next)
    {
        if (!Setting::get('maintenance_mode', false)) {
            return $next($request);
        }

        if ($this->isRecoveryEndpoint($request) || $this->isStaffRequest($request)) {
            return $next($request);
        }

        return response()->json([
            'success' => false,
            'message' => 'The platform is currently undergoing maintenance. Please check back shortly.',
        ], 503);
    }

    private function isStaffRequest(Request $request): bool
    {
        $user = auth('sanctum')->user() ?? auth('web')->user();

        return $user?->isStaff() ?? false;
    }

    private function isRecoveryEndpoint(Request $request): bool
    {
        return $request->is(
            'api/v1/auth/login',
            'api/v1/auth/2fa/code',
            'api/v1/auth/2fa/verify',
            'api/v1/auth/oauth/google',
            'api/v1/auth/oauth/github',
            'api/v1/auth/forgot-password',
            'api/v1/auth/reset-password',
            'api/v1/auth/reset-password/verify',
            'api/v1/settings/public',
        );
    }
}
