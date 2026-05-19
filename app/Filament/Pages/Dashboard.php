<?php

namespace App\Filament\Pages;

use BackedEnum;
use App\Domains\Courses\Models\Course;
use App\Domains\Courses\Models\Lesson;
use App\Domains\Courses\Models\Section;
use App\Domains\Users\Models\User;
use App\Domains\Orders\Models\Order;
use App\Domains\Payments\Enums\PaymentStatus;
use App\Domains\Payments\Models\Payment;
use App\Domains\Finance\Models\InstructorWallet;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Carbon;
use App\Domains\Payments\Enums\PaymentGateway;

class Dashboard extends BaseDashboard
{
    protected string $view = 'filament.pages.dashboard';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?int $navigationSort = -2;

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return '';
    }
    protected function getHeaderWidgets(): array
    {
        return [];
    }

    protected function getFooterWidgets(): array
    {
        return [];
    }

    protected function getDateRange(): array
    {
        $preset   = request('preset', 'this_month');
        $dateFrom = request('date_from');
        $dateTo   = request('date_to');

        if ($preset !== 'custom') {
            [$dateFrom, $dateTo] = match ($preset) {
                'today'        => [now()->startOfDay(), now()->endOfDay()],
                'yesterday'    => [now()->subDay()->startOfDay(), now()->subDay()->endOfDay()],
                'this_week'    => [now()->startOfWeek(), now()->endOfWeek()],
                'last_week'    => [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()],
                'last_30'      => [now()->subDays(29)->startOfDay(), now()->endOfDay()],
                'this_month'   => [now()->startOfMonth(), now()->endOfMonth()],
                'last_month'   => [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()],
                'this_quarter' => [now()->startOfQuarter(), now()->endOfQuarter()],
                'this_year'    => [now()->startOfYear(), now()->endOfYear()],
                'all_time'     => [null, null],
                default        => [now()->startOfMonth(), now()->endOfMonth()],
            };
        } else {
            $dateFrom = $dateFrom ? Carbon::parse($dateFrom)->startOfDay() : null;
            $dateTo   = $dateTo   ? Carbon::parse($dateTo)->endOfDay()   : null;
        }

        return [$dateFrom, $dateTo];
    }

    protected function applyDateRange($query, $column, $from, $to)
    {
        if ($from) $query->where($column, '>=', $from);
        if ($to)   $query->where($column, '<=', $to);
        return $query;
    }

    public function getViewData(): array
    {
        [$from, $to] = $this->getDateRange();

        $preset          = request('preset', 'this_month');
        $gatewayFilter   = request('gateway', 'all');
        $statusFilter    = request('status', 'all');
        $courseStatus    = request('course_status', 'all');

        $paidStatuses = [PaymentStatus::Paid->value, PaymentStatus::Completed->value];

        $payBase = function () use ($from, $to, $gatewayFilter, $statusFilter) {
            $q = Payment::query();
            $this->applyDateRange($q, 'created_at', $from, $to);
            if ($gatewayFilter !== 'all') {
                $q->where('payment_gateway', $gatewayFilter);
            }
            if ($statusFilter !== 'all') {
                $q->where('status', $statusFilter);
            }
            return $q;
        };

        $paidBase = function () use ($from, $to, $gatewayFilter, $paidStatuses) {
            $q = Payment::query()->whereIn('status', $paidStatuses);
            $this->applyDateRange($q, 'paid_at', $from, $to);
            if ($gatewayFilter !== 'all') {
                $q->where('payment_gateway', $gatewayFilter);
            }
            return $q;
        };

        // ── People ──────────────────────────────────────────────────────
        $studentQ = User::where('role', 'student');
        $this->applyDateRange($studentQ, 'created_at', $from, $to);
        $totalStudents = $studentQ->count();

        $newStudentsThisMonth = User::where('role', 'student')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $totalInstructors     = User::where('role', 'instructor')->count();
        $pendingVerifications = User::where('role', 'instructor')->where('is_verified', false)->count();

        $pendingInstructors = User::where('role', 'instructor')
            ->where('is_verified', false)
            ->latest()->take(5)->get();

        // ── Courses ──────────────────────────────────────────────────────
        $courseQ = Course::query();
        $this->applyDateRange($courseQ, 'created_at', $from, $to);
        if ($courseStatus !== 'all') {
            if ($courseStatus === 'published')   $courseQ->where('is_published', true);
            if ($courseStatus === 'unpublished') $courseQ->where('is_published', false);
        }
        $totalCourses = $courseQ->count();

        $newCoursesThisMonth = Course::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $totalSections = Section::count();
        $totalLessons  = Lesson::count();

        $recentCourses = Course::with(['instructor', 'category'])
            ->when($courseStatus !== 'all', function ($q) use ($courseStatus) {
                if ($courseStatus === 'published')   $q->where('is_published', true);
                if ($courseStatus === 'unpublished') $q->where('is_published', false);
            })
            ->latest()->take(6)->get();

        $recentUsers = User::latest()->take(6)->get();

        // ── Orders ───────────────────────────────────────────────────────
        $orderQ = Order::query();
        $this->applyDateRange($orderQ, 'created_at', $from, $to);
        $totalOrders = $orderQ->count();

        $ordersThisMonth = Order::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // ── Finance ──────────────────────────────────────────────────────
        $totalRevenue     = $paidBase()->sum('amount');
        $revenueThisMonth = $paidBase()
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('amount');

        $completedPayments = $paidBase()->count();
        $pendingPayments   = $payBase()->where('status', PaymentStatus::Pending->value)->count();
        $failedPayments    = $payBase()->where('status', PaymentStatus::Failed->value)->count();

        $recentOrders = Order::with(['user', 'payment'])
            ->latest()->take(6)->get();

        $recentPayments = Payment::with(['order'])
            ->when($gatewayFilter !== 'all', fn($q) => $q->where('payment_gateway', $gatewayFilter))
            ->when($statusFilter !== 'all',  fn($q) => $q->where('status', $statusFilter))
            ->latest()->take(6)->get();

        $totalInstructorBalance = InstructorWallet::sum('balance');
        $totalPendingBalance    = InstructorWallet::sum('pending_balance');

        // ── Trends ───────────────────────────────────────────────────────
        $trendBase = $to ?? now();

        $studentTrend = collect(range(5, 0))->map(function ($mo) use ($trendBase) {
            $date = (clone $trendBase)->subMonths($mo);
            return [
                'month' => $date->format('M'),
                'count' => User::where('role', 'student')
                    ->whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->count(),
            ];
        });

        $revenueTrend = collect(range(5, 0))->map(function ($mo) use ($trendBase, $paidStatuses, $gatewayFilter) {
            $date = (clone $trendBase)->subMonths($mo);
            return [
                'month'   => $date->format('M'),
                'revenue' => Payment::whereIn('status', $paidStatuses)
                    ->whereMonth('paid_at', $date->month)
                    ->whereYear('paid_at', $date->year)
                    ->when($gatewayFilter !== 'all', fn($q) => $q->where('payment_gateway', $gatewayFilter))
                    ->sum('amount'),
                'orders'  => Order::whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->count(),
            ];
        });

        // ── Breakdowns ───────────────────────────────────────────────────
        $paymentStatusBreakdown = [
            'completed' => $paidBase()->count(),
            'pending'   => $payBase()->where('status', PaymentStatus::Pending->value)->count(),
            'failed'    => $payBase()->where('status', PaymentStatus::Failed->value)->count(),
            'refunded'  => $payBase()->where('status', PaymentStatus::Refunded->value)->count(),
        ];

        $paymentGatewayBreakdown = [
            'bakong' => Payment::whereIn('status', $paidStatuses)
                ->where('payment_gateway', PaymentGateway::Bakong->value)
                ->when($from, fn($q) => $q->where('paid_at', '>=', $from))
                ->when($to,   fn($q) => $q->where('paid_at', '<=', $to))
                ->sum('amount'),

            'khqr' => Payment::whereIn('status', $paidStatuses)
                ->where('payment_gateway', PaymentGateway::Khqr->value)
                ->when($from, fn($q) => $q->where('paid_at', '>=', $from))
                ->when($to,   fn($q) => $q->where('paid_at', '<=', $to))
                ->sum('amount'),

            'aba' => Payment::whereIn('status', $paidStatuses)
                ->where('payment_gateway', PaymentGateway::Aba->value)
                ->when($from, fn($q) => $q->where('paid_at', '>=', $from))
                ->when($to,   fn($q) => $q->where('paid_at', '<=', $to))
                ->sum('amount'),

            'stripe' => Payment::whereIn('status', $paidStatuses)
                ->where('payment_gateway', PaymentGateway::Stripe->value)
                ->when($from, fn($q) => $q->where('paid_at', '>=', $from))
                ->when($to,   fn($q) => $q->where('paid_at', '<=', $to))
                ->sum('amount'),

            'paypal' => Payment::whereIn('status', $paidStatuses)
                ->where('payment_gateway', PaymentGateway::Paypal->value)
                ->when($from, fn($q) => $q->where('paid_at', '>=', $from))
                ->when($to,   fn($q) => $q->where('paid_at', '<=', $to))
                ->sum('amount'),
        ];

        return [
            // Meta — pass active filters back to the view
            'activePreset'       => $preset,
            'activeDateFrom'     => $from ? $from->format('Y-m-d') : null,
            'activeDateTo'       => $to   ? $to->format('Y-m-d')   : null,
            'activeGateway'      => $gatewayFilter,
            'activeStatus'       => $statusFilter,
            'activeCourseStatus' => $courseStatus,

            // People
            'totalStudents'           => $totalStudents,
            'totalInstructors'        => $totalInstructors,
            'pendingVerifications'    => $pendingVerifications,
            'newStudentsThisMonth'    => $newStudentsThisMonth,
            'recentUsers'             => $recentUsers,
            'pendingInstructors'      => $pendingInstructors,
            'studentTrend'            => $studentTrend,

            // Courses
            'totalCourses'        => $totalCourses,
            'totalSections'       => $totalSections,
            'totalLessons'        => $totalLessons,
            'newCoursesThisMonth' => $newCoursesThisMonth,
            'recentCourses'       => $recentCourses,

            // Finance
            'totalOrders'             => $totalOrders,
            'totalRevenue'            => $totalRevenue,
            'completedPayments'       => $completedPayments,
            'pendingPayments'         => $pendingPayments,
            'failedPayments'          => $failedPayments,
            'ordersThisMonth'         => $ordersThisMonth,
            'revenueThisMonth'        => $revenueThisMonth,
            'recentOrders'            => $recentOrders,
            'recentPayments'          => $recentPayments,
            'totalInstructorBalance'  => $totalInstructorBalance,
            'totalPendingBalance'     => $totalPendingBalance,
            'revenueTrend'            => $revenueTrend,
            'paymentStatusBreakdown'  => $paymentStatusBreakdown,
            'paymentGatewayBreakdown' => $paymentGatewayBreakdown,
        ];
    }
}