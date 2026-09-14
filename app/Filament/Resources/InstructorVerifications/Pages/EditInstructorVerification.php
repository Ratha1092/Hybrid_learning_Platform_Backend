<?php

namespace App\Filament\Resources\InstructorVerifications\Pages;

use App\Domains\Notifications\Notifications\InstructorApprovedNotification;
use App\Domains\Notifications\Notifications\InstructorRejectedNotification;
use App\Domains\Users\Models\InstructorProfile;
use App\Filament\Resources\InstructorVerifications\InstructorVerificationResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Mail;

class EditInstructorVerification extends EditRecord
{
    protected static string $resource = InstructorVerificationResource::class;

    protected function afterSave(): void
    {
        $record = $this->getRecord();
        $user   = $record->user;

        if ($record->status === 'approved') {
            InstructorProfile::firstOrCreate(['user_id' => $record->user_id]);
            $user->syncRoles(['instructor']);
            $user->notify(new InstructorApprovedNotification());
        }

        if ($record->status === 'rejected') {
            $user->removeRole('instructor');
            $user->notify(new InstructorRejectedNotification($record->rejection_reason ?? ''));
        }
    }

    protected function getRedirectUrl(): string
    {
        return route('filament.admin.pages.instructor-verifications');
    }
}