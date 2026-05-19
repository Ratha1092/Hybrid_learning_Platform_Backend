<?php

namespace App\Filament\Resources\Payments;

use App\Domains\Payments\Models\Payment;
use App\Filament\Resources\Payments\Pages;
use Filament\Resources\Resource;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationLabel = 'Payments';
    protected static string|\UnitEnum|null $navigationGroup = 'Commerce';
    protected static ?int $navigationSort = 2;

    public static function getPages(): array
    {
        return [

            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'view' => Pages\ViewPayment::route('/{record}'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),

        ];
    }
}