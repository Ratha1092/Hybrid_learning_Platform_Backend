<?php

namespace App\Domains\Finance\Services;

use App\Domains\Finance\Models\InstructorPayoutAccount;
use App\Domains\Finance\Models\InstructorWallet;
use App\Domains\Finance\Models\PayoutRequest;
use App\Domains\Finance\Models\WalletTransaction;
use App\Domains\System\Models\Setting;
use App\Domains\Users\Models\User;
use Illuminate\Support\Facades\DB;

class PayoutService
{
    public function requestPayout(User $instructor, float $amount, string $source = 'manual'): PayoutRequest
    {
        $account = InstructorPayoutAccount::where('instructor_id', $instructor->id)
            ->where('status', 'verified')
            ->first();

        if (!$account) {
            throw new \RuntimeException('No verified payout account on file.');
        }

        $minimum = (float) Setting::get('minimum_payout_amount', 50);

        if ($amount < $minimum) {
            throw new \RuntimeException("Amount is below the minimum payout of {$minimum}.");
        }

        return DB::transaction(function () use ($instructor, $account, $amount, $source) {
            $wallet = InstructorWallet::where('instructor_id', $instructor->id)
                ->lockForUpdate()
                ->first();

            if (!$wallet || $wallet->balance < $amount) {
                throw new \RuntimeException('Insufficient balance.');
            }

            $wallet->decrement('balance', $amount);

            $payout = PayoutRequest::create([
                'instructor_id' => $instructor->id,
                'payout_account_id' => $account->id,
                'amount' => $amount,
                'currency' => $wallet->currency,
                'payment_method' => $account->method,
                'source' => $source,
                'details' => [
                    'account_name' => $account->account_name,
                    'account_number' => $account->account_number,
                    'phone_number' => $account->phone_number,
                    'qr_code_path' => $account->qr_code_path,
                ],
                'status' => 'pending',
            ]);

            WalletTransaction::create([
                'instructor_id' => $instructor->id,
                'amount' => $amount,
                'type' => 'debit',
                'status' => 'pending',
                'payout_request_id' => $payout->id,
                'description' => $source === 'monthly_auto' ? 'Monthly automatic payout' : 'Payout request',
            ]);

            return $payout;
        });
    }
}
