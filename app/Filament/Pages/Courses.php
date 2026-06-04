<?php

namespace App\Filament\Pages;

use App\Domains\Courses\Models\Course;
use App\Domains\Notifications\Notifications\CourseApprovedNotification;
use App\Domains\Notifications\Notifications\CourseRejectedNotification;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
class Courses extends Page
{
    protected string $view = 'filament.pages.courses';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationLabel = 'Courses';
    protected static string|\UnitEnum|null $navigationGroup = 'Learning';
    protected static ?int $navigationSort = 1;
    protected static ?string $slug = 'courses';

    public string $activeTab   = 'all';
    public string $search      = '';
    public int    $currentPage = 1;
    public int    $perPage     = 10;

    public function mount(): void
    {
        $this->activeTab   = request('tab', 'all');
        $this->search      = request('search', '');
        $this->currentPage = max(1, (int) request('page', 1));
        $this->perPage     = in_array((int) request('per_page', 10), [10, 25, 50])
            ? (int) request('per_page', 10) : 10;
    }

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return '';
    }

    // ── Row actions ───────────────────────────────────────────────────────

    public function setTab(string $tab): void
    {
        $this->activeTab   = $tab;
        $this->currentPage = 1;
    }

    public function setPage(int $page): void
    {
        $this->currentPage = $page;
    }

    public function setPerPage(int $perPage): void
    {
        $this->perPage     = $perPage;
        $this->currentPage = 1;
    }

    public function approveCourse(int $id): void
    {
        $course = Course::withoutGlobalScopes()->findOrFail($id);
        if (!$course->isPendingReview()) return;

        $course->publish(auth()->id());
        $course->instructor?->notify(new CourseApprovedNotification($course));
        Notification::make()->title('Course Approved')->body("\"{$course->title}\" is now live.")->success()->send();
    }

    public function rejectCourse(int $id, string $reason): void
    {
        $course = Course::withoutGlobalScopes()->findOrFail($id);
        if (!$course->isPendingReview()) return;

        $reason = trim($reason);
        if ($reason === '') {
            Notification::make()->title('Rejection reason is required.')->danger()->send();
            return;
        }

        $course->reject($reason);
        $course->instructor?->notify(new CourseRejectedNotification($course, $reason));
        Notification::make()->title('Course Rejected')->body('Instructor notified.')->danger()->send();
    }

    public function archiveCourse(int $id): void
    {
        $course = Course::withoutGlobalScopes()->findOrFail($id);
        if (!$course->isPublished()) return;

        $course->archive();
        Notification::make()->title('Course Archived')->warning()->send();
    }

    public function returnToDraft(int $id): void
    {
        $course = Course::withoutGlobalScopes()->findOrFail($id);
        $course->update([
            'status'           => Course::STATUS_DRAFT,
            'is_published'     => false,
            'approved_at'      => null,
            'approved_by'      => null,
            'rejection_reason' => null,
        ]);
        Notification::make()->title('Returned to Draft')->success()->send();
    }

    public function exportCsv(): void
    {
        $statusMap = [
            'pending'   => Course::STATUS_PENDING,
            'published' => Course::STATUS_PUBLISHED,
            'draft'     => Course::STATUS_DRAFT,
            'rejected'  => Course::STATUS_REJECTED,
            'archived'  => Course::STATUS_ARCHIVED,
        ];

        $query = Course::withoutGlobalScopes()
            ->with(['instructor:id,name', 'category:id,name'])
            ->withCount('enrollments');

        $tab    = $this->activeTab;
        $search = $this->search;

        if ($tab !== 'all' && isset($statusMap[$tab])) {
            $query->where('status', $statusMap[$tab]);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%")
                  ->orWhereHas('instructor', fn($q2) => $q2->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('category',   fn($q2) => $q2->where('name', 'like', "%{$search}%"));
            });
        }

        $rows = [['ID', 'Title', 'Instructor', 'Category', 'Price', 'Status', 'Students', 'Created']];

        foreach ($query->orderBy('id', 'desc')->get() as $course) {
            $rows[] = [
                $course->id,
                $course->title,
                $course->instructor?->name ?? '',
                $course->category?->name ?? '',
                number_format($course->price, 2),
                $course->status,
                $course->enrollments_count,
                $course->created_at?->format('M d, Y') ?? '',
            ];
        }

        $csv = implode("\n", array_map(
            fn($row) => implode(',', array_map(fn($v) => '"' . str_replace('"', '""', (string) $v) . '"', $row)),
            $rows
        ));

        $this->dispatch('download-csv',
            content:  $csv,
            filename: 'courses-' . now()->format('Y-m-d') . '.csv',
        );
    }

    // ── View data ─────────────────────────────────────────────────────────

    protected function getViewData(): array
    {
        $tab     = $this->activeTab;
        $search  = $this->search;
        $page    = max(1, $this->currentPage);
        $perPage = in_array($this->perPage, [10, 25, 50]) ? $this->perPage : 10;

        $statusMap = [
            'pending'   => Course::STATUS_PENDING,
            'published' => Course::STATUS_PUBLISHED,
            'draft'     => Course::STATUS_DRAFT,
            'rejected'  => Course::STATUS_REJECTED,
            'archived'  => Course::STATUS_ARCHIVED,
        ];

        $tabs = [
            ['key' => 'all',       'label' => 'All',            'count' => Course::withoutGlobalScopes()->count(),                                                'color' => null],
            ['key' => 'pending',   'label' => 'Pending Review', 'count' => Course::withoutGlobalScopes()->where('status', Course::STATUS_PENDING)->count(),       'color' => '#fbbf24'],
            ['key' => 'published', 'label' => 'Published',      'count' => Course::withoutGlobalScopes()->where('status', Course::STATUS_PUBLISHED)->count(),     'color' => '#34d399'],
            ['key' => 'draft',     'label' => 'Draft',          'count' => Course::withoutGlobalScopes()->where('status', Course::STATUS_DRAFT)->count(),         'color' => '#94a3b8'],
            ['key' => 'rejected',  'label' => 'Rejected',       'count' => Course::withoutGlobalScopes()->where('status', Course::STATUS_REJECTED)->count(),      'color' => '#f87171'],
            ['key' => 'archived',  'label' => 'Archived',       'count' => Course::withoutGlobalScopes()->where('status', Course::STATUS_ARCHIVED)->count(),      'color' => '#94a3b8'],
        ];

        $query = Course::withoutGlobalScopes()
            ->with(['instructor:id,name', 'category:id,name'])
            ->withCount('enrollments');

        if ($tab !== 'all' && isset($statusMap[$tab])) {
            $query->where('status', $statusMap[$tab]);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%")
                  ->orWhereHas('instructor', fn($q2) => $q2->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('category',   fn($q2) => $q2->where('name', 'like', "%{$search}%"));
            });
        }

        $query->orderBy('id', 'desc');

        $total      = $query->count();
        $totalPages = max(1, (int) ceil($total / $perPage));
        $curPage    = min($page, $totalPages);
        $courses    = $query->skip(($curPage - 1) * $perPage)->take($perPage)->get();

        return compact('tabs', 'tab', 'search', 'courses', 'total', 'totalPages', 'curPage', 'perPage');
    }
}
