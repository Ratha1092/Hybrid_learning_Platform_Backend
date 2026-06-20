<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Spatie\Permission\Models\Permission;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;
    protected string $view = 'filament.resources.roles.edit-role';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Back to Roles')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(route('filament.admin.pages.roles')),
            DeleteAction::make()
                ->visible(fn () => RoleResource::canDelete($this->record)),
        ];
    }

    protected function getViewData(): array
    {
        $role = $this->record;

        $grantedNames = $role->permissions->pluck('name')->all();

        $grouped = Permission::orderBy('name')->get()
            ->groupBy(fn ($p) => explode('.', $p->name)[0]);

        return [
            'role' => $role,
            'grouped' => $grouped,
            'grantedNames' => $grantedNames,
        ];
    }

    protected function getRedirectUrl(): string
    {
        return route('filament.admin.pages.roles');
    }
}
