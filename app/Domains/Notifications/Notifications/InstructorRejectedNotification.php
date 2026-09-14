<?php

namespace App\Domains\Notifications\Notifications;

use App\Domains\Notifications\Concerns\BroadcastsAsNotification;
use App\Domains\Notifications\Enums\NotificationType;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class InstructorRejectedNotification extends Notification
{
    use BroadcastsAsNotification;
    use Queueable;

    public function __construct(public string $reason) {}

    public function via(object $notifiable): array
    {
        return \App\Domains\Notifications\Support\NotificationChannels::standard();
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title'       => 'Application Rejected',
            'message'     => 'Your instructor application has been rejected. Reason: ' . $this->reason,
            'type'        => NotificationType::INSTRUCTOR_VERIFICATION->value,
            'link'        => null,
            'action_text' => null,
        ]);
    }
    public function toArray(object $notifiable): array
    {
        return [
            'title'       => 'Application Rejected',
            'message'     => 'Your instructor application has been rejected. Reason: ' . $this->reason,
            'type'        => NotificationType::INSTRUCTOR_VERIFICATION->value,
            'link'        => null,
            'action_text' => null,
        ];
    }
}
