<?php

namespace App\Filament\Pages;

use App\Domains\Courses\Models\Course;
use App\Domains\Learning\Models\Enrollment;
use BackedEnum;
use Filament\Pages\Page;

class CourseStudents extends Page
{
    protected string $view = 'filament.pages.course-students';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'courses/{course}/students';

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return '';
    }

    protected function getViewData(): array
    {
        $courseId = request()->route('course');
        $course   = Course::withoutGlobalScopes()->findOrFail($courseId);

        $search  = request('search', '');
        $page    = max(1, (int) request('page', 1));
        $perPage = 15;

        $query = Enrollment::with('user:id,name,email,avatar')
            ->where('course_id', $course->id)
            ->withoutTrashed();

        if ($search) {
            $query->whereHas('user', fn($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%"));
        }

        $query->orderBy('enrolled_at', 'desc');

        $total       = $query->count();
        $totalPages  = max(1, (int) ceil($total / $perPage));
        $curPage     = min($page, $totalPages);
        $enrollments = $query->skip(($curPage - 1) * $perPage)->take($perPage)->get();

        return compact('course', 'enrollments', 'search', 'total', 'totalPages', 'curPage', 'perPage');
    }
}
