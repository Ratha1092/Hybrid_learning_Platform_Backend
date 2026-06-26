<?php

namespace App\Filament\Resources\Billing\Pages;

use App\Filament\Resources\Billing\ReceiptResource;
use Filament\Resources\Pages\ListRecords;

class ListReceipts extends ListRecords
{
    protected static string $resource = ReceiptResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
