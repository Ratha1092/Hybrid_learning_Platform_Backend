<?php

namespace App\Filament\Resources\ActivityLogs;

use App\Domains\Auth\Models\ActivityLog;
use App\Filament\Resources\ActivityLogs\Pages\ViewActivityLog;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ActivityLogResource extends Resource
{
    protected static ?string $model = ActivityLog::class;
    protected static ?string $slug = 'audit-log-entries';
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([]);
    }

    public static function getPages(): array
    {
        return [
            'view' => ViewActivityLog::route('/{record}'),
        ];
    }
}
