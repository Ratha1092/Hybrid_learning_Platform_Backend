<?php

namespace App\Filament\Pages;

use App\Domains\Courses\Models\Course;
use App\Domains\Courses\Models\Lesson;
use App\Support\NavBadge;
use App\Support\PanelAccess;
use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Number;

class Lessons extends Page
{
    protected string $view = 'filament.pages.lessons';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-play-circle';
    protected static ?string $navigationLabel = 'Lessons';
    protected static string|\UnitEnum|null $navigationGroup = 'Learning';
    protected static ?int $navigationSort = 5;
    protected static ?string $slug = 'lessons';

    public static function canAccess(): bool
    {
        return PanelAccess::can('courses.view');
    }

    public function mount(): void
    {
        $this->courseId = request()->integer('course_id') ?: null;

        NavBadge::markSeen('lessons');
    }

    public static function getNavigationBadge(): ?string
    {
        $count = NavBadge::countSince('lessons', fn (?\Carbon\Carbon $since) => $since
            ? Lesson::withoutGlobalScopes([SoftDeletingScope::class])->where('created_at', '>', $since)->count()
            : Lesson::withoutGlobalScopes([SoftDeletingScope::class])->count());

        return $count > 0 ? Number::abbreviate($count) : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'New lessons since you last viewed this page';
    }

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return '';
    }

    public string $tab = 'all';
    public string $search = '';
    public ?int $courseId = null;
    public int $page = 1;
    public int $perPage = 10;

    public function updatedSearch(): void
    {
        $this->page = 1;
    }

    public function selectTab(string $tab): void
    {
        $this->tab = $tab;
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
        $tab      = $this->tab;
        $search   = $this->search;
        $courseId = $this->courseId;
        $page     = max(1, $this->page);
        $perPage  = in_array($this->perPage, [10, 25, 50], true) ? $this->perPage : 10;

        $types = ['video', 'article'];

        $base = fn() => Lesson::withoutGlobalScopes([SoftDeletingScope::class])
            ->when($courseId, fn($q) => $q->whereHas('section', fn($q2) => $q2->where('course_id', $courseId)));

        // One grouped query instead of five separate COUNT round-trips for the tab badges.
        $typeCounts = $base()
            ->toBase()
            ->selectRaw('type, count(*) as aggregate')
            ->groupBy('type')
            ->pluck('aggregate', 'type');

        $tabs = [
            ['key' => 'all',     'label' => 'All',     'count' => $typeCounts->sum(),               'color' => '#0891b2'],
            ['key' => 'video',   'label' => 'Video',   'count' => $typeCounts['video'] ?? 0,   'color' => '#2563eb'],
            ['key' => 'article', 'label' => 'Article', 'count' => $typeCounts['article'] ?? 0, 'color' => '#16a34a'],
        ];

        $query = Lesson::withoutGlobalScopes([SoftDeletingScope::class])
            ->with('section:id,title,course_id');

        $courseTitle = $courseId ? Course::find($courseId)?->title : null;

        if ($courseId) {
            $query->whereHas('section', fn($q) => $q->where('course_id', $courseId));
        }

        if ($tab !== 'all' && in_array($tab, $types)) {
            $query->where('type', $tab);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'ilike', "%{$search}%")
                  ->orWhereHas('section', fn($q2) => $q2->where('title', 'ilike', "%{$search}%")
                      ->orWhereHas('course', fn($q3) => $q3->where('title', 'ilike', "%{$search}%")));
            });
        }

        $query->orderBy('id', 'desc');

        $total      = $query->count();
        $totalPages = max(1, (int) ceil($total / $perPage));
        $curPage    = min($page, $totalPages);
        $lessons    = $query->skip(($curPage - 1) * $perPage)->take($perPage)->get();

        return compact('tabs', 'tab', 'search', 'courseId', 'courseTitle', 'lessons', 'total', 'totalPages', 'curPage', 'perPage');
    }
}
