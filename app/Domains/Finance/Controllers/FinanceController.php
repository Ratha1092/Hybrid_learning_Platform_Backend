<?php

namespace App\Domains\Finance\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Finance\Models\InstructorWallet;
use App\Domains\Finance\Models\WalletTransaction;
use App\Domains\Finance\Models\PayoutRequest;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    public function wallet(): JsonResponse
    {
        $wallet = InstructorWallet::firstOrCreate(
            ['instructor_id' => auth()->id()],
            ['balance' => 0, 'pending_balance' => 0, 'currency' => 'USD']
        );

        return ApiResponse::success($wallet, 'Wallet retrieved successfully');
    }

    public function earnings(): JsonResponse
    {
        $instructorId = auth()->id();

        $total = (float) DB::table('order_items')
            ->where('instructor_id', $instructorId)
            ->sum('instructor_amount');

        $thisMonth = (float) DB::table('order_items')
            ->where('instructor_id', $instructorId)
            ->where('created_at', '>=', now()->startOfMonth())
            ->sum('instructor_amount');

        $trend = DB::table('order_items')
            ->where('instructor_id', $instructorId)
            ->where('created_at', '>=', now()->subMonths(6)->startOfMonth())
            ->selectRaw("TO_CHAR(created_at, 'YYYY-MM') as month, SUM(instructor_amount) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return ApiResponse::success([
            'total_earned'  => $total,
            'this_month'    => $thisMonth,
            'monthly_trend' => $trend,
        ], 'Earnings retrieved successfully');
    }

    public function transactions(): JsonResponse
    {
        $transactions = WalletTransaction::where('instructor_id', auth()->id())
            ->latest()
            ->paginate(20);

        return ApiResponse::success($transactions, 'Transactions retrieved successfully');
    }

    public function requestPayout(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'amount'         => 'required|numeric|min:10',
            'payment_method' => 'required|string',
            'details'        => 'nullable|array',
        ]);

        $wallet = InstructorWallet::where('instructor_id', auth()->id())->first();

        if (!$wallet || $wallet->balance < $validated['amount']) {
            return ApiResponse::error('Insufficient balance', 422);
        }

        $payout = PayoutRequest::create([
            'instructor_id'  => auth()->id(),
            'amount'         => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'details'        => $validated['details'] ?? null,
            'status'         => 'pending',
        ]);

        return ApiResponse::success($payout, 'Payout request submitted successfully', 201);
    }
}
