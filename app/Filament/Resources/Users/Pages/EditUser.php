<?php

namespace App\Filament\Resources\Users\Pages;

use App\Domains\Auth\Services\ActivityLogService;
use App\Domains\Users\Models\User;
use App\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected string $view = 'filament.resources.users.edit-user';

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

    public function suspendUser(): void
    {
        $user = $this->record;

        if ($user->id === auth()->id()) {
            Notification::make()->title('Cannot suspend yourself')->danger()->send();
            return;
        }

        if ($user->hasRole('super-admin') && ! auth()->user()?->hasRole('super-admin')) {
            Notification::make()->title('Insufficient permissions')->danger()->send();
            return;
        }

        $user->update(['status' => 'suspended']);

        // Sync the form state so the status dropdown reflects the change immediately
        $this->data['status'] = 'suspended';

        Notification::make()
            ->title('User suspended')
            ->body("{$user->name} has been suspended.")
            ->warning()
            ->send();
    }

    public function unsuspendUser(): void
    {
        $this->record->update(['status' => 'active']);
        $this->data['status'] = 'active';

        Notification::make()
            ->title('User restored')
            ->body("{$this->record->name}'s account is active again.")
            ->success()
            ->send();
    }

    public function removeUser(): void
    {
        $user = $this->record;

        if ($user->id === auth()->id()) {
            Notification::make()->title('Cannot remove yourself')->danger()->send();
            return;
        }

        if ($user->hasRole('super-admin') && ! auth()->user()?->hasRole('super-admin')) {
            Notification::make()->title('Insufficient permissions')->danger()->send();
            return;
        }

        $user->delete();

        Notification::make()
            ->title('User removed')
            ->body("{$user->name} has been removed.")
            ->danger()
            ->send();

        $this->redirect(route('filament.admin.pages.users'));
    }

    protected function getRedirectUrl(): string
    {
        return route('filament.admin.pages.users');
    }
}
