<?php

namespace App\Filament\Resources\Payments\Pages;

use App\Filament\Resources\Payments\PaymentResource;
use App\Filament\Resources\Payments\Tables\PaymentsTable;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;

class ListPayments extends ListRecords
{
    protected static string $resource = PaymentResource::class;

    public function table(Table $table): Table
    {
        return PaymentsTable::configure($table);
    }
}