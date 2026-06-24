<?php

namespace App\Domains\Notifications\Notifications;

use App\Domains\Notifications\Enums\NotificationType;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification as BaseNotification;

class NewInstructorVerificationNotification extends BaseNotification
{
    use Queueable;

    public function __construct(
        public readonly int    $verificationId,
        public readonly string $userName,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $url = "/admin/instructor-verifications/{$this->verificationId}";

        return [
            'title'    => 'New Instructor Verification',
            'body'     => "{$this->userName} submitted a verification request.",
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
