<?php

namespace App\Filament\Resources\Notifications;

use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Notifications\DatabaseNotification;
use App\Filament\Resources\Notifications\Pages\ListNotifications;
use App\Filament\Resources\Notifications\Pages\ViewNotification;
use App\Filament\Resources\Notifications\Tables\NotificationsTable;
use BackedEnum;
use UnitEnum;
use Filament\Schemas\Schema;
use App\Filament\Resources\Notifications\Schemas\NotificationForm;

class NotificationResource extends Resource
{
    protected static ?string $model = DatabaseNotification::class;
    protected static string|BackedEnum|null $navigationIcon ='heroicon-o-bell-alert';
    protected static ?string $navigationLabel ='Notification Logs';
    protected static string|\UnitEnum|null $navigationGroup ='System';
    protected static ?int $navigationSort = 1;
    public static function table(
        Table $table
    ): Table {
        return NotificationsTable::configure($table);
    }
    public static function getRelations(): array
    {
        return [];
    }
    public static function getPages(): array
    {
        return [
            'index' => ListNotifications::route('/'),

            'view' => ViewNotification::route('/{record}'),
        ];
    }
    public static function canGloballySearch(): bool
    {
        return false;
    }
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->check();
    }
    public static function form(Schema $schema): Schema
    {
        return NotificationForm::configure($schema);
    }
}