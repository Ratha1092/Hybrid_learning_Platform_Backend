<?php

namespace App\Filament\Pages;

use App\Domains\Payments\Models\Payment;
use App\Domains\Payments\Services\BakongKhqrService;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
class Payments extends Page
{
    protected string $view = 'filament.pages.payments';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationLabel = 'Payments';
    protected static string|\UnitEnum|null $navigationGroup = 'Marketplace';
    protected static ?int $navigationSort = 2;
    protected static ?string $slug = 'payments';

    public string $activeTab   = 'all';
    public string $search      = '';
    public int    $currentPage = 1;
    public int    $perPage     = 10;

    public function mount(): void
    {
        $this->activeTab   = request('tab', 'all');
        $this->search      = request('search', '');
        $this->currentPage = max(1, (int) request('page', 1));
        $this->perPage     = in_array((int) request('per_page', 10), [10, 25, 50])
            ? (int) request('per_page', 10) : 10;
    }

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return '';
    }

    public function setTab(string $tab): void
    {
        $this->activeTab   = $tab;
        $this->currentPage = 1;
    }

    public function setPage(int $page): void
    {
        $this->currentPage = $page;
    }

    public function setPerPage(int $perPage): void
    {
        $this->perPage     = $perPage;
        $this->currentPage = 1;
    }

    public function forceVerify(int $paymentId): void
    {
        try {
            $payment = Payment::findOrFail($paymentId);
            $result  = app(BakongKhqrService::class)->forceVerifyPayment($payment);

            if ($result->isPaid()) {
                Notification::make()->title('Payment marked as Paid')->success()->send();
            } else {
                Notification::make()
                    ->title('Not confirmed by Bakong yet')
                    ->warning()
                    ->body('Status: ' . ($result->status?->value ?? 'unknown'))
                    ->send();
            }
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Verification failed')
                ->danger()
                ->body($e->getMessage())
                ->send();
        }
    }

    protected function getViewData(): array
    {
        $tab     = $this->activeTab;
        $search  = $this->search;
        $page    = max(1, $this->currentPage);
        $perPage = in_array($this->perPage, [10, 25, 50]) ? $this->perPage : 10;

        $base = fn() => Payment::query();

        $tabs = [
            ['key' => 'all',      'label' => 'All',      'count' => $base()->count(),                                               'color' => '#7c3aed'],
            ['key' => 'pending',  'label' => 'Pending',  'count' => $base()->whereIn('status', ['pending', 'processing'])->count(), 'color' => '#fbbf24'],
            ['key' => 'paid',     'label' => 'Paid',     'count' => $base()->whereIn('status', ['paid', 'completed'])->count(),     'color' => '#34d399'],
            ['key' => 'failed',   'label' => 'Failed',   'count' => $base()->whereIn('status', ['failed', 'expired'])->count(),     'color' => '#f87171'],
            ['key' => 'refunded', 'label' => 'Refunded', 'count' => $base()->where('status', 'refunded')->count(),                  'color' => '#a78bfa'],
        ];

        $query = Payment::with(['order:id,order_number,user_id', 'order.user:id,name']);

        if ($tab === 'pending') {
            $query->whereIn('status', ['pending', 'processing']);
        } elseif ($tab === 'paid') {
            $query->whereIn('status', ['paid', 'completed']);
        } elseif ($tab === 'failed') {
            $query->whereIn('status', ['failed', 'expired']);
        } elseif ($tab === 'refunded') {
            $query->where('status', 'refunded');
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('order', fn($q2) => $q2->where('order_number', 'like', "%{$search}%"));
            });
        }

        $query->orderBy('id', 'desc');

        $total      = $query->count();
        $totalPages = max(1, (int) ceil($total / $perPage));
        $curPage    = min($page, $totalPages);
        $payments   = $query->skip(($curPage - 1) * $perPage)->take($perPage)->get();

        return compact('tabs', 'tab', 'search', 'payments', 'total', 'totalPages', 'curPage', 'perPage');
    }
}
