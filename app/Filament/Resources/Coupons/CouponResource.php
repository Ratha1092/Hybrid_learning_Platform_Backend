<?php

namespace App\Filament\Resources\Coupons;

use App\Domains\Promotions\Models\Coupon;
use App\Filament\Resources\Coupons\Pages\CreateCoupon;
use App\Filament\Resources\Coupons\Pages\EditCoupon;
use App\Filament\Resources\Coupons\Pages\ViewCoupon;
use App\Filament\Resources\Coupons\Schemas\CouponForm;
use App\Support\PanelAccess;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTicket;
    protected static ?string $navigationLabel = 'Coupons';
    protected static ?string $modelLabel = 'Coupon';
    protected static ?string $pluralModelLabel = 'Coupons';
    protected static string|\UnitEnum|null $navigationGroup = 'Commerce';
    protected static ?int $navigationSort = 3;
    protected static ?string $recordTitleAttribute = 'code';
    protected static bool $shouldRegisterNavigation = false;

    public static function getGloballySearchableAttributes(): array
    {
        return ['code', 'description'];
    }

    public static function getGlobalSearchResultDetails(\Illuminate\Database\Eloquent\Model $record): array
    {
        return [
            'Discount' => $record->type === 'percentage' ? $record->value . '%' : '$' . number_format($record->value, 2),
            'Status'   => $record->is_active ? 'Active' : 'Inactive',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return CouponForm::configure($schema);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'create' => CreateCoupon::route('/create'),
            'view'   => ViewCoupon::route('/{record}'),
            'edit'   => EditCoupon::route('/{record}/edit'),
        ];
    }

    public static function getIndexUrl(array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?\Illuminate\Database\Eloquent\Model $tenant = null, bool $shouldGuessMissingParameters = false): string
    {
        return route('filament.admin.pages.coupons');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class]);
    }

    public static function canViewAny(): bool
    {
        return PanelAccess::can('coupons.view');
    }

    public static function canCreate(): bool
    {
        return PanelAccess::can('coupons.create');
    }

    public static function canEdit($record): bool
    {
        return PanelAccess::can('coupons.update');
    }

    public static function canDelete($record): bool
    {
        return PanelAccess::can('coupons.delete');
    }
}
