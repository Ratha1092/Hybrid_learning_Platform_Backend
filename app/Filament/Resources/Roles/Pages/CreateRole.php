<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;
use Spatie\Permission\Models\Permission;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;
    protected string $view = 'filament.resources.roles.create-role';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    protected function getViewData(): array
    {
        $grouped = Permission::orderBy('name')->get()
            ->groupBy(fn ($p) => explode('.', $p->name)[0]);

        return [
            'grouped'          => $grouped,
            'totalPermissions' => Permission::count(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return route('filament.admin.pages.roles');
    }
}
