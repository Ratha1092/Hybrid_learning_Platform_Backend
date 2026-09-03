<?php

namespace App\Domains\Courses\Observers;

use App\Domains\Courses\Models\Lesson;
use Illuminate\Support\Facades\Cache;

class LessonObserver
{
    public function saved(Lesson $lesson): void
    {
        $this->forgetCourseCache($lesson);
    }

    public function deleted(Lesson $lesson): void
    {
        $lesson->attachments()->each(fn ($attachment) => $attachment->delete());
        $this->forgetCourseCache($lesson);
    }

    public function restored(Lesson $lesson): void
    {
        $lesson->attachments()->onlyTrashed()->each(fn ($attachment) => $attachment->restore());
        $this->forgetCourseCache($lesson);
    }

    // CourseController::show() caches the whole course — sections and lessons
    // included — for an hour. Course-level edits already bust that key (see
    // Course::booted), but editing a lesson didn't, so an instructor's change
    // to a title or description stayed invisible on the public course page
    // until the entry expired.
    private function forgetCourseCache(Lesson $lesson): void
    {
        $slug = $lesson->section?->course?->slug;

        if ($slug) {
            Cache::forget("courses.slug.{$slug}");
        }
    }
}
