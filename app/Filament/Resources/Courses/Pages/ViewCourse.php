<?php

namespace App\Filament\Resources\Courses\Pages;

use App\Domains\Courses\Models\Course;
use App\Domains\Notifications\Notifications\CourseApprovedNotification;
use App\Domains\Notifications\Notifications\CourseRejectedNotification;
use App\Filament\Resources\Courses\CourseResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewCourse extends ViewRecord
{
    protected static string $resource = CourseResource::class;
    protected string $view = 'filament.resources.courses.view-course';

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function approveCourse(): void
    {
        /** @var Course $course */
        $course = $this->record;
        if (! $course->isPendingReview()) return;

        $course->publish(auth()->id());
        $course->instructor?->notify(new CourseApprovedNotification($course));
        $this->record = $course->refresh();

        Notification::make()->title('Course Approved & Published')
            ->body("\"{$course->title}\" is now live.")
            ->success()->send();
    }

    public function rejectCourse(string $reason): void
    {
        $reason = trim($reason);
        if ($reason === '') {
            Notification::make()->title('Rejection reason is required.')->danger()->send();
            return;
        }

        /** @var Course $course */
        $course = $this->record;
        if (! $course->isPendingReview()) return;

        $course->reject($reason);
        $course->instructor?->notify(new CourseRejectedNotification($course, $reason));
        $this->record = $course->refresh();

        Notification::make()->title('Course Rejected')->body('Instructor notified.')->danger()->send();
    }

    public function archiveCourse(): void
    {
        /** @var Course $course */
        $course = $this->record;
        if (! $course->isPublished()) return;

        $course->archive();
        $this->record = $course->refresh();

        Notification::make()->title('Course Archived')->warning()->send();
    }
}
