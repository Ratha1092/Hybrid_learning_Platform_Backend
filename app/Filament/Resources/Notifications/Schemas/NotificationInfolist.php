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
                            ->formatStateUsing(fn ($state) => ucfirst(str_replace('_', ' ', $state ?? '')))
                            ->color(fn ($state) => match ($state) {
                                'instructor_verification' => 'warning',
                                'order' => 'success',
                                'payment' => 'info',
                                'course' => 'primary',
                                'finance' => 'danger',
                                'system' => 'gray',
                                default => 'gray',
                            }),

                    ])
                    ->columns(1),

                Section::make('Action')
                    ->schema([
                        TextEntry::make('action_url')
                            ->label('Action URL')
                            ->state(fn ($record) => $record->data['actions'][0]['url'] ?? null)
                            ->url(fn ($record) => $record->data['actions'][0]['url'] ?? null)
                            ->openUrlInNewTab()
                            ->placeholder('N/A'),
                        TextEntry::make('action_label')
                            ->label('Action Label')
                            ->state(fn ($record) => $record->data['actions'][0]['label'] ?? null)
                            ->placeholder('N/A'),
                    ])
                    ->columns(2),
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