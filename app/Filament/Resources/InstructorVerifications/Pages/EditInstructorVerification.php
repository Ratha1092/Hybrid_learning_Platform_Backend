<?php

namespace App\Filament\Resources\InstructorVerifications\Pages;

use App\Domains\Users\Models\InstructorProfile;
use App\Domains\Notifications\Notifications\InstructorApprovedNotification;
use App\Domains\Notifications\Notifications\InstructorRejectedNotification;
use App\Domains\Users\Mail\InstructorApprovedMail;
use App\Domains\Users\Mail\InstructorRejectedMail;
use App\Filament\Resources\InstructorVerifications\InstructorVerificationResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Mail;

class EditInstructorVerification extends EditRecord
{
    protected static string $resource = InstructorVerificationResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getIndexUrl();
    }

    protected function afterSave(): void
    {
        $record = $this->record;
        $user = $record->user;

        if ($record->status === 'approved') {
            $user->update([
                'role' => 'instructor',
                'instructor_status' => 'verified',
            ]);

            $user->syncRoles(['instructor']);

            if (!$record->reviewed_by) {
                $record->update([
                    'reviewed_by' => auth()->id(),
                    'reviewed_at' => now(),
                ]);
            }

            InstructorProfile::firstOrCreate(['user_id' => $user->id]);

            $user->notify(new InstructorApprovedNotification());
            Mail::to($user->email)->send(new InstructorApprovedMail($user));

        } elseif ($record->status === 'rejected') {
            $user->update(['instructor_status' => 'rejected']);

            if (!$record->reviewed_by) {
                $record->update([
                    'reviewed_by' => auth()->id(),
                    'reviewed_at' => now(),
                ]);
            }

            $reason = $record->rejection_reason ?? '';
            $user->notify(new InstructorRejectedNotification($reason));
            Mail::to($user->email)->send(new InstructorRejectedMail($user, $reason));
        }
    }
}