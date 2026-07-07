<?php

namespace App\Filament\Resources\Users\Pages;

use App\Domains\Users\Models\InstructorVerification;
use App\Filament\Resources\Users\UserResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
    protected string $view = 'filament.resources.users.create-user';
    protected array $pendingRoleIds = [];
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

        if ($this->record->fresh()->hasRole('instructor')) {
            InstructorVerification::create([
                'user_id'     => $this->record->id,
                'status'      => 'approved',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);
        }
    }

    protected function getRedirectUrl(): string
    {
        return route('filament.admin.pages.users');
    }
}
