<?php

namespace App\Domains\Notifications\Notifications;

use App\Domains\Notifications\Concerns\BroadcastsAsNotification;
use App\Domains\Notifications\Enums\NotificationType;
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
        return new BroadcastMessage($this->payload($notifiable));
    }

    public function toArray(object $notifiable): array
    {
        return $this->payload($notifiable);
    }

    /** @return array<string, string> */
    private function payload(object $notifiable): array
    {
        return [
            'title'       => 'Account Role Updated',
            'message'     => $this->message(),
            'type'        => NotificationType::SYSTEM->value,
            'link'        => $this->link($notifiable),
            'action_text' => 'View Profile',
        ];
    }

    // Staff have no session on the student-facing frontend, so send them back
    // into the admin panel instead of a login wall on a site they can't use.
    private function link(object $notifiable): string
    {
        if (method_exists($notifiable, 'isStaff') && $notifiable->isStaff()) {
            return route('filament.admin.pages.users');
        }

        return env('FRONTEND_URL', 'http://localhost:3000') . '/profile';
    }
}
