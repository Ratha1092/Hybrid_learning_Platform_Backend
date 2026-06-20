<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Domains\Auth\Services\ActivityLogService;
use App\Filament\Resources\Roles\RoleResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Back to Roles')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(route('filament.admin.pages.roles')),
        ];
    }

    protected function afterCreate(): void
    {
        ActivityLogService::logChange('role.created', $this->record, [], $this->record->only(['name']));
    }

    protected function getRedirectUrl(): string
    {
        return route('filament.admin.pages.roles');
    }
}
