<?php

namespace App\Filament\Pages;

use App\Domains\Billing\Models\Receipt;
use App\Support\PanelAccess;
use BackedEnum;
use Filament\Pages\Page;

class Receipts extends Page
{
    protected string $view = 'filament.pages.receipts';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-receipt-percent';
    protected static ?string $navigationLabel = 'Receipts';
    protected static string|\UnitEnum|null $navigationGroup = 'Finance';
    protected static ?int $navigationSort = 4;
    protected static ?string $slug = 'receipts';

    public static function canAccess(): bool
    {
        return PanelAccess::can('receipts.view');
    }

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return '';
    }

    public string $search = '';
    public string $gateway = '';
    public int $page = 1;
    public int $perPage = 10;

    public function updatedSearch(): void
    {
        $this->page = 1;
    }

    public function updatedGateway(): void
    {
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

    protected function getViewData(): array
    {
        $search  = $this->search;
        $gateway = $this->gateway;
        $page    = max(1, $this->page);
        $perPage = in_array($this->perPage, [10, 25, 50], true) ? $this->perPage : 10;

        $query = Receipt::with([
            'order:id,order_number,user_id',
            'order.user:id,name,email',
        ]);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('receipt_number', 'like', "%{$search}%")
                    ->orWhereHas('order', function ($q2) use ($search) {
                        $q2->where('order_number', 'like', "%{$search}%")
                            ->orWhereHas('user', function ($q3) use ($search) {
                                $q3->where('name', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%");
                            });
                    });
            });
        }

        if ($gateway) {
            $query->where('payment_gateway', $gateway);
        }

        $query->orderBy('id', 'desc');

        $total      = $query->count();
        $totalPages = max(1, (int) ceil($total / $perPage));
        $curPage    = min($page, $totalPages);
        $receipts   = $query->skip(($curPage - 1) * $perPage)->take($perPage)->get();

        $gateways   = Receipt::distinct()->pluck('payment_gateway')->filter()->sort()->values();
        $canDownload = PanelAccess::can('receipts.download');
        $canResend   = PanelAccess::can('receipts.resend');

        return compact(
            'search', 'gateway', 'gateways',
            'receipts', 'total', 'totalPages', 'curPage', 'perPage',
            'canDownload', 'canResend'
        );
    }
}
