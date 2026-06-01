<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Back to Orders')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(OrderResource::getIndexUrl()),

            EditAction::make()
                ->icon('heroicon-o-pencil-square'),
        ];
    }
}
