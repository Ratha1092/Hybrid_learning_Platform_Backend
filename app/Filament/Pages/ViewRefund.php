<?php

namespace App\Filament\Pages;

use App\Domains\Orders\Models\Refund;
use App\Support\PanelAccess;
use Filament\Pages\Page;

class ViewRefund extends Page
{
    protected string $view = 'filament.pages.view-refund';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'refunds/{refund}';

    public static function canAccess(): bool
    {
        return PanelAccess::can('orders.view');
    }

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return '';
    }

    protected function getViewData(): array
    {
        $refund = Refund::with(['order.user', 'order.items', 'refundedBy'])
            ->findOrFail(request()->route('refund'));

        return compact('refund');
    }
}
