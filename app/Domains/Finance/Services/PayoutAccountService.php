<?php

namespace App\Domains\Finance\Services;

use App\Domains\Finance\Models\InstructorPayoutAccount;
use App\Domains\Notifications\Notifications\NewPayoutAccountSubmittedNotification;
use App\Domains\System\Models\Setting;
use App\Domains\Users\Models\User;
use App\Jobs\Notifications\NotifyAdminsJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PayoutAccountService
{
    public function save(User $instructor, array $data): InstructorPayoutAccount
    {
        $existing = InstructorPayoutAccount::where('instructor_id', $instructor->id)->first();

        $account = DB::transaction(function () use ($instructor, $data, $existing) {
            $qrPath = $existing?->qr_code_path;

            if (isset($data['qr_code'])) {
                $qrPath = $data['qr_code']->store('payouts/qr-codes', 'r2');

                if ($existing?->qr_code_path) {
                    Storage::disk('r2')->delete($existing->qr_code_path);
                }
            }

            return InstructorPayoutAccount::updateOrCreate(
                ['instructor_id' => $instructor->id],
                [
                    'method' => 'khqr',
                    'account_name' => $data['account_name'],
                    'qr_code_path' => $qrPath,
                    'status' => 'pending',
                    'rejection_reason' => null,
                    'reviewed_by' => null,
                    'reviewed_at' => null,
                ]
            );
        });

        if (Setting::get('payout_notification', true)) {
            NotifyAdminsJob::dispatch(
                NewPayoutAccountSubmittedNotification::class,
                [$account->id, $instructor->name],
                'payout_accounts.approve'
            );
        }

        return $account;
    }
}
