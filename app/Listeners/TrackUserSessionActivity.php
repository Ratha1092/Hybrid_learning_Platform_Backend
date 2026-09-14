<?php

namespace App\Listeners;

use App\Domains\Auth\Models\UserSession;
use Laravel\Sanctum\Events\TokenAuthenticated;

class TrackUserSessionActivity
{
    public function handle(TokenAuthenticated $event): void
    {
        UserSession::touchActivity($event->token);
    }
}
