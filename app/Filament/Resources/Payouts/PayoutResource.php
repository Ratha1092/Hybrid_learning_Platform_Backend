<?php

namespace App\Filament\Resources\Payouts;

use App\Domains\Finance\Models\PayoutRequest;
use App\Filament\Resources\Payouts\Pages\ViewPayout;
use App\Support\PanelAccess;
use BackedEnum;
use Filament\Resources\Resource;

class PayoutResource extends Resource
{
    protected static ?string $model = PayoutRequest::class;

    protected static ?string $slug = 'payout-requests';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static string|\UnitEnum|null $navigationGroup = 'Finance';

    protected static bool $shouldRegisterNavigation = false;

    public static function getPages(): array
    {
        return [
            'view' => ViewPayout::route('/{record}'),
        ];
    }

    public static function getIndexUrl(
        array $parameters = [],
        bool $isAbsolute = true,
        ?string $panel = null,
        ?\Illuminate\Database\Eloquent\Model $tenant = null,
        bool $shouldGuessMissingParameters = false
    ): string {
        return route('filament.admin.pages.payouts');
    }

    public static function canViewAny(): bool
    {
        return PanelAccess::can('payouts.view');
    }
}