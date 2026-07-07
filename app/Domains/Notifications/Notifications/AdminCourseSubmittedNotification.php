<?php

namespace App\Domains\Notifications\Notifications;

use App\Domains\Notifications\Concerns\BroadcastsAsNotification;
use App\Domains\Notifications\Enums\NotificationType;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class AdminCourseSubmittedNotification extends Notification
{
    use BroadcastsAsNotification;
    public function __construct(
        public readonly int    $courseId,
        public readonly string $courseTitle,
        public readonly string $instructorName,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title'       => 'New Course Submitted for Review',
            'message'     => "\"{$this->courseTitle}\" submitted by {$this->instructorName}.",
            'type'        => NotificationType::COURSE->value,
            'link'        => '/admin/courses',
            'action_text' => 'Review Course',
        ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'     => NotificationType::COURSE->value,
            'title'    => 'New Course Submitted for Review',
            'message'  => "\"{$this->courseTitle}\" was submitted by {$this->instructorName}.",
            'format'   => 'filament',
            'duration' => 'persistent',
            'actions'  => [
                [
                    'name'                 => 'view',
                    'label'                => 'Review Course',
                    'url'                  => '/admin/courses',
                    'view'                 => 'filament-actions::link-action',
                    'shouldOpenUrlInNewTab' => false,
                    'alpineClickHandler'   => "window.location.href='/admin/courses'",
                ],
            ],
        ];
    }
}
