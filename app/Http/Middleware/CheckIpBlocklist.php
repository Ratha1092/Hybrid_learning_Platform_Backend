<?php

namespace App\Http\Middleware;

use App\Domains\System\Models\BlockedIp;
use App\Domains\System\Models\Setting;
use Closure;
use Illuminate\Http\Request;

/**
 * Blocks requests from IPs in the blocked_ips table when the
 * ip_whitelist_enabled setting is on. Named "whitelist" in the admin UI, but
 * the only IP-access-control data model that exists in this app is a
 * blacklist (App\Domains\System\Models\BlockedIp + its Filament CRUD page) —
 * there is no separate "allowed IPs" list, so this reuses that mechanism.
 */
class CheckIpBlocklist
{
    public function handle(Request $request, Closure $next)
    {
        if (!Setting::get('ip_whitelist_enabled', false)) {
            return $next($request);
        }

        if (BlockedIp::isBlocked($request->ip())) {
            abort(403, 'Access denied from this IP address.');
        }

        return $next($request);
    }
}
