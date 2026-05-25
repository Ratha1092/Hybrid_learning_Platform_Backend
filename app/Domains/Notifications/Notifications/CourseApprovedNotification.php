<?php

namespace App\Domains\Notifications\Notifications;

use App\Domains\Courses\Models\Course;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CourseApprovedNotification extends Notification
{
    use Queueable;

    public function __construct(public readonly Course $course) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title'       => 'Course Approved',
            'message'     => "Your course \"{$this->course->title}\" has been approved and is now live.",
            'type'        => 'course_approved',
            'course_id'   => $this->course->id,
            'link'        => env('FRONTEND_URL', 'http://localhost:3000') . '/courses/' . $this->course->slug,
            'action_text' => 'View Course',
        ];
    }
}
