<?php

namespace App\Domains\Notifications\Notifications;

use App\Domains\Notifications\Concerns\BroadcastsAsNotification;
use App\Domains\Notifications\Enums\NotificationType;
use App\Domains\Notifications\Support\NotificationChannels;
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
        return new BroadcastMessage([
            'title'       => 'Course Completed',
            'message'     => "Congratulations! You've completed \"{$this->courseTitle}\".",
            'type'        => NotificationType::COURSE->value,
            'link'        => env('FRONTEND_URL', 'http://localhost:3000') . '/library',
            'action_text' => 'View Certificate',
        ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title'       => 'Course Completed',
            'message'     => "Congratulations! You've completed \"{$this->courseTitle}\".",
            'type'        => NotificationType::COURSE->value,
            'course_id'   => $this->courseId,
            'link'        => env('FRONTEND_URL', 'http://localhost:3000') . '/library',
            'action_text' => 'View Certificate',
        ];
    }
}
