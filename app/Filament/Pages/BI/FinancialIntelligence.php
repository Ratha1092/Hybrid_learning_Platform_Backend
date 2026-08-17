<?php

namespace App\Filament\Pages\BI;

use App\Domains\Billing\Models\Invoice;
use App\Domains\Finance\Models\InstructorWallet;
use App\Domains\Finance\Models\PayoutRequest;
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

class FinancialIntelligence extends Page
{
    use HasBiCsvExport;
    use HasDateRangePresets;

    protected string $view = 'filament.pages.bi.financial-intelligence';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar-square';
    protected static ?string $navigationLabel = 'Financial Intelligence';
    protected static string|\UnitEnum|null $navigationGroup = 'Business Intelligence';
    protected static ?int $navigationSort = 7;
    protected static ?string $slug = 'bi/financial';

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

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable { return ''; }

    public static function canAccess(): bool
    {
        return PanelAccess::can('bi.view_financial');
    }

    public static function pdfView(): string
    {
        return 'bi.pdf.financial-intelligence';
    }

    public static function pdfViewData(array $filters): array
    {
        $preset = $filters['preset'] ?? 'this_month';

        return array_merge([
            'siteName'       => \App\Domains\System\Models\Setting::get('site_name', config('app.name')),
            'title'          => 'Financial Intelligence Report',
            'filtersSummary' => static::dateRangePresetOptions()[$preset] ?? ucfirst($preset),
        ], static::buildPageData($filters));
    }

    public function getViewData(): array
    {
        $filters = ['preset' => $this->preset ?: 'this_month', 'date_from' => $this->dateFrom ?: null, 'date_to' => $this->dateTo ?: null];
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

        $cacheKey = 'bi.financial.' . md5(serialize([$preset, $from?->toDateString(), $to?->toDateString()]));

        return Cache::remember($cacheKey, 300, function () use ($from, $to) {
            $paidStatuses  = [PaymentStatus::Paid->value, PaymentStatus::Completed->value];
            $paidOrderIds  = Order::where('payment_status', OrderPaymentStatus::Paid->value)
                ->when($from, fn ($q) => static::applyDateRange($q, 'paid_at', $from, $to))
                ->pluck('id');

            $grossRevenue      = (float) OrderItem::whereIn('order_id', $paidOrderIds)->sum('final_amount');
            $platformProfit    = (float) OrderItem::whereIn('order_id', $paidOrderIds)->sum('platform_amount');
            $instructorEarnings= (float) OrderItem::whereIn('order_id', $paidOrderIds)->sum('instructor_amount');
            $taxCollected      = (float) static::applyDateRange(Invoice::query(), 'issued_at', $from, $to)->sum('tax_amount');
            $totalWalletBalance= (float) InstructorWallet::sum('balance');
            $pendingPayout     = (float) InstructorWallet::sum('pending_balance');
            $completedPayout   = (float) static::applyDateRange(
                PayoutRequest::query()->where('status', 'approved'), 'processed_at', $from, $to
            )->sum('amount');
            $cashFlow          = $grossRevenue - $completedPayout;
            $operatingMargin   = $grossRevenue > 0 ? round(($platformProfit / $grossRevenue) * 100, 1) : 0.0;

            // Cash flow monthly (12 months)
            $cfLabels  = [];
            $cfRevenue = [];
            $cfPayouts = [];
            for ($i = 11; $i >= 0; $i--) {
                $m    = now()->subMonths($i)->startOfMonth();
                $mEnd = (clone $m)->endOfMonth();
                $cfLabels[]  = $m->format("M 'y");
                $mOrderIds   = Order::where('payment_status', OrderPaymentStatus::Paid->value)
                    ->whereBetween('paid_at', [$m, $mEnd])->pluck('id');
                $cfRevenue[] = (float) OrderItem::whereIn('order_id', $mOrderIds)->sum('final_amount');
                $cfPayouts[] = (float) PayoutRequest::where('status', 'approved')
                    ->whereBetween('processed_at', [$m, $mEnd])->sum('amount');
            }

            // Payout trend (6 months)
            $ptLabels = [];
            $ptValues = [];
            for ($i = 5; $i >= 0; $i--) {
                $m    = now()->subMonths($i)->startOfMonth();
                $mEnd = (clone $m)->endOfMonth();
                $ptLabels[] = $m->format("M 'y");
                $ptValues[] = (float) PayoutRequest::where('status', 'approved')
                    ->whereBetween('processed_at', [$m, $mEnd])->sum('amount');
            }

            // Pending payouts table
            $pendingPayouts = PayoutRequest::where('status', 'pending')
                ->with('instructor:id,name')
                ->orderByDesc('created_at')
                ->take(15)
                ->get();

            // Outstanding balances
            $outstandingBalances = InstructorWallet::where('balance', '>', 0)
                ->with('instructor:id,name')
                ->orderByDesc('balance')
                ->take(10)
                ->get();

            return [
                'kpis' => [
                    'grossRevenue'       => $grossRevenue,
                    'platformProfit'     => $platformProfit,
                    'instructorEarnings' => $instructorEarnings,
                    'pendingPayout'      => $pendingPayout,
                    'completedPayout'    => $completedPayout,
                    'totalWalletBalance' => $totalWalletBalance,
                    'taxCollected'       => $taxCollected,
                    'operatingMargin'    => $operatingMargin,
                    'cashFlow'           => $cashFlow,
                ],
                'cfLabels'  => $cfLabels,
                'cfRevenue' => $cfRevenue,
                'cfPayouts' => $cfPayouts,
                'ptLabels'  => $ptLabels,
                'ptValues'  => $ptValues,
                'pendingPayouts'       => $pendingPayouts,
                'outstandingBalances'  => $outstandingBalances,
            ];
        });
    }
}
