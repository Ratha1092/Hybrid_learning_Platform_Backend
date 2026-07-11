<?php

namespace App\Domains\Notifications\Notifications;

use App\Domains\Notifications\Concerns\BroadcastsAsNotification;
use App\Domains\Notifications\Enums\NotificationType;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification as BaseNotification;

class NewInstructorVerificationNotification extends BaseNotification
{
    use BroadcastsAsNotification;
    use Queueable;

    public function __construct(
        public readonly int    $verificationId,
        public readonly string $userName,
    ) {}

    public function via(object $notifiable): array
    {
        return \App\Domains\Notifications\Support\NotificationChannels::standard();
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title'       => 'New Instructor Verification',
            'message'     => "{$this->userName} submitted a verification request.",
            'type'        => NotificationType::INSTRUCTOR_VERIFICATION->value,
            'link'        => "/admin/instructor-verifications/{$this->verificationId}",
            'action_text' => 'Review Application',
        ]);
    }

    public function toDatabase(object $notifiable): array
    {
        $url = "/admin/instructor-verifications/{$this->verificationId}";

        return [
            'title'    => 'New Instructor Verification',
            'message'  => "{$this->userName} submitted a verification request.",
            'format'   => 'filament',
            'duration' => 'persistent',
            'type'     => NotificationType::INSTRUCTOR_VERIFICATION->value,
            'actions'  => [
                [
                    'name'                 => 'view',
                    'label'                => 'Review Application',
                    'url'                  => $url,
                    'view'                 => 'filament-actions::link-action',
                    'shouldOpenUrlInNewTab' => false,
                    'alpineClickHandler'   => "window.location.href='{$url}'",
                ],
            ],
        ];
    }
}
