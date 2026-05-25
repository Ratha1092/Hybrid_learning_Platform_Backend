<?php

namespace App\Filament\Resources\Notifications\Pages;

use App\Filament\Resources\Notifications\NotificationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Notifications\DatabaseNotification;

class ListNotifications extends ListRecords
{
    protected static string $resource = NotificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('markAllAsRead')
                ->label('Mark all as read')
                ->icon('heroicon-o-check-circle')
                ->action(fn () => DatabaseNotification::whereNull('read_at')->update(['read_at' => now()])),
        ];
    }
}
