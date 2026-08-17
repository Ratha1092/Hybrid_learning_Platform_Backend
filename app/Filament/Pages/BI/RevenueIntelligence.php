<?php

namespace App\Filament\Pages\BI;

use App\Domains\Courses\Models\Category;
use App\Domains\Finance\Models\InstructorWallet;
use App\Domains\Orders\Enums\OrderPaymentStatus;
use App\Domains\Orders\Models\Order;
use App\Domains\Orders\Models\OrderItem;

use App\Domains\Payments\Enums\PaymentStatus;
use App\Domains\Payments\Models\Payment;
use App\Domains\Users\Models\User;
use App\Support\Concerns\HasBiCsvExport;
use App\Support\Concerns\HasDateRangePresets;
use App\Support\PanelAccess;
use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class RevenueIntelligence extends Page
{
    use HasBiCsvExport;
    use HasDateRangePresets;

    protected string $view = 'filament.pages.bi.revenue-intelligence';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Revenue Intelligence';
    protected static string|\UnitEnum|null $navigationGroup = 'Business Intelligence';
    protected static ?int $navigationSort = 2;
    protected static ?string $slug = 'bi/revenue';

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
        return PanelAccess::can('bi.view_revenue');
    }

    public static function pdfView(): string
    {
        return 'bi.pdf.revenue-intelligence';
    }

    public static function pdfViewData(array $filters): array
    {
        $preset = $filters['preset'] ?? 'this_month';

        return array_merge([
            'siteName'       => \App\Domains\System\Models\Setting::get('site_name', config('app.name')),
            'title'          => 'Revenue Intelligence Report',
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

        [$from, $to] = static::resolvePreset($filters['preset'], 'this_month', $filters['date_from'], $filters['date_to']);

        return array_merge(
            ['activePreset' => $filters['preset'], 'activeDateFrom' => $from?->format('Y-m-d') ?? '', 'activeDateTo' => $to?->format('Y-m-d') ?? ''],
            static::buildPageData($filters)
        );
    }

    public static function buildPageData(array $filters): array
    {
        $preset = $filters['preset'] ?? 'this_month';
        [$from, $to] = static::resolvePreset($preset, 'this_month', $filters['date_from'] ?? null, $filters['date_to'] ?? null);

        $cacheKey = 'bi.revenue.' . md5(serialize([$preset, $from?->toDateString(), $to?->toDateString()]));

        return Cache::remember($cacheKey, 300, function () use ($from, $to, $preset) {
            // Paid order IDs in period
            $paidOrderIds = Order::where('payment_status', OrderPaymentStatus::Paid->value)
                ->when($from, fn ($q) => static::applyDateRange($q, 'paid_at', $from, $to))
                ->pluck('id');

            $grossRevenue      = (float) OrderItem::whereIn('order_id', $paidOrderIds)->sum('final_amount');
            $platformRevenue   = (float) OrderItem::whereIn('order_id', $paidOrderIds)->sum('platform_amount');
            $instructorRevenue = (float) OrderItem::whereIn('order_id', $paidOrderIds)->sum('instructor_amount');
            $discounts         = (float) OrderItem::whereIn('order_id', $paidOrderIds)->sum('discount_amount');
            $orderCount        = count($paidOrderIds);
            $aov               = $orderCount > 0 ? $grossRevenue / $orderCount : 0.0;
            $pendingRevenue    = (float) InstructorWallet::sum('pending_balance');

            // Growth vs prior period
            $periodDays = $from && $to ? $from->diffInDays($to) + 1 : 30;
            $prevFrom   = $from ? (clone $from)->subDays($periodDays) : null;
            $prevTo     = $from ? (clone $from)->subSecond() : null;
            $prevIds    = $prevFrom ? Order::where('payment_status', OrderPaymentStatus::Paid->value)
                ->where('paid_at', '>=', $prevFrom)->where('paid_at', '<', $from)->pluck('id') : collect();
            $prevRevenue    = $prevIds->count() ? (float) OrderItem::whereIn('order_id', $prevIds)->sum('final_amount') : 0;
            $revenueGrowth  = $prevRevenue > 0 ? round((($grossRevenue - $prevRevenue) / $prevRevenue) * 100, 1) : ($grossRevenue > 0 ? 100.0 : 0.0);

            // Revenue trend (daily if ≤31 days, else monthly)
            $dayCount  = $from && $to ? (int) $from->diffInDays($to) : 30;
            $trendData = [];
            if ($dayCount <= 31) {
                // Daily
                $period = $from ?? now()->startOfMonth();
                $end    = $to   ?? now()->endOfDay();
                $curr   = clone $period;
                while ($curr <= $end) {
                    $dayKey         = $curr->format('Y-m-d');
                    $trendData[$dayKey] = 0.0;
                    $curr->addDay();
                }
                $dailyRevs = OrderItem::whereIn('order_id', $paidOrderIds)
                    ->join('orders', 'order_items.order_id', '=', 'orders.id')
                    ->selectRaw("DATE(orders.paid_at) as day, SUM(order_items.final_amount) as rev")
                    ->groupBy('day')->orderBy('day')->get();
                foreach ($dailyRevs as $r) {
                    if (isset($trendData[$r->day])) $trendData[$r->day] = (float) $r->rev;
                }
            } else {
                // Monthly (last 12)
                for ($i = 11; $i >= 0; $i--) {
                    $month           = now()->subMonths($i)->startOfMonth();
                    $trendData[$month->format("M 'y")] = 0.0;
                }
                $monthlyRevs = OrderItem::whereIn('order_id', $paidOrderIds)
                    ->join('orders', 'order_items.order_id', '=', 'orders.id')
                    ->selectRaw("DATE_TRUNC('month', orders.paid_at) as month, SUM(order_items.final_amount) as rev")
                    ->groupByRaw("DATE_TRUNC('month', orders.paid_at)")->orderBy('month')->get();
                foreach ($monthlyRevs as $r) {
                    $key = Carbon::parse($r->month)->format("M 'y");
                    if (isset($trendData[$key])) $trendData[$key] = (float) $r->rev;
                }
            }

            // Revenue by Category (via order_items → courses → categories)
            $categoryRevenue = OrderItem::whereIn('order_id', $paidOrderIds)
                ->join('courses', 'order_items.course_id', '=', 'courses.id')
                ->join('categories', 'courses.category_id', '=', 'categories.id')
                ->selectRaw('categories.name as cat_name, SUM(order_items.final_amount) as rev')
                ->groupBy('categories.name')
                ->orderByRaw('SUM(order_items.final_amount) DESC')
                ->take(8)
                ->get();

            // Revenue by Gateway
            $paidStatuses = [PaymentStatus::Paid->value, PaymentStatus::Completed->value];
            $gatewayRevenue = Payment::whereIn('status', $paidStatuses)
                ->when($from, fn ($q) => static::applyDateRange($q, 'paid_at', $from, $to))
                ->selectRaw('payment_gateway, SUM(amount) as rev, COUNT(*) as cnt')
                ->groupBy('payment_gateway')
                ->orderByRaw('SUM(amount) DESC')
                ->get();

            // Top courses by period revenue
            $topCoursesRev = OrderItem::whereIn('order_id', $paidOrderIds)
                ->groupBy('course_id')
                ->selectRaw('course_id, SUM(final_amount) as revenue, COUNT(*) as sales')
                ->orderByRaw('SUM(final_amount) DESC')
                ->take(10)
                ->with('course:id,title,instructor_id')
                ->get();

            // Top instructors by period revenue
            $topInstructorsRev = OrderItem::whereIn('order_id', $paidOrderIds)
                ->groupBy('instructor_id')
                ->selectRaw('instructor_id, SUM(instructor_amount) as revenue, COUNT(*) as sales')
                ->orderByRaw('SUM(instructor_amount) DESC')
                ->take(10)
                ->get();
            $iNames = User::whereIn('id', $topInstructorsRev->pluck('instructor_id'))->pluck('name', 'id');

            return [
                'kpis' => [
                    'grossRevenue'       => $grossRevenue,
                    'platformRevenue'    => $platformRevenue,
                    'instructorRevenue'  => $instructorRevenue,
                    'discounts'          => $discounts,
                    'pendingRevenue'     => $pendingRevenue,
                    'orderCount'         => $orderCount,
                    'aov'                => $aov,
                    'revenueGrowth'      => $revenueGrowth,
                ],
                'trendLabels'    => array_keys($trendData),
                'trendValues'    => array_values($trendData),
                'categoryLabels' => $categoryRevenue->pluck('cat_name')->toArray(),
                'categoryValues' => $categoryRevenue->map(fn ($r) => (float) $r->rev)->values()->toArray(),
                'gatewayLabels'  => $gatewayRevenue->pluck('payment_gateway')->toArray(),
                'gatewayValues'  => $gatewayRevenue->map(fn ($r) => (float) $r->rev)->values()->toArray(),
                'topCourses'   => $topCoursesRev,
                'topInstructors' => $topInstructorsRev->map(fn ($r) => [
                    'name'    => $iNames[$r->instructor_id] ?? 'Unknown',
                    'revenue' => (float) $r->revenue,
                    'sales'   => (int) $r->sales,
                ]),
            ];
        });
    }
}
