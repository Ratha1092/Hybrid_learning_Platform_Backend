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
}
