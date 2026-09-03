<?php

namespace App\Domains\Courses\Observers;

use App\Domains\Courses\Models\Section;
use Illuminate\Support\Facades\Cache;

class SectionObserver
{
    public function saved(Section $section): void
    {
        $this->forgetCourseCache($section);
    }

    public function deleted(Section $section): void
    {
        $section->lessons()->each(fn ($lesson) => $lesson->delete());
        $this->forgetCourseCache($section);
    }

    public function restored(Section $section): void
    {
        $section->lessons()->onlyTrashed()->each(fn ($lesson) => $lesson->restore());
        $this->forgetCourseCache($section);
    }

    // See LessonObserver — the cached course payload in CourseController::show()
    // includes its sections, so renaming/reordering one has to bust it too.
    private function forgetCourseCache(Section $section): void
    {
        $slug = $section->course?->slug;

        if ($slug) {
            Cache::forget("courses.slug.{$slug}");
        }
    }
}
