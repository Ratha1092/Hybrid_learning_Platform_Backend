<?php

namespace App\Filament\Pages;

use App\Domains\Users\Models\User;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class Users extends Page
{
    protected string $view = 'filament.pages.users';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;
    protected static ?string $navigationLabel = 'Users';
    protected static string|\UnitEnum|null $navigationGroup = 'Users';
    protected static ?int $navigationSort = 1;
    protected static ?string $slug = 'users';

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

        $base = fn() => User::withoutTrashed();

        $tabs = [
            ['key' => 'all',        'label' => 'All',        'count' => $base()->count(),                                  'color' => '#2563eb'],
            ['key' => 'admin',      'label' => 'Admin',      'count' => $base()->where('role', 'admin')->count(),           'color' => '#a855f7'],
            ['key' => 'instructor', 'label' => 'Instructor', 'count' => $base()->where('role', 'instructor')->count(),      'color' => '#3b82f6'],
            ['key' => 'student',    'label' => 'Student',    'count' => $base()->where('role', 'student')->count(),         'color' => '#10b981'],
        ];

        $query = User::withoutTrashed();

        if ($tab !== 'all' && in_array($tab, ['admin', 'instructor', 'student'])) {
            $query->where('role', $tab);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $query->orderBy('id', 'desc');

        $total      = $query->count();
        $totalPages = max(1, (int) ceil($total / $perPage));
        $curPage    = min($page, $totalPages);
        $users      = $query->skip(($curPage - 1) * $perPage)->take($perPage)->get();

        return compact('tabs', 'tab', 'search', 'users', 'total', 'totalPages', 'curPage', 'perPage');
    }
}
