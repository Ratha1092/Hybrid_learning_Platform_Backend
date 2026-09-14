<?php

namespace App\Filament\Pages;

use App\Domains\Auth\Services\ActivityLogService;
use App\Domains\Courses\Models\Course;
use App\Domains\Notifications\Notifications\CourseApprovedNotification;
use App\Domains\Notifications\Notifications\CourseRejectedNotification;
use App\Support\NavBadge;
use App\Support\PanelAccess;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Number;

class Courses extends Page
{
    protected string $view = 'filament.pages.courses';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationLabel = 'Courses';
    protected static string|\UnitEnum|null $navigationGroup = 'Learning';
    protected static ?int $navigationSort = 2;
    protected static ?string $slug = 'courses';

    public static function canAccess(): bool
    {
        return PanelAccess::can('courses.view');
    }

    public string $activeTab   = 'all';
    public string $search      = '';
    public int    $currentPage = 1;
    public int    $perPage     = 10;

    // Reject modal state
    public ?int   $rejectingCourseId    = null;
    public string $rejectingCourseTitle = '';
    public string $rejectReason         = '';

    public function mount(): void
    {
        $this->activeTab   = request('tab', 'all');
        $this->search      = request('search', '');
        $this->currentPage = max(1, (int) request('page', 1));
        $this->perPage     = in_array((int) request('per_page', 10), [10, 25, 50])
            ? (int) request('per_page', 10) : 10;

        NavBadge::markSeen('courses');
    }

    public static function getNavigationBadge(): ?string
    {
        $count = NavBadge::countSince('courses', fn (?\Carbon\Carbon $since) => $since
            ? Course::where('status', Course::STATUS_PENDING)->where('created_at', '>', $since)->count()
            : Course::where('status', Course::STATUS_PENDING)->count());

        return $count > 0 ? Number::abbreviate($count) : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Courses pending review since you last viewed this page';
    }

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return '';
    }

    // Row actions

    public function setTab(string $tab): void
    {
        $this->activeTab   = $tab;
        $this->currentPage = 1;
    }

    public function updatedSearch(): void
    {
        $this->currentPage = 1;
    }

    public function setPage(int $page): void
    {
        $this->currentPage = $page;
    }

    public function setPerPage(string|int $perPage): void
    {
        $this->perPage     = in_array((int) $perPage, [10, 25, 50]) ? (int) $perPage : 10;
        $this->currentPage = 1;
    }

    public function openRejectModal(int $id): void
    {
        $course = Course::find($id);
        $this->rejectingCourseId    = $id;
        $this->rejectingCourseTitle = $course?->title ?? '';
        $this->rejectReason         = '';
        $this->dispatch('open-reject-modal');
    }

    public function closeRejectModal(): void
    {
        $this->rejectingCourseId    = null;
        $this->rejectingCourseTitle = '';
        $this->rejectReason         = '';
        $this->dispatch('close-reject-modal');
    }

    public function submitRejectModal(): void
    {
        if (!$this->rejectingCourseId) return;
        $this->rejectCourse($this->rejectingCourseId, $this->rejectReason);
        $this->closeRejectModal();
    }

    public function approveCourse(int $id): void
    {
        $course = Course::findOrFail($id);
        if (!$course->isPendingReview()) return;

        $course->publish(auth()->id());
        $course->instructor?->notify(new CourseApprovedNotification($course));
        ActivityLogService::logChange('course.published', $course);
        Notification::make()->title('Course Approved')->body("\"{$course->title}\" is now live.")->success()->send();
    }

    public function rejectCourse(int $id, string $reason): void
    {
        $course = Course::findOrFail($id);
        if (!$course->isPendingReview()) return;

        $reason = trim($reason);
        if ($reason === '') {
            Notification::make()->title('Rejection reason is required.')->danger()->send();
            return;
        }

        $course->reject($reason);
        $course->instructor?->notify(new CourseRejectedNotification($course, $reason));
        ActivityLogService::logChange('course.rejected', $course, [], ['reason' => $reason]);
        Notification::make()->title('Course Rejected')->body('Instructor notified.')->danger()->send();
    }

    public function archiveCourse(int $id): void
    {
        $course = Course::findOrFail($id);
        if (!$course->isPublished()) return;

        $course->archive();
        Notification::make()->title('Course Archived')->warning()->send();
    }

    public function unarchiveCourse(int $id): void
    {
        $course = Course::findOrFail($id);
        if (!$course->isArchived()) return;

        $course->unarchive(auth()->id());
        Notification::make()->title('Course Restored to Published')->success()->send();
    }

    public function deleteCourse(int $id): void
    {
        if (!PanelAccess::can('courses.delete')) {
            Notification::make()->title('Insufficient permissions')->danger()->send();
            return;
        }

        $course = Course::findOrFail($id);
        $title  = $course->title;
        $course->delete();

        ActivityLogService::logChange('course.deleted', $course);
        Notification::make()->title('Course Deleted')->body("\"{$title}\" has been removed.")->danger()->send();
    }

    public function restoreCourse(int $id): void
    {
        if (!PanelAccess::can('courses.delete')) {
            Notification::make()->title('Insufficient permissions')->danger()->send();
            return;
        }

        $course = Course::onlyTrashed()->find($id);

        if (!$course) {
            return;
        }

        $course->restore();

        ActivityLogService::logChange('course.restored', $course);
        Notification::make()->title('Course Restored')->success()->send();
    }

    public function returnToDraft(int $id): void
    {
        $course = Course::findOrFail($id);
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

        $tab    = $this->activeTab;
        $search = $this->search;

        $query = $tab === 'trashed'
            ? Course::onlyTrashed()->with(['instructor:id,name', 'category:id,name'])->withCount('enrollments')
            : Course::query()->with(['instructor:id,name', 'category:id,name'])->withCount('enrollments');

        if ($tab !== 'all' && $tab !== 'trashed' && isset($statusMap[$tab])) {
            $query->where('status', $statusMap[$tab]);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'ilike', "%{$search}%")
                  ->orWhere('short_description', 'ilike', "%{$search}%")
                  ->orWhereHas('instructor', fn($q2) => $q2->where('name', 'ilike', "%{$search}%"))
                  ->orWhereHas('category',   fn($q2) => $q2->where('name', 'ilike', "%{$search}%"));
                if (str_contains(strtolower($search), 'free')) {
                    $q->orWhere('price', 0);
                }
            });
        }

        $rows = [['ID', 'Title', 'Instructor', 'Category', 'Price', 'Status', 'Students', 'Created']];

        foreach ($query->orderBy('id', 'desc')->get() as $course) {
            $rows[] = [
                $course->id,
                $course->title,
                $course->instructor?->name ?? '',
                $course->category?->name ?? '',
                $course->price > 0 ? number_format($course->price, 2) : 'Free',
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

    // View dat

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

        $statusCounts = Course::query()
            ->toBase()
            ->selectRaw('status, count(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $tabs = [
            ['key' => 'all',       'label' => 'All',            'count' => $statusCounts->sum(),                             'color' => null],
            ['key' => 'pending',   'label' => 'Pending Review', 'count' => $statusCounts[Course::STATUS_PENDING] ?? 0,   'color' => '#fbbf24'],
            ['key' => 'published', 'label' => 'Published',      'count' => $statusCounts[Course::STATUS_PUBLISHED] ?? 0, 'color' => '#34d399'],
            ['key' => 'draft',     'label' => 'Draft',          'count' => $statusCounts[Course::STATUS_DRAFT] ?? 0,     'color' => '#94a3b8'],
            ['key' => 'rejected',  'label' => 'Rejected',       'count' => $statusCounts[Course::STATUS_REJECTED] ?? 0,  'color' => '#f87171'],
            ['key' => 'archived',  'label' => 'Archived',       'count' => $statusCounts[Course::STATUS_ARCHIVED] ?? 0,  'color' => '#94a3b8'],
            ['key' => 'trashed',   'label' => 'Deleted',        'count' => Course::onlyTrashed()->count(),               'color' => '#dc2626'],
        ];

        $query = $tab === 'trashed'
            ? Course::onlyTrashed()->with(['instructor:id,name', 'category:id,name'])->withCount('enrollments')
            : Course::withoutGlobalScopes()->with(['instructor:id,name', 'category:id,name'])->withCount('enrollments');

        if ($tab !== 'all' && $tab !== 'trashed' && isset($statusMap[$tab])) {
            $query->where('status', $statusMap[$tab]);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'ilike', "%{$search}%")
                  ->orWhere('short_description', 'ilike', "%{$search}%")
                  ->orWhereHas('instructor', fn($q2) => $q2->where('name', 'ilike', "%{$search}%"))
                  ->orWhereHas('category',   fn($q2) => $q2->where('name', 'ilike', "%{$search}%"));
                if (str_contains(strtolower($search), 'free')) {
                    $q->orWhere('price', 0);
                }
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
