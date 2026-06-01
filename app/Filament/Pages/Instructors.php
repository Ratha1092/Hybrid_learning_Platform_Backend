<?php

namespace App\Filament\Pages;

use App\Domains\Users\Models\User;
use BackedEnum;
use Filament\Pages\Page;

class Instructors extends Page
{
    protected string $view = 'filament.pages.instructors';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Instructors';
    protected static string|\UnitEnum|null $navigationGroup = 'Learning';
    protected static ?int $navigationSort = 3;
    protected static ?string $slug = 'instructors';

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

        $tabs = [
            ['key' => 'all',      'label' => 'All',      'count' => User::where('role', 'instructor')->count(),                                              'color' => '#2563eb'],
            ['key' => 'verified', 'label' => 'Verified',  'count' => User::where('role', 'instructor')->where('instructor_status', 'verified')->count(),    'color' => '#34d399'],
            ['key' => 'pending',  'label' => 'Pending',   'count' => User::where('role', 'instructor')->where('instructor_status', 'pending')->count(),     'color' => '#fbbf24'],
            ['key' => 'rejected', 'label' => 'Rejected',  'count' => User::where('role', 'instructor')->where('instructor_status', 'rejected')->count(),    'color' => '#f87171'],
        ];

        $query = User::where('role', 'instructor')->withCount('courses');

        if ($tab !== 'all' && in_array($tab, ['verified', 'pending', 'rejected'])) {
            $query->where('instructor_status', $tab);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $query->orderBy('id', 'asc');

        $total       = $query->count();
        $totalPages  = max(1, (int) ceil($total / $perPage));
        $curPage     = min($page, $totalPages);
        $instructors = $query->skip(($curPage - 1) * $perPage)->take($perPage)->get();

        return compact('tabs', 'tab', 'search', 'instructors', 'total', 'totalPages', 'curPage', 'perPage');
    }
}
