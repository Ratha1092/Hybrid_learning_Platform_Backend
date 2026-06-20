<?php

namespace App\Filament\Resources\Users\Pages;

use App\Domains\Auth\Services\ActivityLogService;
use App\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected array $originalRoles = [];

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function fillForm(): void
    {
        parent::fillForm();

        $this->originalRoles = $this->record->getRoleNames()->sort()->values()->all();
    }

    protected function afterSave(): void
    {
        $newRoles = $this->record->getRoleNames()->sort()->values()->all();

        if ($newRoles !== $this->originalRoles) {
            ActivityLogService::logChange(
                'user.roles_changed',
                $this->record,
                ['roles' => $this->originalRoles],
                ['roles' => $newRoles],
            );
        }
    }

    protected function getRedirectUrl(): string
    {
        return route('filament.admin.pages.users');
    }
}
