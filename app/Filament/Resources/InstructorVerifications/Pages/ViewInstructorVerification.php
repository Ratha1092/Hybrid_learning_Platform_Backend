<?php

namespace App\Filament\Resources\InstructorVerifications\Pages;

use App\Domains\Notifications\Notifications\InstructorApprovedNotification;
use App\Domains\Notifications\Notifications\InstructorRejectedNotification;
use App\Domains\Users\Models\InstructorProfile;
use App\Filament\Resources\InstructorVerifications\InstructorVerificationResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Mail;
use App\Domains\Users\Mail\InstructorApprovedMail;
use App\Domains\Users\Mail\InstructorRejectedMail;

class ViewInstructorVerification extends ViewRecord
{
    protected static string $resource = InstructorVerificationResource::class;

    protected string $view = 'filament.resources.instructor-verifications.view-instructor-verification';

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function approve(): void
    {
        $record = $this->getRecord();

        $record->update([
            'status'      => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        $record->user->syncRoles(['instructor']);

        $record->user->notify(new InstructorApprovedNotification());

        Mail::to($record->user->email)
            ->send(new InstructorApprovedMail($record->user));

        InstructorProfile::firstOrCreate(['user_id' => $record->user_id]);

        Notification::make()
            ->title('Instructor Approved')
            ->body("{$record->user->name} has been approved as an instructor.")
            ->success()
            ->send();

        $this->redirect(
            InstructorVerificationResource::getIndexUrl()
        );
    }

    public function reject(string $reason): void
    {
        $record = $this->getRecord();

        $record->update([
            'status'           => 'rejected',
            'rejection_reason' => $reason,
            'reviewed_by'      => auth()->id(),
            'reviewed_at'      => now(),
        ]);

        $record->user->removeRole('instructor');

        $record->user->notify(new InstructorRejectedNotification($reason));

        Mail::to($record->user->email)
            ->send(new InstructorRejectedMail($record->user, $reason));

        Notification::make()
            ->title('Application Rejected')
            ->body('The application has been rejected.')
            ->danger()
            ->send();

        $this->redirect(
            InstructorVerificationResource::getIndexUrl()
        );
    }
}
