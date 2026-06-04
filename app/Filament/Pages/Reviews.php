<?php

namespace App\Filament\Pages;

use App\Domains\Learning\Models\Review;
use BackedEnum;
use Filament\Pages\Page;

class Reviews extends Page
{
    protected string $view = 'filament.pages.reviews';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-star';
    protected static ?string $navigationLabel = 'Reviews';
    protected static string|\UnitEnum|null $navigationGroup = 'Learning';
    protected static ?int $navigationSort = 6;
    protected static ?string $slug = 'reviews';

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return '';
    }

    protected function getViewData(): array
    {
        $tab      = request('tab', 'all');
        $search   = request('search', '');
        $courseId = request('course_id');
        $page     = max(1, (int) request('page', 1));
        $perPage  = (int) request('per_page', 10);

        if (!in_array($perPage, [10, 25, 50])) $perPage = 10;

        $tabs = [
            ['key' => 'all', 'label' => 'All',   'count' => Review::count(),                         'color' => '#d97706'],
            ['key' => '5',   'label' => '5★',     'count' => Review::where('rating', 5)->count(),    'color' => '#34d399'],
            ['key' => '4',   'label' => '4★',     'count' => Review::where('rating', 4)->count(),    'color' => '#60a5fa'],
            ['key' => '3',   'label' => '3★',     'count' => Review::where('rating', 3)->count(),    'color' => '#fbbf24'],
            ['key' => '2',   'label' => '2★',     'count' => Review::where('rating', 2)->count(),    'color' => '#fb923c'],
            ['key' => '1',   'label' => '1★',     'count' => Review::where('rating', 1)->count(),    'color' => '#f87171'],
        ];

        $query = Review::with(['course:id,title', 'user:id,name']);

        if ($courseId) {
            $query->where('course_id', $courseId);
        }

        if ($tab !== 'all' && in_array($tab, ['1', '2', '3', '4', '5'])) {
            $query->where('rating', (int) $tab);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('comment', 'like', "%{$search}%")
                  ->orWhereHas('course', fn($q2) => $q2->where('title', 'like', "%{$search}%"))
                  ->orWhereHas('user',   fn($q2) => $q2->where('name',  'like', "%{$search}%"));
            });
        }

        $query->orderBy('id', 'desc');

        $total      = $query->count();
        $totalPages = max(1, (int) ceil($total / $perPage));
        $curPage    = min($page, $totalPages);
        $reviews    = $query->skip(($curPage - 1) * $perPage)->take($perPage)->get();

        return compact('tabs', 'tab', 'search', 'courseId', 'reviews', 'total', 'totalPages', 'curPage', 'perPage');
    }
}
