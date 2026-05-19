<?php

namespace App\Filament\Resources\Orders;

use App\Domains\Orders\Models\Order;
use App\Filament\Resources\Orders\Pages;
use Filament\Resources\Resource;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationLabel = 'Orders';
    protected static string|\UnitEnum|null $navigationGroup = 'Commerce';
    protected static ?int $navigationSort = 1;

    public static function getPages(): array
    {
        return [

            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}