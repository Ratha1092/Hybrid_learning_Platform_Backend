<?php

namespace App\Filament\Pages;

use BackedEnum;
use App\Domains\Courses\Models\Course;
use App\Domains\Courses\Models\Lesson;
use App\Domains\Courses\Models\Section;
use App\Domains\Users\Models\User;
use App\Domains\Users\Models\InstructorVerification;
use App\Domains\Orders\Models\Order;
use App\Domains\Payments\Enums\PaymentStatus;
use App\Domains\Payments\Models\Payment;
use App\Domains\Finance\Models\InstructorWallet;
use App\Domains\Reports\Concerns\HasScheduleAction;
use App\Domains\Reports\Contracts\Schedulable;
use App\Domains\System\Models\Setting;
use App\Support\Concerns\HasDateRangePresets;
use App\Support\CsvExporter;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Facades\Cache;
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
    protected function getHeaderWidgets(): array
    {
        return [];
    }

    protected function getFooterWidgets(): array
    {
        return [];
    }

    // ── Schedulable contract ────────────────────────────────────────────

    public static function reportKey(): string
    {
        return 'executive';
    }

    public static function reportLabel(): string
    {
        return 'Executive Dashboard';
    }

    public static function pdfView(): string
    {
        return 'reports.pdf.executive-report';
    }

    public static function filtersSummary(array $filters): string
    {
        $preset = $filters['preset'] ?? 'this_month';
        return static::dateRangePresetOptions()[$preset] ?? ucfirst($preset);
    }

    public static function csvHeaderAndRows(array $filters): array
    {
        $data = static::buildDataFromFilters($filters);

        $header = ['Metric', 'Value'];
        $rows = [
            ['Total Students', $data['totalStudents']],
            ['Total Instructors', $data['totalInstructors']],
            ['Pending Verifications', $data['pendingVerifications']],
            ['Pending Course Reviews', $data['pendingCourseReviews']],
            ['Total Courses', $data['totalCourses']],
            ['Total Sections', $data['totalSections']],
            ['Total Lessons', $data['totalLessons']],
            ['Total Orders', $data['totalOrders']],
            ['Total Revenue', number_format((float) $data['totalRevenue'], 2)],
            ['Completed Payments', $data['completedPayments']],
            ['Pending Payments', $data['pendingPayments']],
            ['Failed Payments', $data['failedPayments']],
            ['Outstanding Instructor Balance', number_format((float) $data['totalInstructorBalance'], 2)],
        ];

        return [$header, $rows];
    }

    public static function pdfViewData(array $filters): array
    {
        $data = static::buildDataFromFilters($filters);

        return [
            'siteName' => Setting::get('site_name', config('app.name')),
            'title' => 'Executive Dashboard',
            'filtersSummary' => static::filtersSummary($filters),
            'data' => $data,
        ];
    }

    public function exportCsv(): void
    {
        [$header, $rows] = static::csvHeaderAndRows($this->currentFilters());

        $this->dispatch('download-csv',
            content: CsvExporter::build($header, $rows),
            filename: 'executive-dashboard-' . now()->format('Y-m-d') . '.csv',
        );
    }

    private function currentFilters(): array
    {
        return [
            'preset' => request('preset', 'this_month'),
            'date_from' => request('date_from'),
            'date_to' => request('date_to'),
            'gateway' => request('gateway', 'all'),
            'status' => request('status', 'all'),
            'course_status' => request('course_status', 'all'),
        ];
    }

    private static function buildDataFromFilters(array $filters): array
    {
        [$from, $to] = static::resolvePreset(
            $filters['preset'] ?? 'this_month',
            'this_month',
            $filters['date_from'] ?? null,
            $filters['date_to'] ?? null,
        );

        return (new self())->buildViewData(
            $from,
            $to,
            $filters['preset'] ?? 'this_month',
            $filters['gateway'] ?? 'all',
            $filters['status'] ?? 'all',
            $filters['course_status'] ?? 'all',
        );
    }

    public function getViewData(): array
    {
        [$from, $to] = $this->resolveDateRange('this_month');

        $preset        = request('preset', 'this_month');
        $gatewayFilter = request('gateway', 'all');
        $statusFilter  = request('status', 'all');
        $courseStatus  = request('course_status', 'all');
        $fromKey = $from ? $from->format('Ymd') : 'null';
        $toKey   = $to   ? $to->format('Ymd')   : 'null';
        $cacheKey = "dashboard.{$preset}.{$gatewayFilter}.{$statusFilter}.{$courseStatus}.{$fromKey}.{$toKey}";
        return Cache::tags(['dashboard'])->remember($cacheKey, 300, function () use ($from, $to, $preset, $gatewayFilter, $statusFilter, $courseStatus) {
            return $this->buildViewData($from, $to, $preset, $gatewayFilter, $statusFilter, $courseStatus);
        });
    }

    private function buildViewData($from, $to, $preset, $gatewayFilter, $statusFilter, $courseStatus): array
    {

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

        // People
        $studentQ = User::role('student');
        $this->applyDateRange($studentQ, 'created_at', $from, $to);
        $totalStudents = $studentQ->count();
        $newStudentsThisMonth = $totalStudents;
        $totalInstructors     = User::role('instructor')->count();
        $pendingVerifications = InstructorVerification::pending()->count();
        $pendingCourseReviews = Course::where('status', Course::STATUS_PENDING)->count();
        $pendingInstructors = User::whereHas('instructorVerification', fn ($q) => $q->where('status', 'pending'))
            ->latest()->take(5)->get();

        // Courses
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

        // Orders─
        $orderQ = Order::query();
        $this->applyDateRange($orderQ, 'created_at', $from, $to);
        $totalOrders = $orderQ->count();

        $ordersThisMonth = $totalOrders;

        // Finance
        $totalRevenue = $paidBase()->sum('amount');

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

        // Trends─
        $trendBase = $to ?? now();

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

        // Breakdowns─
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
            'pendingCourseReviews'    => $pendingCourseReviews,
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
            'revenueThisMonth'        => $totalRevenue,
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
