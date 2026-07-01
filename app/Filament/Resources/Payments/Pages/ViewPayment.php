<?php

namespace App\Filament\Resources\Payments\Pages;

use App\Filament\Resources\Payments\PaymentResource;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ViewPayment extends ViewRecord
{
    protected static string $resource = PaymentResource::class;
    protected string $view = 'filament.resources.payments.view-payment';

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function resolveRecord(int|string $key): Model
    {
        // Non-numeric keys (e.g. a stale bookmark to the old /create route) would
        // otherwise hit the DB with an invalid bigint cast — fail clean with a 404 instead.
        if (!is_numeric($key)) {
            throw (new ModelNotFoundException)->setModel($this->getModel(), [$key]);
        }

        return parent::resolveRecord($key);
    }
}
