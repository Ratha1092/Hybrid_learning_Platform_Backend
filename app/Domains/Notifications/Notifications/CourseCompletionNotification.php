<?php

namespace App\Domains\Notifications\Notifications;

use App\Domains\Notifications\Concerns\BroadcastsAsNotification;
use App\Domains\Notifications\Enums\NotificationType;
use App\Domains\Notifications\Support\NotificationChannels;
use App\Domains\Notifications\Support\NotificationLinks;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class CourseCompletionNotification extends Notification
{
    use BroadcastsAsNotification;
    use Queueable;

    public function __construct(
        public readonly int $courseId,
        public readonly string $courseTitle,
    ) {}

    public function via(object $notifiable): array
    {
        return NotificationChannels::standard();
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        $link = NotificationLinks::frontend('/library');

        return new BroadcastMessage([
            'title'       => 'Course Completed',
            'message'     => "Congratulations! You've completed \"{$this->courseTitle}\".",
            'type'        => NotificationType::COURSE->value,
            'link'        => $link,
            'action_text' => 'View in Library',
        ]);
    }

    public function toArray(object $notifiable): array
    {
        $link = NotificationLinks::frontend('/library');

        return [
            'title'       => 'Course Completed',
            'message'     => "Congratulations! You've completed \"{$this->courseTitle}\".",
            'type'        => NotificationType::COURSE->value,
            'course_id'   => $this->courseId,
            'link'        => $link,
            'action_text' => 'View in Library',
        ];
    }
}
