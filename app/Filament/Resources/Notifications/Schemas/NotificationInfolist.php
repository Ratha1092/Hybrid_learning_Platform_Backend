<?php

namespace App\Filament\Resources\Notifications\Schemas;

use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class NotificationInfolist
{
    public static function configure(Schema $schema): 
    Schema {
        return $schema
            ->components([
                Section::make('Notification')
                    ->schema([
                        TextEntry::make('data.title')
                            ->label('Title')
                            ->weight('bold')
                            ->size('lg'),
                        TextEntry::make('data.message')
                            ->label('Message')
                            ->wrap(),
                        TextEntry::make('data.type')
                            ->label('Notification Type')
                            ->badge()
                            ->color(fn ($state) => match ($state) {
                                'InstructorVerification' => 'warning',
                                'Order' => 'success',
                                'Payment' => 'info',
                                default => 'gray',
                            }),

                    ])
                    ->columns(1),

                Section::make('Resource')
                    ->schema([
                        TextEntry::make('data.resource_type')
                            ->label('Resource Type'),
                        TextEntry::make('data.resource_id')
                            ->label('Resource ID'),
                        TextEntry::make('data.action_url')
                            ->label('Action URL')
                            ->url(
                                fn ($record) =>
                                    $record->data['action_url']
                                    ?? null
                            )
                            ->openUrlInNewTab()
                            ->placeholder('N/A'),

                    ])
                    ->columns(3),
                Section::make('Status')
                    ->schema([
                        TextEntry::make('read_at')
                            ->label('Read At')
                            ->dateTime()
                            ->placeholder('Unread')
                            ->badge()
                            ->color(fn ($state) =>
                                $state
                                    ? 'success'
                                    : 'warning'
                            ),
                        TextEntry::make('created_at')
                            ->label('Created')
                            ->since(),
                    ])
                    ->columns(2),

            ]);
    }
}