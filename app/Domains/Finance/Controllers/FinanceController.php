<?php

namespace App\Domains\Finance\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Finance\Models\InstructorWallet;
use App\Domains\Finance\Models\WalletTransaction;
use App\Domains\Finance\Models\PayoutRequest;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    public function wallet()
    {
        $user = auth()->user();
        $wallet = InstructorWallet::where('instructor_id', $user->id)->first();

        if (!$wallet) {
            return ApiResponse::error('Wallet not found', 404);
        }

        return ApiResponse::success($wallet, 'Wallet retrieved successfully');
    }

    public function earnings()
    {
        $user = auth()->user();
        $transactions = WalletTransaction::where('instructor_id', $user->id)
            ->where('type', 'credit')
            ->latest()
            ->paginate(15);

        return ApiResponse::success($transactions, 'Earnings retrieved successfully');
    }

    public function transactions()
    {
        $user = auth()->user();
        $transactions = WalletTransaction::where('instructor_id', $user->id)
            ->latest()
            ->paginate(15);

        return ApiResponse::success($transactions, 'Transactions retrieved successfully');
    }

    public function requestPayout(Request $request)
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'payment_method' => ['required', 'string', 'in:bank_transfer,momo,acleda,wing'],
            'details' => ['required', 'array'],
        ]);

        $user = auth()->user();
        $wallet = InstructorWallet::where('instructor_id', $user->id)->first();

        if (!$wallet) {
            return ApiResponse::error('Wallet not found', 404);
        }

        if ($validated['amount'] > $wallet->balance) {
            return ApiResponse::error('Insufficient balance for payout', 400);
        }

        $payout = PayoutRequest::create([
            'instructor_id' => $user->id,
            'amount' => $validated['amount'],
            'currency' => $wallet->currency,
            'payment_method' => $validated['payment_method'],
            'details' => $validated['details'],
            'status' => 'pending',
            'requested_at' => now(),
        ]);

        return ApiResponse::success($payout, 'Payout request created successfully', 201);
    }
}
