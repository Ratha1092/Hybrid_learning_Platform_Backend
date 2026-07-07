<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Register the channels that the application should listen on.
| Each channel is guarded so only the authenticated owner can subscribe.
|
*/

// Private per-user channel — students, instructors, admins all use this.
// Echo: Echo.private(`user.${userId}`)
Broadcast::channel('user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});
