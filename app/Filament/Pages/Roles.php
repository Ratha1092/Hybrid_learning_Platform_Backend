<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Roles\RoleResource;
use App\Support\PanelAccess;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Spatie\Permission\Models\Role;

class Roles extends Page
{
    protected string $view = 'filament.pages.roles';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;
    protected static ?string $navigationLabel = 'Roles';
    protected static string|\UnitEnum|null $navigationGroup = 'System';
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

    public function deleteRole(int $roleId): void
    {
        $role = Role::find($roleId);

        if (!$role || !RoleResource::canDelete($role)) {
            Notification::make()
                ->title('This role cannot be deleted.')
                ->danger()
                ->send();

            return;
        }

        $role->delete();

        Notification::make()
            ->title('Role deleted.')
            ->success()
            ->send();
    }

    protected function getViewData(): array
    {
        $search  = $this->search;
        $page    = max(1, $this->page);
        $perPage = in_array($this->perPage, [10, 25, 50], true) ? $this->perPage : 10;

        $query = Role::withCount(['permissions', 'users']);

        if ($search) {
            $query->where('name', 'ilike', "%{$search}%");
        }

        $query->orderBy('name');

        $total      = $query->count();
        $totalPages = max(1, (int) ceil($total / $perPage));
        $curPage    = min($page, $totalPages);
        $roles      = $query->skip(($curPage - 1) * $perPage)->take($perPage)->get();

        $canDelete = PanelAccess::can('roles.delete');

        return compact('search', 'roles', 'total', 'totalPages', 'curPage', 'perPage', 'canDelete');
    }
}
