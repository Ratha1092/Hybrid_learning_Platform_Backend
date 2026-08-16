<?php

namespace App\Filament\Pages;

use App\Domains\Courses\Models\Course;
use App\Domains\Courses\Models\Section;
use App\Support\NavBadge;
use App\Support\PanelAccess;
use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Support\Number;

class Sections extends Page
{
    protected string $view = 'filament.pages.sections';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-queue-list';
    protected static ?string $navigationLabel = 'Sections';
    protected static string|\UnitEnum|null $navigationGroup = 'Learning';
    protected static ?int $navigationSort = 4;
    protected static ?string $slug = 'sections';

    public static function canAccess(): bool
    {
        return PanelAccess::can('courses.view');
    }

    public function mount(): void
    {
        $this->courseId = request()->integer('course_id') ?: null;

        NavBadge::markSeen('sections');
    }

    public static function getNavigationBadge(): ?string
    {
        $count = NavBadge::countSince('sections', fn (?\Carbon\Carbon $since) => $since
            ? Section::where('created_at', '>', $since)->count()
            : Section::count());

        return $count > 0 ? Number::abbreviate($count) : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'New sections since you last viewed this page';
    }

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return '';
    }

    public string $search = '';
    public ?int $courseId = null;
    public int $page = 1;
    public int $perPage = 10;

    public function updatedSearch(): void
    {
        $this->page = 1;
    }

    public function updatedCourseId(): void
    {
        $this->page = 1;
    }

    public function setPerPage(int $perPage): void
    {
        $this->perPage = in_array($perPage, [10, 25, 50], true) ? $perPage : 10;
        $this->page = 1;
    }

    public function gotoPage(int $page): void
    {
        $this->page = max(1, $page);
    }

    protected function getViewData(): array
    {
        $search   = $this->search;
        $courseId = $this->courseId;
        $page     = max(1, $this->page);
        $perPage  = in_array($this->perPage, [10, 25, 50], true) ? $this->perPage : 10;

        $query = Section::with('course:id,title')->withCount('lessons');

        $courseTitle = $courseId ? Course::find($courseId)?->title : null;

        if ($courseId) {
            $query->where('course_id', $courseId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'ilike', "%{$search}%")
                  ->orWhereHas('course', fn($q2) => $q2->where('title', 'ilike', "%{$search}%"));
            });
        }

        $query->orderBy('id', 'desc');

        $total      = $query->count();
        $totalPages = max(1, (int) ceil($total / $perPage));
        $curPage    = min($page, $totalPages);
        $sections   = $query->skip(($curPage - 1) * $perPage)->take($perPage)->get();

        return compact('search', 'courseId', 'courseTitle', 'sections', 'total', 'totalPages', 'curPage', 'perPage');
    }
}
