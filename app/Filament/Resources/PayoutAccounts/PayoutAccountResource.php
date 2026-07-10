<?php

namespace App\Filament\Resources\PayoutAccounts;

use App\Domains\Finance\Models\InstructorPayoutAccount;
use App\Filament\Resources\PayoutAccounts\Pages;
use App\Support\PanelAccess;
use BackedEnum;
use Filament\Resources\Resource;

class PayoutAccountResource extends Resource
{
    protected static ?string $model = InstructorPayoutAccount::class;
    protected static ?string $slug = 'payout-accounts';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-qr-code';
    protected static string|\UnitEnum|null $navigationGroup = 'Finance';

    protected static bool $shouldRegisterNavigation = false;

    public static function getIndexUrl(array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?\Illuminate\Database\Eloquent\Model $tenant = null, bool $shouldGuessMissingParameters = false): string
    {
        return route('filament.admin.pages.payout-accounts');
    }

    public static function getPages(): array
    {
        return [
            'view' => Pages\ViewPayoutAccount::route('/{record}'),
        ];
    }

    public static function canViewAny(): bool
    {
        return PanelAccess::can('payout_accounts.view');
    }

    public static function getModelLabel(): string
    {
        return 'Payout Account';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Payout Accounts';
    }
}
