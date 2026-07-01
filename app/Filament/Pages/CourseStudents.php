<?php

namespace App\Filament\Pages;

use App\Domains\Courses\Models\Course;
use App\Domains\Learning\Models\Enrollment;
use App\Support\PanelAccess;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class CourseStudents extends Page
{
    protected string $view = 'filament.pages.course-students';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'courses/{course}/students';

    public static function canAccess(): bool
    {
        return PanelAccess::can('courses.view');
    }

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return '';
    }

    public int $courseId;
    public string $search = '';
    public int $page = 1;

    public function mount(): void
    {
        $this->courseId = (int) request()->route('course');
    }

    public function updatedSearch(): void
    {
        $this->page = 1;
    }

    public function gotoPage(int $page): void
    {
        $this->page = max(1, $page);
    }

    public function suspendEnrollment(int $id): void
    {
        $enrollment = Enrollment::withoutGlobalScopes()->findOrFail($id);
        $enrollment->update(['status' => 'suspended']);

        Notification::make()
            ->title('Student suspended')
            ->body('The student has been suspended from this course.')
            ->warning()
            ->send();
    }

    public function unsuspendEnrollment(int $id): void
    {
        $enrollment = Enrollment::withoutGlobalScopes()->findOrFail($id);
        $enrollment->update(['status' => 'active']);

        Notification::make()
            ->title('Student restored')
            ->body('The student\'s enrollment has been restored to active.')
            ->success()
            ->send();
    }

    public function removeEnrollment(int $id): void
    {
        $enrollment = Enrollment::withoutGlobalScopes()->findOrFail($id);
        $enrollment->delete();

        Notification::make()
            ->title('Student removed')
            ->body('The student has been removed from this course.')
            ->danger()
            ->send();
    }

    protected function getViewData(): array
    {
        $course   = Course::withoutGlobalScopes()->findOrFail($this->courseId);

        $search  = $this->search;
        $page    = max(1, $this->page);
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
