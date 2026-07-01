<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected string $view = 'filament.resources.users.create-user';

    protected array $pendingRoleIds = [];

    // Bypasses Filament's relationship select entirely — its getState() call during
    // save re-reads roles from the DB and clobbers whatever the checkboxes set.
    public array $selectedRoleIds = [];

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($this->selectedRoleIds)) {
            Notification::make()->title('Please select at least one role.')->danger()->send();
            $this->halt();
        }

        $this->pendingRoleIds = array_values(array_map('intval', $this->selectedRoleIds));
        unset($data['roles']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->record->syncRoles($this->pendingRoleIds);
    }

    protected function getRedirectUrl(): string
    {
        return route('filament.admin.pages.users');
    }
}
