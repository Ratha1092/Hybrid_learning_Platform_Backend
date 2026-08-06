<?php

namespace App\Http\Middleware;

use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful as SanctumEnsureFrontendRequestsAreStateful;

/**
 * Sanctum's own middleware hardcodes session.same_site to 'lax' on every
 * stateful request, unconditionally overwriting SESSION_SAME_SITE=none —
 * which breaks auth entirely when the SPA and API are on different
 * subdomains. This override respects the configured value instead.
 */
class EnsureFrontendRequestsAreStatefulWithSameSite extends SanctumEnsureFrontendRequestsAreStateful
{
    protected function configureSecureCookieSessions()
    {
        config([
            'session.http_only' => true,
            'session.same_site' => config('session.same_site', 'lax'),
        ]);
    }
}
