<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\ViewRecord;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;
    protected string $view = 'filament.resources.users.view-user';

    public function mount(int|string $record): void
    {
        parent::mount($record);

        // Super-admin accounts are invisible/untouchable to everyone except other super-admins.
        if ($this->record->hasRole('super-admin') && ! auth()->user()?->hasRole('super-admin')) {
            abort(404);
        }
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
