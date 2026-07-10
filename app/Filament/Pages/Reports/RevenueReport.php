<?php

namespace App\Filament\Pages\Reports;

use App\Domains\Orders\Enums\OrderPaymentStatus;
use App\Domains\Orders\Models\Order;
use App\Domains\Promotions\Models\Coupon;
use App\Domains\Reports\Concerns\HasScheduleAction;
use App\Domains\Reports\Contracts\Schedulable;
use App\Domains\System\Models\Setting;
use App\Support\Concerns\HasDateRangePresets;
use App\Support\CsvExporter;
use App\Support\PanelAccess;
use BackedEnum;
use Filament\Pages\Page;

class RevenueReport extends Page implements Schedulable
{
    use HasDateRangePresets;
    use HasScheduleAction;

    protected string $view = 'filament.pages.reports.revenue-report';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Revenue Report';
    protected static string|\UnitEnum|null $navigationGroup = 'Data Exports';
    protected static ?int $navigationSort = 1;
    protected static ?string $slug = 'reports/revenue';

    public string $preset = 'this_month';
    public string $dateFrom = '';
    public string $dateTo = '';
    public int $page = 1;

    public function mount(): void
    {
        $this->preset   = request('preset', 'this_month');
        $this->dateFrom = request('date_from', '');
        $this->dateTo   = request('date_to', '');
        $this->page     = (int) request('page', 1);
    }

    public function applyDateFilter(string $preset, string $from = '', string $to = ''): void
    {
        $this->preset   = $preset ?: 'this_month';
        $this->dateFrom = $preset === 'custom' ? $from : '';
        $this->dateTo   = $preset === 'custom' ? $to : '';
        $this->page     = 1;
        $this->dispatchDateResolved();
    }

    public static function canAccess(): bool
    {
        return PanelAccess::can('reports.view_revenue');
    }

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return '';
    }

    // Schedulable contract

    public static function reportKey(): string
    {
        return 'revenue';
    }

    public static function reportLabel(): string
    {
        return 'Revenue';
    }

    public static function pdfView(): string
    {
        return 'reports.pdf.revenue-report';
    }

    public static function filtersSummary(array $filters): string
    {
        $preset = $filters['preset'] ?? 'this_month';
        return static::dateRangePresetOptions()[$preset] ?? ucfirst($preset);
    }

    public static function csvHeaderAndRows(array $filters): array
    {
        $data = static::buildReportData($filters, paginate: false);

        $header = ['Order #', 'Date', 'Customer', 'Items', 'Subtotal', 'Discount', 'Coupon', 'Final Amount', 'Platform Cut', 'Instructor Cut'];

        $rows = $data['orders']->map(fn (Order $o) => [
            $o->order_number,
            $o->created_at?->format('M d, Y') ?? '',
            $o->customer_name ?? $o->user?->name ?? '',
            $o->items->count(),
            number_format((float) $o->total_amount, 2),
            number_format((float) $o->discount_amount, 2),
            $o->coupon_code ?? '',
            number_format((float) $o->final_amount, 2),
            number_format((float) $o->items->sum('platform_amount'), 2),
            number_format((float) $o->items->sum('instructor_amount'), 2),
        ])->all();

        return [$header, $rows];
    }

    public static function pdfViewData(array $filters): array
    {
        $data = static::buildReportData($filters, paginate: false);

        return [
            'siteName' => Setting::get('site_name', config('app.name')),
            'title' => 'Revenue Report',
            'filtersSummary' => static::filtersSummary($filters),
            'kpis' => $data['kpis'],
            'revenueTrend' => $data['revenueTrend'],
            'topCoupons' => $data['topCoupons'],
            'orders' => $data['orders']->take(200),
        ];
    }

    // Interactive pag

    public function getViewData(): array
    {
        $filters = $this->currentFilters();

        [$from, $to] = static::resolvePreset($filters['preset'], 'this_month', $filters['date_from'] ?? null, $filters['date_to'] ?? null);
        return array_merge(
            [
                'activePreset'   => $filters['preset'],
                'activeDateFrom' => $from?->format('Y-m-d') ?? '',
                'activeDateTo'   => $to?->format('Y-m-d') ?? '',
            ],
            static::buildReportData($filters, paginate: true),
        );
    }

    public function exportCsv(): void
    {
        [$header, $rows] = static::csvHeaderAndRows($this->currentFilters());

        $this->dispatch('download-csv',
            content: CsvExporter::build($header, $rows),
            filename: 'revenue-report-' . now()->format('Y-m-d') . '.csv',
        );
    }

    // Shared cor

    private function currentFilters(): array
    {
        return [
            'preset'    => $this->preset ?: 'this_month',
            'date_from' => $this->dateFrom ?: null,
            'date_to'   => $this->dateTo ?: null,
            'page'      => $this->page,
            'per_page'  => 10,
        ];
    }

    public static function buildReportData(array $filters, bool $paginate = true): array
    {
        $preset = $filters['preset'] ?? 'this_month';
        [$from, $to] = static::resolvePreset($preset, 'this_month', $filters['date_from'] ?? null, $filters['date_to'] ?? null);

        $paidBase = function () use ($from, $to) {
            $q = Order::where('payment_status', OrderPaymentStatus::Paid->value);
            static::applyDateRange($q, 'created_at', $from, $to);
            return $q;
        };

        $totalRevenue = (clone $paidBase())->sum('final_amount');
        $totalDiscounts = (clone $paidBase())->sum('discount_amount');
        $orderCount = (clone $paidBase())->count();
        $averageOrderValue = $orderCount > 0 ? $totalRevenue / $orderCount : 0;

        $itemTotals = \App\Domains\Orders\Models\OrderItem::whereHas('order', function ($q) use ($from, $to) {
            $q->where('payment_status', OrderPaymentStatus::Paid->value);
            static::applyDateRange($q, 'created_at', $from, $to);
        })->selectRaw('SUM(platform_amount) as platform_total, SUM(instructor_amount) as instructor_total')->first();

        $platformRevenue = (float) ($itemTotals->platform_total ?? 0);
        $instructorPayable = (float) ($itemTotals->instructor_total ?? 0);

        if ($from && $to) {
            $diffDays = (int) $from->diffInDays($to) + 1;
            if ($diffDays <= 31) {
                $trendTitle = 'Daily Revenue';
                $revenueTrend = collect();
                $cursor = clone $from;
                while ($cursor->lte($to)) {
                    $ds = (clone $cursor)->startOfDay();
                    $de = (clone $cursor)->endOfDay();
                    $revenueTrend->push([
                        'label'   => $cursor->format('M d'),
                        'revenue' => (float) Order::where('payment_status', OrderPaymentStatus::Paid->value)
                            ->whereBetween('created_at', [$ds, $de])
                            ->sum('final_amount'),
                    ]);
                    $cursor->addDay();
                }
            } else {
                $trendTitle = 'Monthly Revenue';
                $startMonth = (clone $from)->startOfMonth();
                $endMonth   = (clone $to)->startOfMonth();
                $revenueTrend = collect();
                $cursor = clone $startMonth;
                while ($cursor->lte($endMonth)) {
                    $revenueTrend->push([
                        'label'   => $cursor->format('M \'y'),
                        'revenue' => (float) Order::where('payment_status', OrderPaymentStatus::Paid->value)
                            ->whereMonth('created_at', $cursor->month)
                            ->whereYear('created_at', $cursor->year)
                            ->sum('final_amount'),
                    ]);
                    $cursor->addMonth();
                }
            }
        } else {
            $trendTitle = 'Revenue Trend (12 Months)';
            $trendBase  = now();
            $revenueTrend = collect(range(11, 0))->map(function ($mo) use ($trendBase) {
                $date = (clone $trendBase)->subMonths($mo);
                return [
                    'label'   => $date->format('M \'y'),
                    'revenue' => (float) Order::where('payment_status', OrderPaymentStatus::Paid->value)
                        ->whereMonth('created_at', $date->month)
                        ->whereYear('created_at', $date->year)
                        ->sum('final_amount'),
                ];
            });
        }

        $topCoupons = Coupon::where('used_count', '>', 0)
            ->orderByDesc('used_count')
            ->take(5)
            ->get(['code', 'type', 'value', 'used_count']);

        $query = Order::where('payment_status', OrderPaymentStatus::Paid->value)
            ->with(['items', 'user:id,name'])
            ->orderByDesc('id');
        static::applyDateRange($query, 'created_at', $from, $to);

        if ($paginate) {
            $page = max(1, (int) ($filters['page'] ?? 1));
            $perPage = in_array((int) ($filters['per_page'] ?? 10), [10, 25, 50]) ? (int) $filters['per_page'] : 10;
            $totalRows = (clone $query)->count();
            $totalPages = max(1, (int) ceil($totalRows / $perPage));
            $orders = $query->skip(($page - 1) * $perPage)->take($perPage)->get();
        } else {
            $orders = $query->get();
            $totalRows = $orders->count();
            $totalPages = 1;
            $page = 1;
            $perPage = $totalRows;
        }

        return [
            'kpis' => [
                'totalRevenue' => $totalRevenue,
                'totalDiscounts' => $totalDiscounts,
                'platformRevenue' => $platformRevenue,
                'instructorPayable' => $instructorPayable,
                'orderCount' => $orderCount,
                'averageOrderValue' => $averageOrderValue,
            ],
            'revenueTrend' => $revenueTrend,
            'trendTitle'   => $trendTitle,
            'topCoupons' => $topCoupons,
            'orders' => $orders,
            'total' => $totalRows,
            'totalPages' => $totalPages,
            'curPage' => $page,
            'perPage' => $perPage,
        ];
    }
}
