<?php

namespace App\Domains\Notifications\Notifications;

use App\Domains\Notifications\Concerns\BroadcastsAsNotification;
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
        return ['database', 'broadcast'];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title'       => 'Application Rejected',
            'message'     => 'Your instructor application has been rejected. Reason: ' . $this->reason,
            'type'        => 'instructor_rejected',
            'link'        => null,
            'action_text' => null,
        ]);
    }
    public function toArray(object $notifiable): array
    {
        return [
            'title'       => 'Application Rejected',
            'message'     => 'Your instructor application has been rejected. Reason: ' . $this->reason,
            'type'        => 'instructor_rejected',
            'link'        => null,
            'action_text' => null,
        ];
    }
}
