<?php

namespace App\Domains\Notifications\Notifications;

use App\Domains\Courses\Models\Course;
use App\Domains\Notifications\Concerns\BroadcastsAsNotification;
use App\Domains\Notifications\Enums\NotificationType;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class CourseApprovedNotification extends Notification
{
    use BroadcastsAsNotification;
    use Queueable;

    public function __construct(public readonly Course $course) {}

    public function via(object $notifiable): array
    {
        return \App\Domains\Notifications\Support\NotificationChannels::standard();
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title'       => 'Course Approved',
            'message'     => "Your course \"{$this->course->title}\" has been approved and is now live.",
            'type'        => NotificationType::COURSE->value,
            'link'        => env('FRONTEND_URL', 'http://localhost:3000') . '/courses/' . $this->course->slug,
            'action_text' => 'View Course',
        ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title'       => 'Course Approved',
            'message'     => "Your course \"{$this->course->title}\" has been approved and is now live.",
            'type'        => NotificationType::COURSE->value,
            'course_id'   => $this->course->id,
            'link'        => env('FRONTEND_URL', 'http://localhost:3000') . '/courses/' . $this->course->slug,
            'action_text' => 'View Course',
        ];
    }
}
