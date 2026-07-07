<?php

namespace App\Domains\Notifications\Concerns;

trait BroadcastsAsNotification
{
    public function broadcastAs(): string
    {
        return 'notification.received';
    }
}
