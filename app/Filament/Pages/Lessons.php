<?php

namespace App\Filament\Pages;

use App\Domains\Courses\Models\Lesson;
use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class Lessons extends Page
{
    protected string $view = 'filament.pages.lessons';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-play-circle';
    protected static ?string $navigationLabel = 'Lessons';
    protected static string|\UnitEnum|null $navigationGroup = 'Learning';
    protected static ?int $navigationSort = 5;
    protected static ?string $slug = 'lessons';

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return '';
    }

    protected function getViewData(): array
    {
        $tab     = request('tab', 'all');
        $search  = request('search', '');
        $page    = max(1, (int) request('page', 1));
        $perPage = (int) request('per_page', 10);

        if (!in_array($perPage, [10, 25, 50])) $perPage = 10;

        $types = ['video', 'article', 'quiz', 'live', 'assignment'];

        $tabs = [
            ['key' => 'all',        'label' => 'All',        'count' => Lesson::withoutGlobalScopes([SoftDeletingScope::class])->count(),                                                'color' => '#0891b2'],
            ['key' => 'video',      'label' => 'Video',      'count' => Lesson::withoutGlobalScopes([SoftDeletingScope::class])->where('type', 'video')->count(),      'color' => '#2563eb'],
            ['key' => 'article',    'label' => 'Article',    'count' => Lesson::withoutGlobalScopes([SoftDeletingScope::class])->where('type', 'article')->count(),    'color' => '#16a34a'],
            ['key' => 'quiz',       'label' => 'Quiz',       'count' => Lesson::withoutGlobalScopes([SoftDeletingScope::class])->where('type', 'quiz')->count(),       'color' => '#7c3aed'],
            ['key' => 'live',       'label' => 'Live',       'count' => Lesson::withoutGlobalScopes([SoftDeletingScope::class])->where('type', 'live')->count(),       'color' => '#dc2626'],
            ['key' => 'assignment', 'label' => 'Assignment', 'count' => Lesson::withoutGlobalScopes([SoftDeletingScope::class])->where('type', 'assignment')->count(), 'color' => '#d97706'],
        ];

        $query = Lesson::withoutGlobalScopes([SoftDeletingScope::class])
            ->with('section:id,title');

        if ($tab !== 'all' && in_array($tab, $types)) {
            $query->where('type', $tab);
        }

        if ($search) {
            $query->where('title', 'like', "%{$search}%");
        }

        $query->orderBy('id', 'asc');

        $total      = $query->count();
        $totalPages = max(1, (int) ceil($total / $perPage));
        $curPage    = min($page, $totalPages);
        $lessons    = $query->skip(($curPage - 1) * $perPage)->take($perPage)->get();

        return compact('tabs', 'tab', 'search', 'lessons', 'total', 'totalPages', 'curPage', 'perPage');
    }
}
