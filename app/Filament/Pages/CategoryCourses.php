<?php

namespace App\Filament\Pages;

use App\Domains\Courses\Models\Category;
use App\Domains\Courses\Models\Course;
use BackedEnum;
use Filament\Pages\Page;

class CategoryCourses extends Page
{
    protected string $view = 'filament.pages.category-courses';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'category-courses';

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return '';
    }

    protected function getViewData(): array
    {
        $categoryId = max(0, (int) request('id', 0));
        $category   = $categoryId ? Category::withCount('courses')->find($categoryId) : null;

        if (!$category) {
            abort(404);
        }

        $search  = request('search', '');
        $page    = max(1, (int) request('page', 1));
        $perPage = in_array((int) request('per_page', 10), [10, 25, 50])
            ? (int) request('per_page', 10) : 10;

        $query = Course::withoutGlobalScopes()
            ->with(['instructor:id,name'])
            ->withCount('enrollments')
            ->where('category_id', $categoryId);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%")
                  ->orWhereHas('instructor', fn($q2) => $q2->where('name', 'like', "%{$search}%"));
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
