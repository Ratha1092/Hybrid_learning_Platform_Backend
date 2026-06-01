<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
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

            ViewAction::make()
                ->icon('heroicon-o-eye'),

            DeleteAction::make()
                ->icon('heroicon-o-trash'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getIndexUrl();
    }
}
