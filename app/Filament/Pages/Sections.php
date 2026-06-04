<?php

namespace App\Filament\Pages;

use App\Domains\Courses\Models\Section;
use BackedEnum;
use Filament\Pages\Page;

class Sections extends Page
{
    protected string $view = 'filament.pages.sections';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-queue-list';
    protected static ?string $navigationLabel = 'Sections';
    protected static string|\UnitEnum|null $navigationGroup = 'Learning';
    protected static ?int $navigationSort = 4;
    protected static ?string $slug = 'sections';

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return '';
    }

    protected function getViewData(): array
    {
        $search   = request('search', '');
        $courseId = request('course_id');
        $page     = max(1, (int) request('page', 1));
        $perPage  = (int) request('per_page', 10);

        if (!in_array($perPage, [10, 25, 50])) $perPage = 10;

        $query = Section::with('course:id,title')->withCount('lessons');

        if ($courseId) {
            $query->where('course_id', $courseId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('course', fn($q2) => $q2->where('title', 'like', "%{$search}%"));
            });
        }

        $query->orderBy('id', 'asc');

        $total      = $query->count();
        $totalPages = max(1, (int) ceil($total / $perPage));
        $curPage    = min($page, $totalPages);
        $sections   = $query->skip(($curPage - 1) * $perPage)->take($perPage)->get();

        return compact('search', 'courseId', 'sections', 'total', 'totalPages', 'curPage', 'perPage');
    }
}
