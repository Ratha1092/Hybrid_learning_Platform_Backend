<?php

namespace App\Filament\Pages;

use App\Support\PanelAccess;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Spatie\Permission\Models\Role;

class Roles extends Page
{
    protected string $view = 'filament.pages.roles';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;
    protected static ?string $navigationLabel = 'Roles';
    protected static string|\UnitEnum|null $navigationGroup = 'People';
    protected static ?int $navigationSort = 2;
    protected static ?string $slug = 'roles';

    public static function canAccess(): bool
    {
        return PanelAccess::can('roles.view');
    }

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

        $query = Role::withCount(['permissions', 'users']);

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $query->orderBy('name');

        $total      = $query->count();
        $totalPages = max(1, (int) ceil($total / $perPage));
        $curPage    = min($page, $totalPages);
        $roles      = $query->skip(($curPage - 1) * $perPage)->take($perPage)->get();

        return compact('search', 'roles', 'total', 'totalPages', 'curPage', 'perPage');
    }
}
