<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use Filament\Resources\Pages\ViewRecord;

class ViewRole extends ViewRecord
{
    protected static string $resource = RoleResource::class;
    protected string $view = 'filament.resources.roles.view-role';

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getViewData(): array
    {
        $role = $this->record->load('users', 'permissions');

        $grantedNames = $role->permissions->pluck('name')->all();
        $grouped = $role->permissions->sortBy('name')
            ->groupBy(fn ($p) => explode('.', $p->name)[0]);

        $users      = $role->users()->orderBy('name')->get();
        $usersCount = $users->count();
        $canUpdate  = auth()->user()?->can('roles.update') ?? false;

        $assignUrl = route('admin.roles.users.assign', ['role' => $role->id]);

        $assignedIds = $users->pluck('id')->all();
        $allUsers = \App\Domains\Users\Models\User::orderBy('name')
            ->when(!empty($assignedIds), fn ($q) => $q->whereNotIn('id', $assignedIds))
            ->get(['id', 'name', 'email']);

        return compact('role', 'grantedNames', 'grouped', 'users', 'usersCount', 'canUpdate', 'assignUrl', 'allUsers');
    }
}
