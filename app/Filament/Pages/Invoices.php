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

    protected function getViewData(): array
    {
        $search  = request('search', '');
        $type    = request('type', '');
        $status  = request('status', '');
        $page    = max(1, (int) request('page', 1));
        $perPage = (int) request('per_page', 10);

        if (!in_array($perPage, [10, 25, 50])) {
            $perPage = 10;
        }

        $query = Invoice::with([
            'order:id,order_number,user_id',
            'order.user:id,name,email',
        ]);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('order', function ($q2) use ($search) {
                        $q2->where('order_number', 'like', "%{$search}%")
                            ->orWhereHas('user', function ($q3) use ($search) {
                                $q3->where('name', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%");
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
