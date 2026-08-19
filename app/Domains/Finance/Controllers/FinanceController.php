<?php

namespace App\Domains\Finance\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Finance\Models\InstructorWallet;
use App\Domains\Finance\Models\PayoutRequest;
use App\Domains\Finance\Models\RevenueShare;
use App\Domains\Finance\Models\WalletTransaction;
use App\Domains\Finance\Services\PayoutService;
use App\Domains\System\Models\Setting;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    public function __construct(private readonly PayoutService $payoutService) {}

    public function wallet(Request $request): JsonResponse
    {
        if (!Setting::get('wallet_enabled', true)) {
            return ApiResponse::error('The instructor wallet system is currently disabled.', 403);
        }

        $wallet = InstructorWallet::firstOrCreate(
            ['instructor_id' => $request->user()->id],
            ['balance' => 0, 'pending_balance' => 0, 'currency' => Setting::get('default_currency', 'USD')]
        );

        $holdDays = (int) Setting::get('payout_hold_period_days', 14);

        // The oldest still-pending share tells us when the next chunk of
        // pending_balance will clear — mirrors the cutoff ReleasePendingBalanceCommand uses.
        $oldestPending = RevenueShare::where('instructor_id', $request->user()->id)
            ->where('status', 'pending')
            ->orderBy('created_at')
            ->first();

        $data = $wallet->toArray();
        $data['hold_period_days'] = $holdDays;
        $data['next_release_at'] = $oldestPending
            ? $oldestPending->created_at->copy()->addDays($holdDays)->toIso8601String()
            : null;

        return ApiResponse::success($data, 'Wallet retrieved successfully');
    }

    public function earnings(Request $request): JsonResponse
    {
        $instructorId = $request->user()->id;

        $data = Cache::remember(
            "finance:{$instructorId}:earnings",
            now()->addMinutes(15),
            function () use ($instructorId) {
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

                return [
                    'total_earned'  => $total,
                    'this_month'    => $thisMonth,
                    'monthly_trend' => $trend,
                ];
            }
        );

        return ApiResponse::success($data, 'Earnings retrieved successfully');
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
        if (!Setting::get('wallet_enabled', true)) {
            return ApiResponse::error('The instructor wallet system is currently disabled.', 403);
        }

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01', 'decimal:0,2'],
        ]);

        try {
            $payout = $this->payoutService->requestPayout($request->user(), (float) $validated['amount']);
        } catch (\RuntimeException $e) {
            return ApiResponse::error($e->getMessage(), 422);
        }

        return ApiResponse::success($payout, 'Payout request submitted successfully', 201);
    }

    public function payoutRequests(Request $request): JsonResponse
    {
        $payouts = PayoutRequest::where('instructor_id', $request->user()->id)
            ->with('receipt:id,payout_request_id,receipt_number')
            ->latest()
            ->paginate(20);

        return ApiResponse::success($payouts, 'Payout requests retrieved successfully');
    }
}
