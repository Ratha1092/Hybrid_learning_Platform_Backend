<?php

namespace App\Filament\Resources\Users\Pages;

use App\Domains\Auth\Services\ActivityLogService;
use App\Domains\Users\Mail\AccountSuspendedMail;
use App\Domains\Users\Models\InstructorVerification;
use App\Domains\Users\Models\User;
use App\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class EditUser extends EditRecord
{
    use WithFileUploads;

    protected static string $resource = UserResource::class;

    protected string $view = 'filament.resources.users.edit-user';

    protected array $originalRoles = [];

    protected array $pendingRoleIds = [];

    protected ?string $originalAvatar = null;

    // Bypasses Filament's relationship select entirely — its getState() call during
    // save re-reads roles from the DB and clobbers whatever the checkboxes set.
    public array $selectedRoleIds = [];

    public ?TemporaryUploadedFile $avatarUpload = null;

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
        $this->originalAvatar = $this->record->avatar;
        $this->selectedRoleIds = $this->record->roles()
            ->pluck('roles.id')
            ->map(fn ($id) => (string) $id)
            ->values()
            ->all();
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Read from selectedRoleIds (driven directly by the checkboxes), not $data['roles'] —
        // Filament's relationship Select re-reads roles from the DB during getState() and
        // would otherwise clobber whatever was just checked.
        $this->pendingRoleIds = array_values(array_map('intval', $this->selectedRoleIds));
        unset($data['roles']);

        if ($this->avatarUpload) {
            $this->validate([
                'avatarUpload' => ['image', 'mimes:jpeg,png,webp', 'max:3072'],
            ]);

            $data['avatar'] = $this->avatarUpload->store('avatars', 'r2');
        }

        return $data;
    }

    protected function afterSave(): void
    {
        // Sync roles via Spatie so the pivot table is always correct.
        $this->record->syncRoles($this->pendingRoleIds);

        $fresh = $this->record->fresh();
        $this->deleteReplacedAvatar($fresh);
        $this->avatarUpload = null;
        $this->originalAvatar = $fresh->avatar;
        $newRoles = $fresh->getRoleNames()->sort()->values()->all();

        if ($newRoles !== $this->originalRoles) {
            ActivityLogService::logChange(
                'user.roles_changed',
                $this->record,
                ['roles' => $this->originalRoles],
                ['roles' => $newRoles],
            );
        }

        // When the instructor role is assigned via the admin panel the normal
        // application flow is bypassed, so no InstructorVerification record is
        // created. Without it isVerifiedInstructor() returns false and the user
        // gets a 403 on every instructor API endpoint.
        $this->ensureInstructorVerification($fresh);
    }

    private function deleteReplacedAvatar(User $user): void
    {
        if (
            $this->originalAvatar
            && $this->originalAvatar !== $user->avatar
            && ! str_starts_with($this->originalAvatar, 'http')
        ) {
            Storage::disk('r2')->delete($this->originalAvatar);
        }
    }

    private function ensureInstructorVerification(User $user): void
    {
        if (! $user->hasRole('instructor')) {
            return;
        }

        $verification = InstructorVerification::withTrashed()
            ->where('user_id', $user->id)
            ->first();

        if ($verification) {
            // Restore soft-deleted record and promote to approved if needed.
            if ($verification->trashed()) {
                $verification->restore();
            }
            if ($verification->status !== 'approved') {
                $verification->update([
                    'status'      => 'approved',
                    'reviewed_by' => auth()->id(),
                    'reviewed_at' => now(),
                ]);
            }
        } else {
            InstructorVerification::create([
                'user_id'     => $user->id,
                'status'      => 'approved',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);
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

        // Cut off any active session immediately rather than waiting for their token to expire
        $user->tokens()->delete();

        Mail::to($user->email)->send(new AccountSuspendedMail($user));

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
