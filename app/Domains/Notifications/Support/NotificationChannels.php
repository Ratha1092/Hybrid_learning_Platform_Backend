<?php

namespace App\Domains\Notifications\Support;

use App\Domains\System\Models\Setting;

/**
 * Shared via() channel list for every notification in this domain — the
 * 'broadcast' channel is what "push" means in this app (see
 * app/Domains/Notifications/Notifications/*.php, all real-time delivery goes
 * through Pusher broadcasting, there is no separate FCM/web-push mechanism).
 * Wiring the push_notifications_enabled setting here means every existing
 * notification class respects it without editing each one's via() logic.
 */
class NotificationChannels
{
    public static function standard(): array
    {
        return Setting::get('push_notifications_enabled', true)
            ? ['database', 'broadcast']
            : ['database'];
    }
}
