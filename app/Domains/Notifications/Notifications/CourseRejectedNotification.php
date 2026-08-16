<?php

namespace App\Domains\Notifications\Notifications;

use App\Domains\Courses\Models\Course;
use App\Domains\Notifications\Concerns\BroadcastsAsNotification;
use App\Domains\Notifications\Enums\NotificationType;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class CourseRejectedNotification extends Notification
{
    use BroadcastsAsNotification;
    use Queueable;

    public function __construct(
        public readonly Course $course,
        public readonly ?string $reason = null,
    ) {}

    public function via(object $notifiable): array
    {
        return \App\Domains\Notifications\Support\NotificationChannels::standard();
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        $message = "Your course \"{$this->course->title}\" was not approved.";
        if ($this->reason) {
            $message .= " Reason: {$this->reason}";
        }

        return new BroadcastMessage([
            'title'       => 'Course Rejected',
            'message'     => $message,
            'type'        => NotificationType::COURSE->value,
            'link'        => env('FRONTEND_URL', 'http://localhost:3000') . '/instructor/courses',
            'action_text' => 'Edit Course',
        ]);
    }

    public function toArray(object $notifiable): array
    {
        $message = "Your course \"{$this->course->title}\" was not approved.";

        if ($this->reason) {
            $message .= " Reason: {$this->reason}";
        }

        return [
            'title'       => 'Course Rejected',
            'message'     => $message,
            'type'        => NotificationType::COURSE->value,
            'course_id'   => $this->course->id,
            'link'        => env('FRONTEND_URL', 'http://localhost:3000') . '/instructor/courses',
            'action_text' => 'Edit Course',
        ];
    }
}
