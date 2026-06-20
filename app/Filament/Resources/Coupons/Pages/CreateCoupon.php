<?php

namespace App\Filament\Resources\Coupons\Pages;

use App\Domains\Auth\Services\ActivityLogService;
use App\Filament\Resources\Coupons\CouponResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateCoupon extends CreateRecord
{
    protected static string $resource = CouponResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Back to Coupons')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(route('filament.admin.pages.coupons')),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        return $data;
    }

    protected function afterCreate(): void
    {
        ActivityLogService::logChange('coupon.created', $this->record, [], $this->record->only(['code', 'type', 'value']));
    }

    protected function getRedirectUrl(): string
    {
        return route('filament.admin.pages.coupons');
    }
}
