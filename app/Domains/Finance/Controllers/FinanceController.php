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
    public function wallet(Request $request): JsonResponse
    {
        $wallet = InstructorWallet::firstOrCreate(
            ['instructor_id' => $request->user()->id],
            ['balance' => 0, 'pending_balance' => 0, 'currency' => 'USD']
        );

        return ApiResponse::success($wallet, 'Wallet retrieved successfully');
    }

    public function earnings(Request $request): JsonResponse
    {
        $instructorId = $request->user()->id;

        $total = (float) DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('order_items.instructor_id', $instructorId)
            ->where('orders.payment_status', 'paid')
            ->sum('order_items.instructor_amount');

        $thisMonth = (float) DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('order_items.instructor_id', $instructorId)
            ->where('orders.payment_status', 'paid')
            ->where('order_items.created_at', '>=', now()->startOfMonth())
            ->sum('order_items.instructor_amount');

        $trend = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('order_items.instructor_id', $instructorId)
            ->where('orders.payment_status', 'paid')
            ->where('order_items.created_at', '>=', now()->subMonths(6)->startOfMonth())
            ->selectRaw("TO_CHAR(order_items.created_at, 'YYYY-MM') as month, SUM(order_items.instructor_amount) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return ApiResponse::success([
            'total_earned'  => $total,
            'this_month'    => $thisMonth,
            'monthly_trend' => $trend,
        ], 'Earnings retrieved successfully');
    }

    public function transactions(Request $request): JsonResponse
    {
        $transactions = WalletTransaction::where(
            'instructor_id',
            $request->user()->id
        )
            ->latest()
            ->paginate(20);

        return ApiResponse::success($transactions, 'Transactions retrieved successfully');
    }

    public function requestPayout(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:10', 'decimal:0,2'],
            'payment_method' => [
                'required',
                'string',
                'in:bank_transfer,momo,acleda,wing',
            ],
            'details' => ['nullable', 'array', 'max:20'],
        ]);

        $payout = DB::transaction(function () use ($request, $validated) {
            $wallet = InstructorWallet::where(
                'instructor_id',
                $request->user()->id
            )
                ->lockForUpdate()
                ->first();

            if (!$wallet || $wallet->balance < $validated['amount']) {
                return null;
            }

            $wallet->decrement('balance', $validated['amount']);

            $payout = PayoutRequest::create([
                'instructor_id' => $request->user()->id,
                'amount' => $validated['amount'],
                'currency' => $wallet->currency,
                'payment_method' => $validated['payment_method'],
                'details' => $validated['details'] ?? null,
                'status' => 'pending',
            ]);

            WalletTransaction::create([
                'instructor_id' => $request->user()->id,
                'amount' => $validated['amount'],
                'type' => 'debit',
                'status' => 'pending',
                'payout_request_id' => $payout->id,
                'description' => 'Payout request',
            ]);

            return $payout;
        });

        if (!$payout) {
            return ApiResponse::error('Insufficient balance', 422);
        }
        return ApiResponse::success($payout, 'Payout request submitted successfully', 201);
    }
}
