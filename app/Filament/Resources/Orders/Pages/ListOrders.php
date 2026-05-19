<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use App\Filament\Resources\Orders\Tables\OrdersTable;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    public function table(Table $table): Table
    {
        return OrdersTable::configure($table);
    }
}