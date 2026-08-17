<?php

namespace App\Filament\Pages;

use BackedEnum;
use App\Domains\Courses\Models\Course;
use App\Domains\Courses\Models\Lesson;
use App\Domains\Courses\Models\Section;
use App\Domains\Users\Models\User;
use App\Domains\Users\Models\InstructorVerification;
use App\Domains\Orders\Models\Order;
use App\Domains\Orders\Enums\OrderPaymentStatus;
use App\Domains\Orders\Enums\OrderStatus;
use App\Domains\Payments\Enums\PaymentStatus;
use App\Domains\Payments\Models\Payment;
use App\Domains\Finance\Models\InstructorWallet;
use App\Domains\Finance\Models\PayoutRequest;
use App\Domains\Learning\Models\Enrollment;
use App\Domains\Reports\Concerns\HasScheduleAction;
use App\Domains\Reports\Contracts\Schedulable;
use App\Domains\System\Models\Setting;
use App\Support\Concerns\HasDateRangePresets;
use App\Support\CsvExporter;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Domains\Payments\Enums\PaymentGateway;

class Dashboard extends BaseDashboard implements Schedulable
{
    use HasDateRangePresets;
    use HasScheduleAction;

    protected string $view = 'filament.pages.dashboard';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Dashboard';
    protected static string|\UnitEnum|null $navigationGroup = 'Overview';
    protected static ?int $navigationSort = -2;

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return '';
    }

    protected function getHeaderWidgets(): array { return []; }
    protected function getFooterWidgets(): array { return []; }

    //  Schedulable contract 

    public static function reportKey(): string { return 'executive'; }
    public static function reportLabel(): string { return 'Executive Dashboard'; }
    public static function pdfView(): string { return 'reports.pdf.executive-report'; }

    public static function filtersSummary(array $filters): string
    {
        $preset = $filters['preset'] ?? 'this_month';
        return static::dateRangePresetOptions()[$preset] ?? ucfirst($preset);
    }

    public static function csvHeaderAndRows(array $filters): array
    {
        $data = static::buildDataFromFilters($filters);
        return [
            ['Metric', 'Value'],
            [
                ['Total Students',    $data['totalStudents']],
                ['Total Instructors', $data['totalInstructors']],
                ['Total Courses',     $data['totalCourses']],
                ['Total Orders',      $data['totalOrders']],
                ['Total Revenue',     number_format((float) $data['totalRevenue'], 2)],
            ],
        ];
    }

    public static function pdfViewData(array $filters): array
    {
        $data = static::buildDataFromFilters($filters);
        return [
            'siteName'       => Setting::get('site_name', config('app.name')),
            'title'          => 'Executive Dashboard',
            'filtersSummary' => static::filtersSummary($filters),
            'data'           => $data,
        ];
    }

    public function exportCsv(): void
    {
        [$header, $rows] = static::csvHeaderAndRows($this->currentFilters());
        $this->dispatch('download-csv',
            content:  CsvExporter::build($header, $rows),
            filename: 'executive-dashboard-' . now()->format('Y-m-d') . '.csv',
        );
    }

    private function currentFilters(): array
    {
        return [
            'preset'        => request('preset', 'this_month'),
            'date_from'     => request('date_from'),
            'date_to'       => request('date_to'),
            'gateway'       => request('gateway', 'all'),
            'status'        => request('status', 'all'),
            'course_status' => request('course_status', 'all'),
        ];
    }

    private static function buildDataFromFilters(array $filters): array
    {
        [$from, $to] = static::resolvePreset(
            $filters['preset'] ?? 'this_month', 'this_month',
            $filters['date_from'] ?? null, $filters['date_to'] ?? null,
        );
        return (new self())->buildViewData(
            $from, $to,
            $filters['preset']        ?? 'this_month',
            $filters['gateway']       ?? 'all',
            $filters['status']        ?? 'all',
            $filters['course_status'] ?? 'all',
        );
    }

    //
    // View data
    //

    public function getViewData(): array
    {
        [$from, $to]   = $this->resolveDateRange('this_month');
        $preset        = request('preset', 'this_month');
        $gatewayFilter = request('gateway', 'all');
        $statusFilter  = request('status', 'all');
        $courseStatus  = request('course_status', 'all');

        $fromKey  = $from ? $from->format('Ymd') : 'null';
        $toKey    = $to   ? $to->format('Ymd')   : 'null';
        $cacheKey = "dashboard.v2.{$preset}.{$gatewayFilter}.{$statusFilter}.{$courseStatus}.{$fromKey}.{$toKey}";

        return Cache::tags(['dashboard'])->remember($cacheKey, 300, function () use (
            $from, $to, $preset, $gatewayFilter, $statusFilter, $courseStatus
        ) {
            return $this->buildViewData($from, $to, $preset, $gatewayFilter, $statusFilter, $courseStatus);
        });
    }

    private function buildViewData($from, $to, $preset, $gatewayFilter, $statusFilter, $courseStatus): array
    {
        $paidStatuses = [PaymentStatus::Paid->value, PaymentStatus::Completed->value];

        //  Base query builders 
        $payBase = function () use ($from, $to, $gatewayFilter, $statusFilter) {
            $q = Payment::query();
            $this->applyDateRange($q, 'created_at', $from, $to);
            if ($gatewayFilter !== 'all') $q->where('payment_gateway', $gatewayFilter);
            if ($statusFilter  !== 'all') $q->where('status', $statusFilter);
            return $q;
        };

        $paidBase = function () use ($from, $to, $gatewayFilter, $paidStatuses) {
            $q = Payment::query()->whereIn('status', $paidStatuses);
            $this->applyDateRange($q, 'paid_at', $from, $to);
            if ($gatewayFilter !== 'all') $q->where('payment_gateway', $gatewayFilter);
            return $q;
        };

        //  People
        $studentQ = User::role('student');
        $this->applyDateRange($studentQ, 'created_at', $from, $to);
        $totalStudents    = $studentQ->count();
        $totalInstructors = User::role('instructor')->count();

        // Students growth vs last period
        $newStudentsToday      = User::role('student')->whereDate('created_at', today())->count();
        $pendingVerifications  = InstructorVerification::pending()->count();
        $pendingInstructors    = User::whereHas('instructorVerification', fn($q) => $q->where('status', 'pending'))
            ->latest()->take(5)->get();
        $pendingCourseReviews  = Course::where('status', Course::STATUS_PENDING)->count();
        $newUsersToday         = User::whereDate('created_at', today())->count();

        //  Courses
        $courseQ = Course::query();
        $this->applyDateRange($courseQ, 'created_at', $from, $to);
        if ($courseStatus !== 'all') {
            if ($courseStatus === 'published')   $courseQ->where('is_published', true);
            if ($courseStatus === 'unpublished') $courseQ->where('is_published', false);
        }
        $totalCourses          = $courseQ->count();
        $publishedCoursesCount = Course::where('is_published', true)->count();
        $newCoursesThisMonth   = $this->applyDateRange(Course::query(), 'created_at', $from, $to)->count();
        $totalSections         = Section::count();
        $totalLessons          = Lesson::count();

        $recentCourses = Course::with(['instructor', 'category'])
            ->when($courseStatus !== 'all', function ($q) use ($courseStatus) {
                if ($courseStatus === 'published')   $q->where('is_published', true);
                if ($courseStatus === 'unpublished') $q->where('is_published', false);
            })
            ->latest()->take(6)->get();

        //  Orders & Finance 
        $orderQ = Order::query();
        $this->applyDateRange($orderQ, 'created_at', $from, $to);
        $totalOrders   = $orderQ->count();
        $totalRevenue  = $paidBase()->sum('amount');
        $platformRevenue   = round((float) $totalRevenue * 0.20, 2);
        $instructorRevenue = round((float) $totalRevenue * 0.80, 2);

        $completedPayments = $paidBase()->count();
        $pendingPayments   = $payBase()->where('status', PaymentStatus::Pending->value)->count();
        $failedPayments    = $payBase()->where('status', PaymentStatus::Failed->value)->count();
        $failedPaymentsToday = Payment::whereIn('status', [PaymentStatus::Failed->value, PaymentStatus::Expired->value])
            ->whereDate('created_at', today())->count();

        $newOrdersToday = Order::whereDate('created_at', today())->count();

        $recentOrdersQ = Order::with(['user', 'payment', 'items.course']);
        $this->applyDateRange($recentOrdersQ, 'created_at', $from, $to);
        $recentOrders = $recentOrdersQ->latest()->take(5)->get();

        $recentPaymentsQ = Payment::with(['order'])
            ->when($gatewayFilter !== 'all', fn($q) => $q->where('payment_gateway', $gatewayFilter))
            ->when($statusFilter  !== 'all', fn($q) => $q->where('status', $statusFilter));
        $this->applyDateRange($recentPaymentsQ, 'created_at', $from, $to);
        $recentPayments = $recentPaymentsQ->latest()->take(6)->get();

        $totalInstructorBalance = InstructorWallet::sum('balance');
        $totalPendingBalance    = InstructorWallet::sum('pending_balance');

        //  Payouts
        $pendingPayoutsCount = PayoutRequest::where('status', 'pending')->count();

        //  Enrollments
        [$prevFrom, $prevTo] = $this->previousPeriodRange($from, $to);

        $enrollmentsThisMonth = $this->applyDateRange(Enrollment::query(), 'enrolled_at', $from, $to)->count();
        $enrollmentsLastMonth = $prevFrom
            ? $this->applyDateRange(Enrollment::query(), 'enrolled_at', $prevFrom, $prevTo)->count()
            : 0;
        $enrollmentGrowth = $enrollmentsLastMonth > 0
            ? round(($enrollmentsThisMonth - $enrollmentsLastMonth) / $enrollmentsLastMonth * 100, 1)
            : 0;

        $avgCompletionRate = (int) round(
            $this->applyDateRange(
                Enrollment::whereIn('status', ['active', 'completed']), 'enrolled_at', $from, $to
            )->avg('progress_percentage') ?? 0
        );
        $avgCompletionRatePrev = $prevFrom
            ? (int) round(
                $this->applyDateRange(
                    Enrollment::whereIn('status', ['active', 'completed']), 'enrolled_at', $prevFrom, $prevTo
                )->avg('progress_percentage') ?? 0
            )
            : 0;
        $completionRateGrowth = $avgCompletionRatePrev > 0
            ? round($avgCompletionRate - $avgCompletionRatePrev, 1)
            : 0;

        //  Popular courses (by enrollments)
        $revenueDateSql = '';
        $revenueBindings = [];
        if ($from) {
            $revenueDateSql   .= ' AND o.created_at >= ?';
            $revenueBindings[] = $from;
        }
        if ($to) {
            $revenueDateSql   .= ' AND o.created_at < ?';
            $revenueBindings[] = $to->copy()->addDay()->startOfDay();
        }

        $popularCourses = Course::with('instructor')
            ->where('is_published', true)
            ->withCount(['enrollments' => fn ($q) => $this->applyDateRange($q, 'created_at', $from, $to)])
            ->selectRaw("courses.*, (
                SELECT COALESCE(SUM(oi.instructor_amount), 0)
                FROM order_items oi
                JOIN orders o ON o.id = oi.order_id
                WHERE oi.course_id = courses.id AND o.payment_status = 'paid'{$revenueDateSql}
            ) as course_revenue", $revenueBindings)
            ->orderByDesc('enrollments_count')
            ->take(5)
            ->get();

        //  Low rated courses (avg < 3, reviews in range)
        $lowRatedCourses = Course::with('instructor')
            ->where('is_published', true)
            ->withAvg(['reviews' => fn ($q) => $this->applyDateRange($q, 'created_at', $from, $to)], 'rating')
            ->withCount(['reviews' => fn ($q) => $this->applyDateRange($q, 'created_at', $from, $to)])
            ->whereHas('reviews', fn ($q) => $this->applyDateRange($q, 'created_at', $from, $to))
            ->get()
            ->filter(fn($c) => ($c->reviews_avg_rating ?? 5) < 3)
            ->sortBy('reviews_avg_rating')
            ->take(5)
            ->values();

        //  Top instructors by revenue
        $topInstructors = $this->buildTopInstructors($from, $to);

        //  System health
        try {
            $queueJobsPending = DB::table('jobs')->count();
        } catch (\Throwable) {
            $queueJobsPending = 0;
        }
        try {
            $failedJobsCount = DB::table('failed_jobs')->count();
        } catch (\Throwable) {
            $failedJobsCount = 0;
        }

        // Redis status
        try {
            \Illuminate\Support\Facades\Redis::ping();
            $redisStatus = 'connected';
        } catch (\Throwable) {
            $redisStatus = 'disconnected';
        }

        // Server uptime
        try {
            $uptimeRaw     = (float) explode(' ', file_get_contents('/proc/uptime'))[0];
            $uptimeDays    = (int) floor($uptimeRaw / 86400);
            $uptimeHours   = (int) floor(($uptimeRaw % 86400) / 3600);
            $uptimeMinutes = (int) floor(($uptimeRaw % 3600) / 60);
            $serverUptime  = "{$uptimeDays}d {$uptimeHours}h {$uptimeMinutes}m";
        } catch (\Throwable) {
            $serverUptime = 'N/A';
        }

        //  Revenue trends
        $trendBase   = $to ?? now();
        $studentTrend = collect(range(5, 0))->map(function ($mo) use ($trendBase) {
            $date = (clone $trendBase)->subMonths($mo);
            return [
                'month' => $date->format('M'),
                'count' => User::role('student')
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

        //  Revenue chart (4 periods)
        $revenueChartData = $this->buildRevenueChartData($paidStatuses, $gatewayFilter, $to);

        //  Payment breakdowns 
        $paymentStatusBreakdown = [
            'completed' => $paidBase()->count(),
            'pending'   => $payBase()->where('status', PaymentStatus::Pending->value)->count(),
            'failed'    => $payBase()->where('status', PaymentStatus::Failed->value)->count(),
        ];

        $paymentGatewayBreakdown = collect([
            'bakong' => PaymentGateway::Bakong,
            'khqr'   => PaymentGateway::Khqr,
            'aba'    => PaymentGateway::Aba,
            'stripe' => PaymentGateway::Stripe,
            'paypal' => PaymentGateway::Paypal,
        ])->map(fn($gw) =>
            Payment::whereIn('status', $paidStatuses)
                ->where('payment_gateway', $gw->value)
                ->when($from, fn($q) => $q->where('paid_at', '>=', $from))
                ->when($to,   fn($q) => $q->where('paid_at', '<=', $to))
                ->sum('amount')
        )->toArray();

        //  Return
        return [
            // Meta
            'activePreset'       => $preset,
            'activePeriodLabel'  => static::dateRangePresetOptions()[$preset] ?? ucfirst($preset),
            'activeDateFrom'     => $from ? $from->format('Y-m-d') : null,
            'activeDateTo'       => $to   ? $to->format('Y-m-d')   : null,
            'activeGateway'      => $gatewayFilter,
            'activeStatus'       => $statusFilter,
            'activeCourseStatus' => $courseStatus,

            // People
            'totalStudents'        => $totalStudents,
            'totalInstructors'     => $totalInstructors,
            'newStudentsToday'     => $newStudentsToday,
            'newOrdersToday'       => $newOrdersToday,
            'newUsersToday'        => $newUsersToday,
            'pendingVerifications' => $pendingVerifications,
            'pendingCourseReviews' => $pendingCourseReviews,
            'pendingInstructors'   => $pendingInstructors,
            'studentTrend'         => $studentTrend,
            'recentUsers'          => User::latest()->take(6)->get(),

            // Courses
            'totalCourses'          => $totalCourses,
            'publishedCoursesCount' => $publishedCoursesCount,
            'newCoursesThisMonth'   => $newCoursesThisMonth,
            'totalSections'         => $totalSections,
            'totalLessons'          => $totalLessons,
            'recentCourses'         => $recentCourses,
            'popularCourses'        => $popularCourses,
            'lowRatedCourses'       => $lowRatedCourses,

            // Finance
            'totalOrders'             => $totalOrders,
            'totalRevenue'            => $totalRevenue,
            'platformRevenue'         => $platformRevenue,
            'instructorRevenue'       => $instructorRevenue,
            'completedPayments'       => $completedPayments,
            'pendingPayments'         => $pendingPayments,
            'failedPayments'          => $failedPayments,
            'failedPaymentsToday'     => $failedPaymentsToday,
            'ordersThisMonth'         => $totalOrders,
            'revenueThisMonth'        => $totalRevenue,
            'recentOrders'            => $recentOrders,
            'recentPayments'          => $recentPayments,
            'totalInstructorBalance'  => $totalInstructorBalance,
            'totalPendingBalance'     => $totalPendingBalance,
            'revenueTrend'            => $revenueTrend,
            'revenueChartData'        => $revenueChartData,
            'paymentStatusBreakdown'  => $paymentStatusBreakdown,
            'paymentGatewayBreakdown' => $paymentGatewayBreakdown,
            'pendingPayoutsCount'     => $pendingPayoutsCount,

            // Enrollments
            'enrollmentsThisMonth'  => $enrollmentsThisMonth,
            'enrollmentsLastMonth'  => $enrollmentsLastMonth,
            'enrollmentGrowth'      => $enrollmentGrowth,
            'avgCompletionRate'     => $avgCompletionRate,
            'completionRateGrowth'  => $completionRateGrowth,

            // Top instructors
            'topInstructors' => $topInstructors,

            // System health
            'queueJobsPending' => $queueJobsPending,
            'failedJobsCount'  => $failedJobsCount,
            'redisStatus'      => $redisStatus,
            'serverUptime'     => $serverUptime,
            'refreshInterval'  => max(5, (int) Setting::get('dashboard_refresh_interval', 30)),
        ];
    }

    /**
     * The period of equal length immediately preceding [$from, $to], used as the
     * comparison baseline for growth badges. Returns [null, null] when the active
     * filter has no bounded start (e.g. the "All Time" preset), since there is no
     * meaningful "previous period" to compare against.
     */
    private function previousPeriodRange($from, $to): array
    {
        if (! $from || ! $to) {
            return [null, null];
        }

        $days     = $from->diffInDays($to) + 1;
        $prevTo   = (clone $from)->subDay()->endOfDay();
        $prevFrom = (clone $prevTo)->subDays($days - 1)->startOfDay();

        return [$prevFrom, $prevTo];
    }

    //
    // Revenue chart: 4 period series
    //

    private function buildRevenueChartData(array $paidStatuses, string $gatewayFilter, $to = null): array
    {
        $anchorEnd = $to ? Carbon::instance($to) : now();

        return [
            '7d'  => $this->buildDailySeries(7,  $paidStatuses, $gatewayFilter, $anchorEnd),
            '30d' => $this->buildDailySeries(30, $paidStatuses, $gatewayFilter, $anchorEnd),
            '6m'  => $this->buildMonthlySeries(6,  $paidStatuses, $gatewayFilter, $anchorEnd),
            '12m' => $this->buildMonthlySeries(12, $paidStatuses, $gatewayFilter, $anchorEnd),
        ];
    }

    private function buildDailySeries(int $days, array $paidStatuses, string $gatewayFilter, $anchorEnd): array
    {
        $start = (clone $anchorEnd)->subDays($days - 1)->startOfDay();
        $end   = (clone $anchorEnd)->endOfDay();

        $rows = Payment::whereIn('status', $paidStatuses)
            ->whereBetween('paid_at', [$start, $end])
            ->when($gatewayFilter !== 'all', fn($q) => $q->where('payment_gateway', $gatewayFilter))
            ->selectRaw("DATE(paid_at) as day, SUM(amount) as total")
            ->groupBy('day')
            ->pluck('total', 'day');

        $labels = $gross = $platform = $instructor = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date   = (clone $anchorEnd)->subDays($i);
            $key    = $date->format('Y-m-d');
            $amount = (float) ($rows[$key] ?? 0);

            $labels[]     = $days <= 7 ? $date->format('D') : $date->format('M j');
            $gross[]      = round($amount, 2);
            $platform[]   = round($amount * 0.20, 2);
            $instructor[] = round($amount * 0.80, 2);
        }

        $totalGross = array_sum($gross);
        $prevStart  = (clone $anchorEnd)->subDays($days * 2)->startOfDay();
        $prevEnd    = (clone $anchorEnd)->subDays($days)->endOfDay();
        $prevTotal  = (float) Payment::whereIn('status', $paidStatuses)
            ->whereBetween('paid_at', [$prevStart, $prevEnd])
            ->when($gatewayFilter !== 'all', fn($q) => $q->where('payment_gateway', $gatewayFilter))
            ->sum('amount');

        $growth = $prevTotal > 0 ? round(($totalGross - $prevTotal) / $prevTotal * 100, 1) : 0;

        return [
            'labels'           => $labels,
            'gross'            => $gross,
            'platform'         => $platform,
            'instructor'       => $instructor,
            'total_gross'      => round($totalGross, 2),
            'total_platform'   => round($totalGross * 0.20, 2),
            'total_instructor' => round($totalGross * 0.80, 2),
            'gross_growth'     => $growth,
            'platform_growth'  => round($growth * 0.85, 1),
            'instructor_growth'=> round($growth * 1.05, 1),
        ];
    }

    private function buildMonthlySeries(int $months, array $paidStatuses, string $gatewayFilter, $anchorEnd): array
    {
        $start = (clone $anchorEnd)->startOfMonth()->subMonths($months - 1);
        $end   = (clone $anchorEnd)->endOfMonth();

        $rows = Payment::whereIn('status', $paidStatuses)
            ->whereBetween('paid_at', [$start, $end])
            ->when($gatewayFilter !== 'all', fn($q) => $q->where('payment_gateway', $gatewayFilter))
            ->selectRaw("TO_CHAR(DATE_TRUNC('month', paid_at), 'YYYY-MM') as month, SUM(amount) as total")
            ->groupBy('month')
            ->pluck('total', 'month');

        $labels = $gross = $platform = $instructor = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $date   = (clone $anchorEnd)->startOfMonth()->subMonths($i);
            $key    = $date->format('Y-m');
            $amount = (float) ($rows[$key] ?? 0);

            $labels[]     = $months <= 6 ? $date->format('M') : $date->format('M Y');
            $gross[]      = round($amount, 2);
            $platform[]   = round($amount * 0.20, 2);
            $instructor[] = round($amount * 0.80, 2);
        }

        $totalGross = array_sum($gross);
        $prevStart  = (clone $anchorEnd)->startOfMonth()->subMonths($months * 2);
        $prevEnd    = (clone $anchorEnd)->startOfMonth()->subMonths($months)->endOfMonth();
        $prevTotal  = (float) Payment::whereIn('status', $paidStatuses)
            ->whereBetween('paid_at', [$prevStart, $prevEnd])
            ->when($gatewayFilter !== 'all', fn($q) => $q->where('payment_gateway', $gatewayFilter))
            ->sum('amount');

        $growth = $prevTotal > 0 ? round(($totalGross - $prevTotal) / $prevTotal * 100, 1) : 0;

        return [
            'labels'            => $labels,
            'gross'             => $gross,
            'platform'          => $platform,
            'instructor'        => $instructor,
            'total_gross'       => round($totalGross, 2),
            'total_platform'    => round($totalGross * 0.20, 2),
            'total_instructor'  => round($totalGross * 0.80, 2),
            'gross_growth'      => $growth,
            'platform_growth'   => round($growth * 0.85, 1),
            'instructor_growth' => round($growth * 1.05, 1),
        ];
    }

    //
    // Top instructors by revenue
    //

    private function buildTopInstructors($from = null, $to = null): array
    {
        try {
            $rows = DB::table('order_items as oi')
                ->join('orders as o', 'o.id', '=', 'oi.order_id')
                ->join('users as u', 'u.id', '=', 'oi.instructor_id')
                ->where('o.payment_status', 'paid')
                ->when($from, fn ($q) => $q->where('o.created_at', '>=', $from))
                ->when($to, fn ($q) => $q->where('o.created_at', '<', $to->copy()->addDay()->startOfDay()))
                ->groupBy('oi.instructor_id', 'u.name', 'u.email')
                ->selectRaw("
                    oi.instructor_id as id,
                    u.name,
                    u.email,
                    SUM(oi.instructor_amount) as revenue,
                    COUNT(DISTINCT o.user_id) as students,
                    COUNT(DISTINCT oi.course_id) as courses
                ")
                ->orderByDesc('revenue')
                ->limit(5)
                ->get();

            if ($rows->isEmpty()) {
                return [];
            }

            // Growth compares this filtered period's revenue against the equal-length
            // period immediately preceding it, scoped to the same top instructors.
            [$prevFrom, $prevTo] = $this->previousPeriodRange($from, $to);

            $prevRevenueByInstructor = $prevFrom
                ? DB::table('order_items as oi')
                    ->join('orders as o', 'o.id', '=', 'oi.order_id')
                    ->where('o.payment_status', 'paid')
                    ->whereIn('oi.instructor_id', $rows->pluck('id'))
                    ->where('o.created_at', '>=', $prevFrom)
                    ->where('o.created_at', '<', $prevTo->copy()->addDay()->startOfDay())
                    ->groupBy('oi.instructor_id')
                    ->selectRaw('oi.instructor_id as id, SUM(oi.instructor_amount) as revenue')
                    ->pluck('revenue', 'id')
                : collect();

            return $rows->map(function ($row) use ($prevRevenueByInstructor) {
                $prevRevenue = (float) ($prevRevenueByInstructor[$row->id] ?? 0);
                $growth = $prevRevenue > 0
                    ? round(($row->revenue - $prevRevenue) / $prevRevenue * 100, 1)
                    : ((float) $row->revenue > 0 ? 100.0 : 0.0);
                $arr = (array) $row;
                $arr['growth'] = $growth;
                return $arr;
            })->toArray();
        } catch (\Throwable) {
            return [];
        }
    }
}
