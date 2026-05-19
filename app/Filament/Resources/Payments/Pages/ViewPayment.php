<?php

namespace App\Filament\Resources\Payments\Pages;

use App\Filament\Resources\Payments\PaymentResource;
use App\Filament\Resources\Payments\Schemas\PaymentInfolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;

class ViewPayment extends ViewRecord
{
    protected static string $resource = PaymentResource::class;

    public function infolist(Schema $schema): Schema
    {
        return PaymentInfolist::configure($schema);
    }
}