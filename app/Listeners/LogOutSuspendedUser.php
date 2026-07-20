<?php

namespace App\Listeners;

use App\Domains\Users\Models\User;
use App\Exceptions\AccountSuspendedException;
use Illuminate\Auth\Events\Authenticated;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\Events\TokenAuthenticated;

class LogOutSuspendedUser
{
    /**
     * Session/cookie guards (web, and Sanctum's stateful SPA mode) fire this
     * on every request once a user is resolved.
     */
    public function handle(Authenticated $event): void
    {
        /** @var User $user */
        $user = $event->user;

        if ($user->status !== User::STATUS_SUSPENDED) {
            return;
        }

        $user->currentAccessToken()?->delete();

        Auth::guard($event->guard)->logout();

        $request = request();
        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        throw new AccountSuspendedException();
    }

    /**
     * Bearer-token clients (mobile/API) never fire Authenticated — Sanctum's
     * RequestGuard resolves the user without it — so they're covered here
     * instead, on every request authenticated via a personal access token.
     */
    public function handleTokenAuthenticated(TokenAuthenticated $event): void
    {
        $user = $event->token->tokenable;

        if (! $user instanceof User || $user->status !== User::STATUS_SUSPENDED) {
            return;
        }

        $event->token->delete();

        throw new AccountSuspendedException();
    }
}
