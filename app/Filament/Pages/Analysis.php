<?php

namespace App\Filament\Pages;

use BackedEnum;
use App\Domains\Courses\Models\Course;
use App\Domains\Courses\Models\Lesson;
use App\Domains\Courses\Models\Section;
use App\Domains\Users\Models\User;
use App\Domains\Orders\Models\Order;
use App\Domains\Payments\Enums\PaymentStatus;
use App\Domains\Payments\Enums\PaymentGateway;
use App\Domains\Payments\Models\Payment;
use App\Domains\Finance\Models\InstructorWallet;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;

class Analysis extends Page
{
    protected string $view = 'filament.pages.analysis';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Analysis';
    protected static string|\UnitEnum|null $navigationGroup = 'Overview';
    protected static ?int $navigationSort = -1;

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return '';
    }

    protected function getDateRange(): array
    {
        $preset   = request('preset', 'last_6m');
        $dateFrom = request('date_from');
        $dateTo   = request('date_to');

        if ($preset !== 'custom') {
            [$dateFrom, $dateTo] = match ($preset) {
                'today'        => [now()->startOfDay(), now()->endOfDay()],
                'yesterday'    => [now()->subDay()->startOfDay(), now()->subDay()->endOfDay()],
                'this_week'    => [now()->startOfWeek(), now()->endOfWeek()],
                'last_week'    => [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()],
                'last_30'      => [now()->subDays(29)->startOfDay(), now()->endOfDay()],
                'last_6m'      => [now()->subMonths(6)->startOfDay(), now()->endOfDay()],
                'this_month'   => [now()->startOfMonth(), now()->endOfMonth()],
                'last_month'   => [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()],
                'this_quarter' => [now()->startOfQuarter(), now()->endOfQuarter()],
                'this_year'    => [now()->startOfYear(), now()->endOfYear()],
                'all_time'     => [null, null],
                default        => [now()->subMonths(6)->startOfDay(), now()->endOfDay()],
            };
        } else {
            $dateFrom = $dateFrom ? Carbon::parse($dateFrom)->startOfDay() : null;
            $dateTo   = $dateTo   ? Carbon::parse($dateTo)->endOfDay()   : null;
        }

        return [$dateFrom, $dateTo];
    }

    protected function getViewData(): array
    {
        [$from, $to] = $this->getDateRange();

        $preset        = request('preset', 'last_6m');
        $gatewayFilter = request('gateway', 'all');

        $paidStatuses = [PaymentStatus::Paid->value, PaymentStatus::Completed->value];

        // Payment query helpers
        $payBase = function () use ($from, $to, $gatewayFilter) {
            $q = Payment::query();
            if ($from) $q->where('created_at', '>=', $from);
            if ($to)   $q->where('created_at', '<=', $to);
            if ($gatewayFilter !== 'all') $q->where('payment_gateway', $gatewayFilter);
            return $q;
        };

        $paidBase = function () use ($from, $to, $gatewayFilter, $paidStatuses) {
            $q = Payment::query()->whereIn('status', $paidStatuses);
            if ($from) $q->where('paid_at', '>=', $from);
            if ($to)   $q->where('paid_at', '<=', $to);
            if ($gatewayFilter !== 'all') $q->where('payment_gateway', $gatewayFilter);
            return $q;
        };

        // ── KPIs ─────────────────────────────────────────────────────────
        $totalRevenue      = (float) $paidBase()->sum('amount');
        $totalOrders       = Order::query()
            ->when($from, fn($q) => $q->where('created_at', '>=', $from))
            ->when($to,   fn($q) => $q->where('created_at', '<=', $to))
            ->count();

        $completedPayments = $paidBase()->count();
        $pendingPayments   = $payBase()->where('status', PaymentStatus::Pending->value)->count();
        $failedPayments    = $payBase()->where('status', PaymentStatus::Failed->value)->count();
        $refundedPayments  = $payBase()->where('status', PaymentStatus::Refunded->value)->count();
        $allPayments       = max($completedPayments + $pendingPayments + $failedPayments + $refundedPayments, 1);
        $successRate       = round(($completedPayments / $allPayments) * 100);

        $totalStudents = User::where('role', 'student')
            ->when($from, fn($q) => $q->where('created_at', '>=', $from))
            ->when($to,   fn($q) => $q->where('created_at', '<=', $to))
            ->count();

        // ── Platform content (all-time) ───────────────────────────────────
        $totalCourses         = Course::count();
        $publishedCourses     = Course::where('is_published', true)->count();
        $totalInstructors     = User::where('role', 'instructor')->count();
        $totalLessons         = Lesson::count();
        $totalSections        = Section::count();
        $pendingVerifications = User::where('role', 'instructor')->where('is_verified', false)->count();
        $pendingCourseReviews = Course::where('status', Course::STATUS_PENDING)->count();

        $totalInstructorBalance = (float) InstructorWallet::sum('balance');
        $totalPendingBalance    = (float) InstructorWallet::sum('pending_balance');

        // ── 6-month trends ────────────────────────────────────────────────
        $trendBase = $to ?? now();

        $studentTrend = collect(range(5, 0))->map(function ($mo) use ($trendBase) {
            $date = (clone $trendBase)->subMonths($mo);
            return [
                'month' => $date->format('M'),
                'count' => User::where('role', 'student')
                    ->whereMonth('created_at', $date->month)
                    ->whereYear('created_at',  $date->year)
                    ->count(),
            ];
        });

        $revenueTrend = collect(range(5, 0))->map(function ($mo) use ($trendBase, $paidStatuses, $gatewayFilter) {
            $date = (clone $trendBase)->subMonths($mo);
            return [
                'month'   => $date->format('M'),
                'revenue' => (float) Payment::whereIn('status', $paidStatuses)
                    ->whereMonth('paid_at', $date->month)
                    ->whereYear('paid_at',  $date->year)
                    ->when($gatewayFilter !== 'all', fn($q) => $q->where('payment_gateway', $gatewayFilter))
                    ->sum('amount'),
                'orders'  => Order::whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->count(),
            ];
        });

        // ── Payment status breakdown ───────────────────────────────────────
        $paymentStatusBreakdown = [
            'Completed' => $completedPayments,
            'Pending'   => $pendingPayments,
            'Failed'    => $failedPayments,
            'Refunded'  => $refundedPayments,
        ];
        $statusTotal = max(array_sum($paymentStatusBreakdown), 1);

        // ── Gateway breakdown ─────────────────────────────────────────────
        $gwData = [
            'Bakong' => (float) Payment::whereIn('status', $paidStatuses)->where('payment_gateway', PaymentGateway::Bakong->value)->when($from, fn($q) => $q->where('paid_at', '>=', $from))->when($to, fn($q) => $q->where('paid_at', '<=', $to))->sum('amount'),
            'KHQR'   => (float) Payment::whereIn('status', $paidStatuses)->where('payment_gateway', PaymentGateway::Khqr->value)->when($from, fn($q) => $q->where('paid_at', '>=', $from))->when($to, fn($q) => $q->where('paid_at', '<=', $to))->sum('amount'),
            'ABA'    => (float) Payment::whereIn('status', $paidStatuses)->where('payment_gateway', PaymentGateway::Aba->value)->when($from, fn($q) => $q->where('paid_at', '>=', $from))->when($to, fn($q) => $q->where('paid_at', '<=', $to))->sum('amount'),
            'Stripe' => (float) Payment::whereIn('status', $paidStatuses)->where('payment_gateway', PaymentGateway::Stripe->value)->when($from, fn($q) => $q->where('paid_at', '>=', $from))->when($to, fn($q) => $q->where('paid_at', '<=', $to))->sum('amount'),
            'PayPal' => (float) Payment::whereIn('status', $paidStatuses)->where('payment_gateway', PaymentGateway::Paypal->value)->when($from, fn($q) => $q->where('paid_at', '>=', $from))->when($to, fn($q) => $q->where('paid_at', '<=', $to))->sum('amount'),
        ];

        $paymentGatewayBreakdown = collect($gwData)->sortByDesc(fn($v) => $v);
        $gtTotal = max($paymentGatewayBreakdown->sum(), 1);

        return [
            // Filters
            'activePreset'  => $preset,
            'activeGateway' => $gatewayFilter,

            // KPIs
            'totalRevenue'     => $totalRevenue,
            'totalOrders'      => $totalOrders,
            'totalStudents'    => $totalStudents,
            'successRate'      => $successRate,

            // Payment breakdown
            'completedPayments'      => $completedPayments,
            'pendingPayments'        => $pendingPayments,
            'failedPayments'         => $failedPayments,
            'refundedPayments'       => $refundedPayments,
            'paymentStatusBreakdown' => $paymentStatusBreakdown,
            'statusTotal'            => $statusTotal,

            // Gateway breakdown
            'paymentGatewayBreakdown' => $paymentGatewayBreakdown,
            'gtTotal'                 => $gtTotal,

            // Content (all-time)
            'totalCourses'          => $totalCourses,
            'publishedCourses'      => $publishedCourses,
            'totalInstructors'      => $totalInstructors,
            'totalLessons'          => $totalLessons,
            'totalSections'         => $totalSections,
            'pendingVerifications'  => $pendingVerifications,
            'pendingCourseReviews'  => $pendingCourseReviews,
            'totalInstructorBalance'=> $totalInstructorBalance,
            'totalPendingBalance'   => $totalPendingBalance,

            // Trends
            'studentTrend' => $studentTrend,
            'revenueTrend' => $revenueTrend,
        ];
    }
}
