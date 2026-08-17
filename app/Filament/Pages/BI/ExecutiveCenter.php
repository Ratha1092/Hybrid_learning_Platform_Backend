<?php

namespace App\Filament\Pages\BI;

use App\Domains\Analytics\Models\DailyMetric;
use App\Domains\Courses\Models\Course;
use App\Domains\Learning\Models\Enrollment;
use App\Domains\Orders\Enums\OrderPaymentStatus;
use App\Domains\Orders\Models\Order;
use App\Domains\Orders\Models\OrderItem;
use App\Domains\Users\Models\User;
use App\Support\Concerns\HasBiCsvExport;
use App\Support\Concerns\HasDateRangePresets;
use App\Support\PanelAccess;
use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class ExecutiveCenter extends Page
{
    use HasBiCsvExport;
    use HasDateRangePresets;

    protected string $view = 'filament.pages.bi.executive-center';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-home-modern';
    protected static ?string $navigationLabel = 'Executive Center';
    protected static string|\UnitEnum|null $navigationGroup = 'Business Intelligence';
    protected static ?int $navigationSort = 1;
    protected static ?string $slug = 'bi/executive';

    public string $preset = 'this_month';
    public string $dateFrom = '';
    public string $dateTo = '';

    public function mount(): void
    {
        $this->preset   = request('preset', 'this_month');
        $this->dateFrom = request('date_from', '');
        $this->dateTo   = request('date_to', '');
    }

    public function applyDateFilter(string $preset, string $from = '', string $to = ''): void
    {
        $this->preset   = $preset ?: 'this_month';
        $this->dateFrom = $preset === 'custom' ? $from : '';
        $this->dateTo   = $preset === 'custom' ? $to : '';
        $this->dispatchDateResolved();
    }

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return '';
    }

    public static function canAccess(): bool
    {
        return PanelAccess::can('bi.view_executive');
    }

    public static function pdfView(): string
    {
        return 'bi.pdf.executive-center';
    }

    public static function pdfViewData(array $filters): array
    {
        $preset = $filters['preset'] ?? 'this_month';

        return array_merge([
            'siteName'       => \App\Domains\System\Models\Setting::get('site_name', config('app.name')),
            'title'          => 'Executive Center Report',
            'filtersSummary' => static::dateRangePresetOptions()[$preset] ?? ucfirst($preset),
        ], static::buildPageData($filters));
    }

    public function getViewData(): array
    {
        $filters = [
            'preset'    => $this->preset ?: 'this_month',
            'date_from' => $this->dateFrom ?: null,
            'date_to'   => $this->dateTo ?: null,
        ];

        return array_merge(
            [
                'activePreset'   => $filters['preset'],
                'activeDateFrom' => static::resolvePreset($filters['preset'], 'this_month', $filters['date_from'], $filters['date_to'])[0]?->format('Y-m-d') ?? '',
                'activeDateTo'   => static::resolvePreset($filters['preset'], 'this_month', $filters['date_from'], $filters['date_to'])[1]?->format('Y-m-d') ?? '',
            ],
            static::buildPageData($filters)
        );
    }

    public static function buildPageData(array $filters): array
    {
        $preset = $filters['preset'] ?? 'this_month';
        [$from, $to] = static::resolvePreset($preset, 'this_month', $filters['date_from'] ?? null, $filters['date_to'] ?? null);

        $cacheKey = 'bi.executive.' . md5(serialize([$preset, $from?->toDateString(), $to?->toDateString()]));

        return Cache::remember($cacheKey, 300, function () use ($from, $to) {
            // ── Paid order base query ────────────────────────────────────────
            $paidBase = function () use ($from, $to) {
                $q = Order::where('payment_status', OrderPaymentStatus::Paid->value);
                static::applyDateRange($q, 'paid_at', $from, $to);
                return $q;
            };

            $grossRevenue  = (float) $paidBase()->sum('final_amount');
            $orderCount    = (int)   $paidBase()->count();
            $aov           = $orderCount > 0 ? $grossRevenue / $orderCount : 0.0;

            // ── OrderItem splits ─────────────────────────────────────────────
            $paidOrderIds     = $paidBase()->pluck('id');
            $platformRevenue  = (float) OrderItem::whereIn('order_id', $paidOrderIds)->sum('platform_amount');
            $instructorEarnings = (float) OrderItem::whereIn('order_id', $paidOrderIds)->sum('instructor_amount');

            // ── All-time platform counts ─────────────────────────────────────
            $activeStudents     = User::role('student')->where('status', 'active')->count();
            $activeInstructors  = User::role('instructor')->where('status', 'active')->count();
            $publishedCourses   = Course::where('is_published', true)->count();
            $newEnrollments     = (int) static::applyDateRange(Enrollment::query(), 'created_at', $from, $to)->count();

            // ── Completion rate ──────────────────────────────────────────────
            $totalEnrollments    = Enrollment::count();
            $completedEnrollments = Enrollment::where('status', 'completed')->count();
            $completionRate      = $totalEnrollments > 0 ? round(($completedEnrollments / $totalEnrollments) * 100, 1) : 0.0;

            // ── Marketplace Health Score (composite 0-100) ───────────────────
            $prevFrom = $from ? (clone $from)->subMonth() : null;
            $prevTo   = $from ? (clone $from)->subSecond() : null;
            $prevRevenue = $prevFrom ? (float) Order::where('payment_status', OrderPaymentStatus::Paid->value)
                ->where('paid_at', '>=', $prevFrom)->where('paid_at', '<', $from)->sum('final_amount') : 0;
            $revenueGrowth = ($prevRevenue > 0 && $grossRevenue > 0)
                ? round((($grossRevenue - $prevRevenue) / $prevRevenue) * 100, 1)
                : ($grossRevenue > 0 ? 100.0 : 0.0);
            $healthScore = 0;
            if ($revenueGrowth >= 0) $healthScore += 50;
            if ($completionRate >= 40) $healthScore += 50;

            // ── Revenue trend (12 months) from DailyMetric ──────────────────
            $trendEnd   = $to ?? now();
            $trendStart = (clone $trendEnd)->startOfMonth()->subMonths(11);
            $rawTrend = DailyMetric::selectRaw("DATE_TRUNC('month', date) as month, SUM(total_revenue) as revenue, SUM(total_orders) as orders_count")
                ->whereBetween('date', [$trendStart->toDateString(), $trendEnd->toDateString()])
                ->groupByRaw("DATE_TRUNC('month', date)")
                ->orderBy('month')
                ->get()
                ->keyBy(fn ($r) => Carbon::parse($r->month)->format('Y-m'));

            $trendLabels  = [];
            $trendRevenue = [];
            $trendOrders  = [];
            for ($i = 11; $i >= 0; $i--) {
                $month            = (clone $trendEnd)->startOfMonth()->subMonths($i);
                $key              = $month->format('Y-m');
                $trendLabels[]    = $month->format("M 'y");
                $trendRevenue[]   = (float) ($rawTrend[$key]->revenue ?? 0);
                $trendOrders[]    = (int)   ($rawTrend[$key]->orders_count ?? 0);
            }

            // ── Student vs Instructor growth (6 months) ──────────────────────
            $growthLabels      = [];
            $studentGrowth     = [];
            $instructorGrowth  = [];
            for ($i = 5; $i >= 0; $i--) {
                $month             = now()->startOfMonth()->subMonths($i);
                $monthEnd          = (clone $month)->endOfMonth();
                $growthLabels[]    = $month->format("M 'y");
                $studentGrowth[]   = User::role('student')
                    ->whereBetween('created_at', [$month, $monthEnd])->count();
                $instructorGrowth[] = User::role('instructor')
                    ->whereBetween('created_at', [$month, $monthEnd])->count();
            }

            // ── Top courses by all-time revenue (from order_items) ──────────
            $topCourses = OrderItem::selectRaw('course_id, SUM(final_amount) as total_revenue, COUNT(*) as total_sales')
                ->groupBy('course_id')
                ->orderByRaw('SUM(final_amount) DESC')
                ->take(10)
                ->with(['course' => fn ($q) => $q->with('instructor:id,name')])
                ->get();

            // ── Top 10 instructors by period earnings ────────────────────────
            $instructorEarningsRows = OrderItem::whereIn('order_id', $paidOrderIds)
                ->groupBy('instructor_id')
                ->selectRaw('instructor_id, SUM(instructor_amount) as earnings, COUNT(*) as sales')
                ->orderByRaw('SUM(instructor_amount) DESC')
                ->take(10)
                ->get();
            $instructorIds    = $instructorEarningsRows->pluck('instructor_id');
            $instructorNames  = User::whereIn('id', $instructorIds)->pluck('name', 'id');
            $topInstructors   = $instructorEarningsRows->map(fn ($r) => [
                'name'     => $instructorNames[$r->instructor_id] ?? 'Unknown',
                'earnings' => (float) $r->earnings,
                'sales'    => (int) $r->sales,
            ]);

            // ── Recent orders ────────────────────────────────────────────────
            $recentOrders = Order::where('payment_status', OrderPaymentStatus::Paid->value)
                ->with('user:id,name')
                ->orderByDesc('paid_at')
                ->take(10)
                ->get();

            return [
                'kpis' => [
                    'grossRevenue'      => $grossRevenue,
                    'platformRevenue'   => $platformRevenue,
                    'instructorEarnings'=> $instructorEarnings,
                    'orderCount'        => $orderCount,
                    'newEnrollments'    => $newEnrollments,
                    'activeStudents'    => $activeStudents,
                    'activeInstructors' => $activeInstructors,
                    'publishedCourses'  => $publishedCourses,
                    'aov'               => $aov,
                    'healthScore'       => $healthScore,
                    'revenueGrowth'     => $revenueGrowth,
                    'completionRate'    => $completionRate,
                ],
                'revenueTrendData' => [
                    'labels'   => $trendLabels,
                    'revenue'  => $trendRevenue,
                    'orders'   => $trendOrders,
                ],
                'growthData' => [
                    'labels'      => $growthLabels,
                    'students'    => $studentGrowth,
                    'instructors' => $instructorGrowth,
                ],
                'topCoursesChartLabels' => $topCourses->take(5)->values()->map(fn ($c) => substr($c->course?->title ?? '?', 0, 28))->values()->toArray(),
                'topCoursesChartValues' => $topCourses->take(5)->values()->map(fn ($c) => (float) $c->total_revenue)->values()->toArray(),
                'topCourses'     => $topCourses,
                'topInstructors' => $topInstructors,
                'recentOrders'   => $recentOrders,
            ];
        });
    }
}
