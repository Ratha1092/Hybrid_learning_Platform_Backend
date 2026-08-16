<?php

namespace App\Filament\Pages;

use App\Domains\Courses\Models\Category;
use App\Filament\Resources\Categories\CategoryResource;
use App\Support\NavBadge;
use App\Support\PanelAccess;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Number;

class Categories extends Page
{
    protected string $view = 'filament.pages.categories';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationLabel = 'Categories';
    protected static string|\UnitEnum|null $navigationGroup = 'Learning';
    protected static ?int $navigationSort = 1;
    protected static ?string $slug = 'categories';

    public static function canAccess(): bool
    {
        return PanelAccess::can('categories.view');
    }

    public function mount(): void
    {
        NavBadge::markSeen('categories');
    }

    public static function getNavigationBadge(): ?string
    {
        $count = NavBadge::countSince('categories', fn (?\Carbon\Carbon $since) => $since
            ? Category::where('created_at', '>', $since)->count()
            : Category::count());

        return $count > 0 ? Number::abbreviate($count) : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'New categories since you last viewed this page';
    }

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return '';
    }

    public string $search = '';
    public int $page = 1;
    public int $perPage = 10;

    public function updatedSearch(): void
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

    public function deleteCategory(int $categoryId): void
    {
        $category = Category::withCount('courses')->find($categoryId);

        if (!$category || !CategoryResource::canDelete($category)) {
            Notification::make()
                ->title($category && $category->courses_count > 0
                    ? 'Move or delete its courses first — this category still has courses assigned.'
                    : 'This category cannot be deleted.')
                ->danger()
                ->send();

            return;
        }

        $category->delete();

        Notification::make()
            ->title('Category deleted.')
            ->success()
            ->send();
    }

    protected function getViewData(): array
    {
        $search  = $this->search;
        $page    = max(1, $this->page);
        $perPage = in_array($this->perPage, [10, 25, 50], true) ? $this->perPage : 10;

        $query = Category::withCount('courses');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('description', 'ilike', "%{$search}%");
            });
        }

        $query->orderBy('id', 'desc');

        $total      = $query->count();
        $totalPages = max(1, (int) ceil($total / $perPage));
        $curPage    = min($page, $totalPages);
        $categories = $query->skip(($curPage - 1) * $perPage)->take($perPage)->get();

        return compact('search', 'categories', 'total', 'totalPages', 'curPage', 'perPage');
    }
}
