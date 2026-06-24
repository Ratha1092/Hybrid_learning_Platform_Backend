<?php

namespace App\Filament\Pages\Reports;

use App\Domains\Payments\Enums\PaymentGateway;
use App\Domains\Payments\Enums\PaymentStatus;
use App\Domains\Payments\Models\Payment;
use App\Domains\Reports\Concerns\HasScheduleAction;
use App\Domains\Reports\Contracts\Schedulable;
use App\Domains\System\Models\Setting;
use App\Support\Concerns\HasDateRangePresets;
use App\Support\CsvExporter;
use App\Support\PanelAccess;
use BackedEnum;
use Filament\Pages\Page;

class PaymentReport extends Page implements Schedulable
{
    use HasDateRangePresets;
    use HasScheduleAction;

    protected string $view = 'filament.pages.reports.payment-report';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationLabel = 'Payments Report';
    protected static string|\UnitEnum|null $navigationGroup = 'Reports';
    protected static ?int $navigationSort = 1;
    protected static ?string $slug = 'reports/payments';

    public static function canAccess(): bool
    {
        return PanelAccess::can('reports.view_payment');
    }

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return '';
    }

    // Schedulable contract

    public static function reportKey(): string
    {
        return 'payment';
    }

    public static function reportLabel(): string
    {
        return 'Payment';
    }

    public static function pdfView(): string
    {
        return 'reports.pdf.payment-report';
    }

    public static function filtersSummary(array $filters): string
    {
        $preset = $filters['preset'] ?? 'this_month';
        $summary = static::dateRangePresetOptions()[$preset] ?? ucfirst($preset);

        if (($filters['gateway'] ?? 'all') !== 'all') {
            $summary .= ' · ' . strtoupper($filters['gateway']);
        }

        if (($filters['status'] ?? 'all') !== 'all') {
            $summary .= ' · ' . ucfirst($filters['status']);
        }

        return $summary;
    }

    public static function csvHeaderAndRows(array $filters): array
    {
        $data = static::buildReportData($filters, paginate: false);

        $header = ['Transaction ID', 'Order #', 'Gateway', 'Amount', 'Currency', 'Status', 'Verification Attempts', 'Paid At', 'Failure Reason'];

        $rows = $data['payments']->map(fn (Payment $p) => [
            $p->transaction_id,
            $p->order?->order_number ?? '',
            strtoupper($p->payment_gateway?->value ?? ''),
            number_format((float) $p->amount, 2),
            $p->currency,
            $p->status?->value ?? '',
            $p->verification_attempts,
            $p->paid_at?->format('M d, Y H:i') ?? '',
            $p->failure_reason ?? '',
        ])->all();

        return [$header, $rows];
    }

    public static function pdfViewData(array $filters): array
    {
        $data = static::buildReportData($filters, paginate: false);

        return [
            'siteName' => Setting::get('site_name', config('app.name')),
            'title' => 'Payment Report',
            'filtersSummary' => static::filtersSummary($filters),
            'kpis' => $data['kpis'],
            'gatewayBreakdown' => $data['gatewayBreakdown'],
            'statusBreakdown' => $data['statusBreakdown'],
            'payments' => $data['payments']->take(200),
        ];
    }

    // Interactive page

    public function getViewData(): array
    {
        $filters = $this->currentFilters();

        return array_merge(
            $this->staticFilterMeta($filters),
            static::buildReportData($filters, paginate: true),
        );
    }

    public function exportCsv(): void
    {
        [$header, $rows] = static::csvHeaderAndRows($this->currentFilters());

        $this->dispatch('download-csv',
            content: CsvExporter::build($header, $rows),
            filename: 'payment-report-' . now()->format('Y-m-d') . '.csv',
        );
    }

    // Shared core

    private function currentFilters(): array
    {
        return [
            'preset' => request('preset', 'this_month'),
            'date_from' => request('date_from'),
            'date_to' => request('date_to'),
            'gateway' => request('gateway', 'all'),
            'status' => request('status', 'all'),
            'page' => (int) request('page', 1),
            'per_page' => (int) request('per_page', 10),
        ];
    }

    private function staticFilterMeta(array $filters): array
    {
        return [
            'activePreset' => $filters['preset'],
            'activeGateway' => $filters['gateway'],
            'activeStatus' => $filters['status'],
        ];
    }

    public static function buildReportData(array $filters, bool $paginate = true): array
    {
        $preset = $filters['preset'] ?? 'this_month';

        [$from, $to] = static::resolvePreset($preset, 'this_month', $filters['date_from'] ?? null, $filters['date_to'] ?? null);

        $gateway = $filters['gateway'] ?? 'all';
        $status = $filters['status'] ?? 'all';

        $base = function () use ($from, $to, $gateway, $status) {
            $q = Payment::query();
            static::applyDateRange($q, 'created_at', $from, $to);
            if ($gateway !== 'all') {
                $q->where('payment_gateway', $gateway);
            }
            if ($status !== 'all') {
                $q->where('status', $status);
            }
            return $q;
        };

        $paidStatuses = [PaymentStatus::Paid->value, PaymentStatus::Completed->value];

        $total = $base()->count();
        $totalPaidAmount = (clone $base())->whereIn('status', $paidStatuses)->sum('amount');
        $failedCount = (clone $base())->where('status', PaymentStatus::Failed->value)->count();
        $pendingCount = (clone $base())->whereIn('status', [PaymentStatus::Pending->value, PaymentStatus::Processing->value])->count();
        $refundedCount = (clone $base())->where('status', PaymentStatus::Refunded->value)->count();
        $paidCount = (clone $base())->whereIn('status', $paidStatuses)->count();
        $avgVerificationAttempts = round((float) (clone $base())->avg('verification_attempts'), 1);
        $successRate = $total > 0 ? round(($paidCount / $total) * 100) : 0;

        $gatewayBreakdown = collect(PaymentGateway::cases())->mapWithKeys(function (PaymentGateway $gw) use ($base) {
            $q = (clone $base())->where('payment_gateway', $gw->value);
            return [$gw->value => ['count' => $q->count(), 'amount' => (clone $q)->sum('amount')]];
        })->all();

        $statusBreakdown = collect([PaymentStatus::Paid, PaymentStatus::Pending, PaymentStatus::Failed, PaymentStatus::Refunded])
            ->mapWithKeys(function (PaymentStatus $st) use ($base) {
                $q = (clone $base())->where('status', $st->value);
                return [$st->value => ['count' => $q->count(), 'amount' => (clone $q)->sum('amount')]];
            })->all();

        $query = $base()->with(['order:id,order_number'])->orderByDesc('id');

        if ($paginate) {
            $page = max(1, (int) ($filters['page'] ?? 1));
            $perPage = in_array((int) ($filters['per_page'] ?? 10), [10, 25, 50]) ? (int) $filters['per_page'] : 10;
            $totalRows = (clone $query)->count();
            $totalPages = max(1, (int) ceil($totalRows / $perPage));
            $payments = $query->skip(($page - 1) * $perPage)->take($perPage)->get();
        } else {
            $payments = $query->get();
            $totalRows = $payments->count();
            $totalPages = 1;
            $page = 1;
            $perPage = $totalRows;
        }

        return [
            'kpis' => [
                'total' => $total,
                'totalPaidAmount' => $totalPaidAmount,
                'successRate' => $successRate,
                'failedCount' => $failedCount,
                'pendingCount' => $pendingCount,
                'refundedCount' => $refundedCount,
                'avgVerificationAttempts' => $avgVerificationAttempts,
            ],
            'gatewayBreakdown' => $gatewayBreakdown,
            'statusBreakdown' => $statusBreakdown,
            'payments' => $payments,
            'total' => $totalRows,
            'totalPages' => $totalPages,
            'curPage' => $page,
            'perPage' => $perPage,
        ];
    }
}
