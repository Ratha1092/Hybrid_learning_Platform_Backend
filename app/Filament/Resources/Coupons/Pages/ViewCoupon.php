<?php

namespace App\Filament\Resources\Coupons\Pages;

use App\Filament\Resources\Coupons\CouponResource;
use Filament\Resources\Pages\ViewRecord;

class ViewCoupon extends ViewRecord
{
    protected static string $resource = CouponResource::class;
    protected string $view = 'filament.resources.coupons.view-coupon';

    protected function getHeaderActions(): array
    {
        return [];
    }
}
