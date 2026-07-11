<?php

namespace App\Filament\Pages;

use App\Domains\Billing\Services\InvoiceService;
use App\Domains\Finance\Models\InstructorWallet;
use App\Domains\Finance\Models\RevenueShare;
use App\Domains\Finance\Models\WalletTransaction;
use App\Domains\Orders\Enums\OrderPaymentStatus;
use App\Domains\Orders\Enums\OrderStatus;
use App\Domains\Orders\Models\Order;
use App\Domains\Orders\Models\Refund;
use App\Domains\Payments\Enums\PaymentStatus;
use App\Domains\System\Models\Setting;
use App\Support\PanelAccess;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class Refunds extends Page
{
    protected string $view = 'filament.pages.refunds';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-receipt-refund';
    protected static ?string $navigationLabel = 'Refunds';
    protected static string|\UnitEnum|null $navigationGroup = 'Commerce';
    protected static ?int $navigationSort = 4;
    protected static ?string $slug = 'refunds';

    public static function canAccess(): bool
    {
        return PanelAccess::can('orders.view');
    }

    public static function getNavigationBadge(): ?string
    {
        $eligible = Order::where('payment_status', OrderPaymentStatus::Paid)
            ->where('status', '!=', OrderStatus::Refunded)
            ->exists();

        return $eligible ? '•' : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return '';
    }

    public string $tab = 'eligible';
    public string $search = '';
    public int $page = 1;
    public int $perPage = 10;

    public function updatedSearch(): void
    {
        $this->page = 1;
    }

    public function selectTab(string $tab): void
    {
        $this->tab = $tab;
        $this->page = 1;
    }

    public function setPerPage(int $perPage): void
    {
        $this->perPage = in_array($perPage, [10, 25, 50], true) ? $perPage : 10;
        $this->page = 1;
    }

    public function gotoPage(int $page): void
    {
        $this->page = max(1, $page);
    }

    public function refund(int $orderId, string $reason): void
    {
        if (!PanelAccess::can('orders.update')) {
            return;
        }

        $reason = trim($reason);
        if ($reason === '') {
            Notification::make()->title('A refund reason is required.')->danger()->send();
            return;
        }

        $order = Order::with('items')->findOrFail($orderId);

        if ($order->status === OrderStatus::Refunded) {
            Notification::make()->title('This order has already been refunded.')->warning()->send();
            return;
        }

        if ($order->payment_status !== OrderPaymentStatus::Paid) {
            Notification::make()->title('Only paid orders can be refunded.')->danger()->send();
            return;
        }

        $refundPeriodDays = (int) Setting::get('refund_period_days', 30);
        if ($refundPeriodDays > 0 && $order->paid_at?->diffInDays(now()) > $refundPeriodDays) {
            Notification::make()
                ->title('Refund window has expired.')
                ->body("This order was paid more than {$refundPeriodDays} days ago and is outside the refund period.")
                ->danger()
                ->send();
            return;
        }

        DB::transaction(function () use ($order, $reason) {
            $order->update([
                'status' => OrderStatus::Refunded,
                'payment_status' => OrderPaymentStatus::Refunded,
                'refunded_at' => now(),
            ]);

            $order->payments()
                ->whereIn('status', [PaymentStatus::Paid->value, PaymentStatus::Completed->value])
                ->update(['status' => PaymentStatus::Refunded->value]);

            foreach ($order->items as $item) {
                $item->update([
                    'is_refunded' => true,
                    'refunded_at' => now(),
                ]);

                $revenueShare = RevenueShare::where('order_item_id', $item->id)
                    ->where('status', 'distributed')
                    ->first();

                if (!$revenueShare) {
                    continue;
                }

                $wallet = InstructorWallet::where('instructor_id', $revenueShare->instructor_id)
                    ->lockForUpdate()
                    ->first();

                if ($wallet) {
                    $wallet->decrement('balance', $revenueShare->instructor_amount);
                }

                WalletTransaction::create([
                    'instructor_id' => $revenueShare->instructor_id,
                    'amount' => $revenueShare->instructor_amount,
                    'type' => 'debit',
                    'status' => 'completed',
                    'revenue_share_id' => $revenueShare->id,
                    'description' => "Refund reversal — order #{$order->order_number}",
                ]);

                $revenueShare->update(['status' => 'reversed']);
            }

            Refund::create([
                'order_id' => $order->id,
                'amount' => $order->final_amount,
                'reason' => $reason,
                'refunded_by' => auth()->id(),
            ]);

            try {
                app(InvoiceService::class)->issueCreditNote($order, $reason);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Credit note generation failed', [
                    'order_id' => $order->id,
                    'error'    => $e->getMessage(),
                ]);
            }
        });

        Notification::make()
            ->title('Order Refunded')
            ->body("Order #{$order->order_number} has been refunded. Any distributed instructor earnings were reversed.")
            ->success()
            ->send();
    }

    protected function getViewData(): array
    {
        $tab     = $this->tab;
        $search  = $this->search;
        $page    = max(1, $this->page);
        $perPage = in_array($this->perPage, [10, 25, 50], true) ? $this->perPage : 10;

        $eligibleCount = Order::where('payment_status', OrderPaymentStatus::Paid)
            ->where('status', '!=', OrderStatus::Refunded)
            ->count();
        $refundedCount = Order::where('status', OrderStatus::Refunded)->count();

        $tabs = [
            ['key' => 'eligible', 'label' => 'Eligible',  'count' => $eligibleCount, 'color' => '#fbbf24'],
            ['key' => 'refunded', 'label' => 'Refunded',  'count' => $refundedCount, 'color' => '#f87171'],
        ];

        $query = Order::with('user:id,name,email');

        if ($tab === 'refunded') {
            $query->where('status', OrderStatus::Refunded);
        } else {
            $tab = 'eligible';
            $query->where('payment_status', OrderPaymentStatus::Paid)
                ->where('status', '!=', OrderStatus::Refunded);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('user', fn ($q2) => $q2->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%"));
            });
        }

        $query->orderBy('id', 'desc');

        $total      = $query->count();
        $totalPages = max(1, (int) ceil($total / $perPage));
        $curPage    = min($page, $totalPages);
        $orders     = $query->skip(($curPage - 1) * $perPage)->take($perPage)->get();

        $reasons = Refund::whereIn('order_id', $orders->pluck('id'))
            ->get()
            ->keyBy('order_id');

        $canUpdate = PanelAccess::can('orders.update');

        return compact('tabs', 'tab', 'search', 'orders', 'reasons', 'total', 'totalPages', 'curPage', 'perPage', 'canUpdate');
    }
}
