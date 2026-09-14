<?php

namespace App\Filament\Pages;

use App\Domains\Billing\Models\Invoice;
use App\Support\PanelAccess;
use BackedEnum;
use Filament\Pages\Page;

class Invoices extends Page
{
    protected string $view = 'filament.pages.invoices';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Invoices';
    protected static string|\UnitEnum|null $navigationGroup = 'Finance';
    protected static ?int $navigationSort = 3;
    protected static ?string $slug = 'invoices';

    public static function canAccess(): bool
    {
        return PanelAccess::can('invoices.view');
    }

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return '';
    }

    public string $search = '';
    public string $type = '';
    public string $status = '';
    public int $page = 1;
    public int $perPage = 10;

    public function updatedSearch(): void
    {
        $this->page = 1;
    }

    public function updatedType(): void
    {
        $this->page = 1;
    }

    public function updatedStatus(): void
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
        $type    = $this->type;
        $status  = $this->status;
        $page    = max(1, $this->page);
        $perPage = in_array($this->perPage, [10, 25, 50], true) ? $this->perPage : 10;

        $query = Invoice::with([
            'order:id,order_number,user_id',
            'order.user:id,name,email',
        ]);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'ilike', "%{$search}%")
                    ->orWhereHas('order', function ($q2) use ($search) {
                        $q2->where('order_number', 'ilike', "%{$search}%")
                            ->orWhereHas('user', function ($q3) use ($search) {
                                $q3->where('name', 'ilike', "%{$search}%")
                                    ->orWhere('email', 'ilike', "%{$search}%");
                            });
                    });
            });
        }

        if ($type && in_array($type, ['invoice', 'credit_note'])) {
            $query->where('type', $type);
        }

        if ($status && in_array($status, ['issued', 'void'])) {
            $query->where('status', $status);
        }

        $query->orderBy('id', 'desc');

        $total      = $query->count();
        $totalPages = max(1, (int) ceil($total / $perPage));
        $curPage    = min($page, $totalPages);
        $invoices   = $query->skip(($curPage - 1) * $perPage)->take($perPage)->get();

        $canDownload    = PanelAccess::can('invoices.download');
        $canResend      = PanelAccess::can('invoices.resend');

        return compact(
            'search', 'type', 'status',
            'invoices', 'total', 'totalPages', 'curPage', 'perPage',
            'canDownload', 'canResend'
        );
    }
}
