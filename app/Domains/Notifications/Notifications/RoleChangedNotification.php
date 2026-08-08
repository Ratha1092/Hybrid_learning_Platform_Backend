<?php

namespace App\Domains\Notifications\Notifications;

use App\Domains\Notifications\Concerns\BroadcastsAsNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class RoleChangedNotification extends Notification
{
    use BroadcastsAsNotification;
    use Queueable;

    /** @param array<int, string> $newRoles */
    public function __construct(
        public readonly array $newRoles,
    ) {}

    public function via(object $notifiable): array
    {
        return \App\Domains\Notifications\Support\NotificationChannels::standard();
    }

    private function message(): string
    {
        $roles = implode(', ', $this->newRoles) ?: 'no role';

        return "Your account role has been updated to: {$roles}. Log out and log back in for the change to take effect.";
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title'       => 'Account Role Updated',
            'message'     => $this->message(),
            'type'        => 'role_changed',
            'link'        => env('FRONTEND_URL', 'http://localhost:3000') . '/profile',
            'action_text' => 'View Profile',
        ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title'       => 'Account Role Updated',
            'message'     => $this->message(),
            'type'        => 'role_changed',
            'link'        => env('FRONTEND_URL', 'http://localhost:3000') . '/profile',
            'action_text' => 'View Profile',
        ];
    }
}
