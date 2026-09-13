<?php

namespace App\Filament\Resources\Payouts\Pages;

use App\Filament\Resources\Payouts\PayoutResource;
use Filament\Resources\Pages\ViewRecord;

class ViewPayout extends ViewRecord
{
    protected static string $resource = PayoutResource::class;

    protected string $view = 'filament.resources.payouts.view-payout';

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }

    protected function getViewData(): array
    {
        return [
            'backUrl' => route('filament.admin.pages.payouts'),
        ];
    }
}