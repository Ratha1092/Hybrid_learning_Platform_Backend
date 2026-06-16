<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\ViewRecord;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;
    protected string $view = 'filament.resources.users.view-user';

    protected function getHeaderActions(): array
    {
        return [];
    }
}
