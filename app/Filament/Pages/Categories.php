<?php

namespace App\Filament\Pages;

use App\Domains\Courses\Models\Category;
use BackedEnum;
use Filament\Pages\Page;

class Categories extends Page
{
    protected string $view = 'filament.pages.categories';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationLabel = 'Categories';
    protected static string|\UnitEnum|null $navigationGroup = 'Learning';
    protected static ?int $navigationSort = 2;
    protected static ?string $slug = 'categories';

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return '';
    }

    protected function getViewData(): array
    {
        $search  = request('search', '');
        $page    = max(1, (int) request('page', 1));
        $perPage = (int) request('per_page', 10);

        if (!in_array($perPage, [10, 25, 50])) $perPage = 10;

        $query = Category::withCount('courses');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
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
