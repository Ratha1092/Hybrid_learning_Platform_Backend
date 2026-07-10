<?php

namespace App\Filament\Resources\PayoutAccounts\Pages;

use App\Domains\Auth\Services\ActivityLogService;
use App\Domains\Notifications\Notifications\PayoutAccountRejectedNotification;
use App\Domains\Notifications\Notifications\PayoutAccountVerifiedNotification;
use App\Domains\System\Models\Setting;
use App\Filament\Resources\PayoutAccounts\PayoutAccountResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewPayoutAccount extends ViewRecord
{
    protected static string $resource = PayoutAccountResource::class;

    protected string $view = 'filament.resources.payout-accounts.view-payout-account';

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function approve(): void
    {
        $record = $this->getRecord();

        if ($record->status !== 'pending') {
            return;
        }

        $record->update([
            'status' => 'verified',
            'rejection_reason' => null,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        ActivityLogService::logChange('payout_account.verified', $record);

        if (Setting::get('payout_notification', true)) {
            $record->instructor->notify(new PayoutAccountVerifiedNotification());
        }

        Notification::make()
            ->title('Payout Account Verified')
            ->body("{$record->instructor->name}'s payout account has been verified.")
            ->success()
            ->send();
    }

    public function reject(string $reason): void
    {
        $record = $this->getRecord();

        if ($record->status !== 'pending') {
            return;
        }

        $record->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        ActivityLogService::logChange(
            'payout_account.rejected',
            $record,
            [],
            ['reason' => $reason]
        );

        if (Setting::get('payout_notification', true)) {
            $record->instructor->notify(new PayoutAccountRejectedNotification($reason));
        }

        Notification::make()
            ->title('Payout Account Rejected')
            ->danger()
            ->send();
    }
}
