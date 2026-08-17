<?php

namespace App\Filament\Pages;

use App\Domains\Courses\Models\Course;
use App\Domains\Learning\Models\Review;
use App\Support\NavBadge;
use App\Support\PanelAccess;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Number;

class Reviews extends Page
{
    protected string $view = 'filament.pages.reviews';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-star';
    protected static ?string $navigationLabel = 'Reviews';
    protected static string|\UnitEnum|null $navigationGroup = 'Learning';
    protected static ?int $navigationSort = 6;
    protected static ?string $slug = 'reviews';

    public static function canAccess(): bool
    {
        return PanelAccess::can('reviews.view');
    }

    public function mount(): void
    {
        $this->courseId = request()->integer('course_id') ?: null;

        NavBadge::markSeen('reviews');
    }

    public static function getNavigationBadge(): ?string
    {
        $count = NavBadge::countSince('reviews', fn (?\Carbon\Carbon $since) => $since
            ? Review::where('created_at', '>', $since)->count()
            : Review::count());

        return $count > 0 ? Number::abbreviate($count) : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'New reviews since you last viewed this page';
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

    public function toggleFeatured(int $reviewId): void
    {
        if (!PanelAccess::can('reviews.update')) {
            Notification::make()->title('You do not have permission to feature reviews.')->danger()->send();
            return;
        }

        $review = Review::find($reviewId);

        if (!$review) {
            return;
        }

        $review->update(['is_featured' => !$review->is_featured]);

        Notification::make()
            ->title($review->is_featured ? 'Review featured' : 'Review unfeatured')
            ->success()
            ->send();
    }

    public function toggleApproved(int $reviewId): void
    {
        if (!PanelAccess::can('reviews.approve')) {
            Notification::make()->title('You do not have permission to approve reviews.')->danger()->send();
            return;
        }

        $review = Review::find($reviewId);

        if (!$review) {
            return;
        }

        $review->update([
            'is_approved' => !$review->is_approved,
            'approved_by' => !$review->is_approved ? auth()->id() : $review->approved_by,
        ]);

        Notification::make()
            ->title($review->is_approved ? 'Review approved' : 'Review unapproved')
            ->success()
            ->send();
    }

    protected function getViewData(): array
    {
        $tab      = $this->tab;
        $search   = $this->search;
        $courseId = $this->courseId;
        $page     = max(1, $this->page);
        $perPage  = in_array($this->perPage, [10, 25, 50], true) ? $this->perPage : 10;

        // One grouped query instead of six separate COUNT round-trips for the tab badges.
        $ratingCounts = Review::query()
            ->toBase()
            ->selectRaw('rating, count(*) as aggregate')
            ->groupBy('rating')
            ->pluck('aggregate', 'rating');

        $tabs = [
            ['key' => 'all', 'label' => 'All',   'count' => $ratingCounts->sum(),         'color' => '#2563eb'],
            ['key' => '5',   'label' => '5★',     'count' => $ratingCounts[5] ?? 0,    'color' => '#34d399'],
            ['key' => '4',   'label' => '4★',     'count' => $ratingCounts[4] ?? 0,    'color' => '#60a5fa'],
            ['key' => '3',   'label' => '3★',     'count' => $ratingCounts[3] ?? 0,    'color' => '#fbbf24'],
            ['key' => '2',   'label' => '2★',     'count' => $ratingCounts[2] ?? 0,    'color' => '#fb923c'],
            ['key' => '1',   'label' => '1★',     'count' => $ratingCounts[1] ?? 0,    'color' => '#f87171'],
        ];

        $query = Review::with(['course:id,title', 'user:id,name']);

        $courseTitle = $courseId ? Course::find($courseId)?->title : null;

        if ($courseId) {
            $query->where('course_id', $courseId);
        }

        if ($tab !== 'all' && in_array($tab, ['1', '2', '3', '4', '5'])) {
            $query->where('rating', (int) $tab);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('comment', 'ilike', "%{$search}%")
                  ->orWhereHas('course', fn($q2) => $q2->where('title', 'ilike', "%{$search}%"))
                  ->orWhereHas('user',   fn($q2) => $q2->where('name',  'ilike', "%{$search}%"));
            });
        }

        $query->orderBy('id', 'desc');

        $total      = $query->count();
        $totalPages = max(1, (int) ceil($total / $perPage));
        $curPage    = min($page, $totalPages);
        $reviews    = $query->skip(($curPage - 1) * $perPage)->take($perPage)->get();

        return compact('tabs', 'tab', 'search', 'courseId', 'courseTitle', 'reviews', 'total', 'totalPages', 'curPage', 'perPage');
    }
}
