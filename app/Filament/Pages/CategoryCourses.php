<?php

namespace App\Filament\Pages;

use App\Domains\Courses\Models\Category;
use App\Domains\Courses\Models\Course;
use App\Support\PanelAccess;
use BackedEnum;
use Filament\Pages\Page;

class CategoryCourses extends Page
{
    protected string $view = 'filament.pages.category-courses';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'category-courses';

    public static function canAccess(): bool
    {
        return PanelAccess::can('courses.view');
    }

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return '';
    }

    public int $categoryId = 0;
    public string $search = '';
    public int $page = 1;
    public int $perPage = 10;

    public function mount(): void
    {
        $this->categoryId = max(0, (int) request('id', 0));
    }

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

    protected function getViewData(): array
    {
        $categoryId = $this->categoryId;
        $category   = $categoryId ? Category::withCount('courses')->find($categoryId) : null;

        if (!$category) {
            abort(404);
        }

        $search  = $this->search;
        $page    = max(1, $this->page);
        $perPage = in_array($this->perPage, [10, 25, 50], true) ? $this->perPage : 10;

        $query = Course::withoutGlobalScopes()
            ->with(['instructor:id,name'])
            ->withCount('enrollments')
            ->where('category_id', $categoryId);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'ilike', "%{$search}%")
                  ->orWhere('short_description', 'ilike', "%{$search}%")
                  ->orWhereHas('instructor', fn($q2) => $q2->where('name', 'ilike', "%{$search}%"));
            });
        }

        $query->orderBy('id', 'desc');

        $total      = $query->count();
        $totalPages = max(1, (int) ceil($total / $perPage));
        $curPage    = min($page, $totalPages);
        $courses    = $query->skip(($curPage - 1) * $perPage)->take($perPage)->get();

        $backUrl = route('filament.admin.pages.categories');

        return compact('category', 'courses', 'search', 'total', 'totalPages', 'curPage', 'perPage', 'backUrl');
    }
}
