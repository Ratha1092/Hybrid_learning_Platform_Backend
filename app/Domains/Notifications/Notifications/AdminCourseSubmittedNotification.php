<?php

namespace App\Domains\Notifications\Notifications;

use App\Domains\Notifications\Concerns\BroadcastsAsNotification;
use App\Domains\Notifications\Enums\NotificationType;
use App\Filament\Resources\Courses\CourseResource;
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
        return \App\Domains\Notifications\Support\NotificationChannels::standard();
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        $courseUrl = CourseResource::getUrl('view', ['record' => $this->courseId]);

        return new BroadcastMessage([
            'title'       => 'New Course Submitted for Review',
            'message'     => "\"{$this->courseTitle}\" submitted by {$this->instructorName}.",
            'type'        => NotificationType::COURSE->value,
            'link'        => $courseUrl,
            'action_text' => 'Review Course',
        ]);
    }

    public function toArray(object $notifiable): array
    {
        $courseUrl = CourseResource::getUrl('view', ['record' => $this->courseId]);

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
                    'url'                  => $courseUrl,
                    'view'                 => 'filament-actions::link-action',
                    'shouldOpenUrlInNewTab' => false,
                    'alpineClickHandler'   => "window.location.href='{$courseUrl}'",
                ],
            ],
        ];
    }
}
