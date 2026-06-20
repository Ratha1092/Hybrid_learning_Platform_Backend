<?php

namespace App\Filament\Resources\Coupons\Pages;

use App\Domains\Auth\Services\ActivityLogService;
use App\Filament\Resources\Coupons\CouponResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCoupon extends EditRecord
{
    protected static string $resource = CouponResource::class;

    protected array $originalState = [];

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Back to Coupons')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(route('filament.admin.pages.coupons')),
            DeleteAction::make()
                ->visible(fn () => CouponResource::canDelete($this->record)),
        ];
    }

    protected function fillForm(): void
    {
        parent::fillForm();

        $this->originalState = $this->record->only(['code', 'type', 'value', 'is_active', 'max_uses', 'max_uses_per_user']);
    }

    protected function afterSave(): void
    {
        $newState = $this->record->only(['code', 'type', 'value', 'is_active', 'max_uses', 'max_uses_per_user']);

        ActivityLogService::logChange('coupon.updated', $this->record, $this->originalState, $newState);
    }

    protected function getRedirectUrl(): string
    {
        return route('filament.admin.resources.coupons.view', ['record' => $this->record->id]);
    }
}
