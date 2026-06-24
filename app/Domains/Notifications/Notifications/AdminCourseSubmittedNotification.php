<?php

namespace App\Domains\Notifications\Notifications;

use Illuminate\Notifications\Notification;

class AdminCourseSubmittedNotification extends Notification
{
    public function __construct(
        public readonly int    $courseId,
        public readonly string $courseTitle,
        public readonly string $instructorName,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title'    => 'New Course Submitted for Review',
            'body'     => "\"{$this->courseTitle}\" was submitted by {$this->instructorName}.",
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
