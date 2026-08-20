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
        $existing = InstructorPayoutAccount::withTrashed()->where('instructor_id', $instructor->id)->first();

        $account = DB::transaction(function () use ($instructor, $data, $existing) {
            if ($existing?->trashed()) {
                $existing->restore();
            }

            $qrPath = $existing?->qr_code_path;

            if (isset($data['qr_code'])) {
                $qrPath = $data['qr_code']->store('payouts/qr-codes', 'r2');

                if ($existing?->qr_code_path) {
                    Storage::disk('r2')->delete($existing->qr_code_path);
                }
            }

            $autoVerify = Setting::get('auto_verify_payout_accounts', false);

            return InstructorPayoutAccount::updateOrCreate(
                ['instructor_id' => $instructor->id],
                [
                    'method' => 'khqr',
                    'account_name' => $data['account_name'],
                    'qr_code_path' => $qrPath,
                    'status' => $autoVerify ? 'verified' : 'pending',
                    'rejection_reason' => null,
                    'reviewed_by' => null,
                    'reviewed_at' => $autoVerify ? now() : null,
                ]
            );
        });

        // Nothing for an admin to review when it was auto-verified — skip the "needs approval" notice.
        if ($account->status === 'pending' && Setting::get('payout_notification', true)) {
            NotifyAdminsJob::dispatch(
                NewPayoutAccountSubmittedNotification::class,
                [$account->id, $instructor->name],
                'payout_accounts.approve'
            );
        }

        return $account;
    }
}
