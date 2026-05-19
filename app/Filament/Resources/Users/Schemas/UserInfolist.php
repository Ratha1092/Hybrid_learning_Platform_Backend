<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;

use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;

use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema

            ->components([
                Section::make()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                ImageEntry::make('avatar')
                                    ->label('')
                                    ->circular()
                                    ->defaultImageUrl(
                                        'https://ui-avatars.com/api/?name='
                                    ),
                                Grid::make(1)
                                    ->schema([
                                        TextEntry::make('name')
                                            ->size('lg')
                                            ->weight('bold'),
                                        TextEntry::make('email'),
                                        TextEntry::make('role')
                                            ->badge()
                                            ->color(fn (string $state): string => match ($state) {
                                                'admin' => 'danger',
                                                'instructor' => 'warning',
                                                'student' => 'success',
                                                default => 'gray',
                                            }),
                                        TextEntry::make('status')
                                            ->badge()
                                            ->color(fn (string $state): string => match ($state) {
                                                'active' => 'success',
                                                'suspended' => 'danger',
                                                default => 'gray',
                                            }),
                                    ]),
                            ]),
                    ]),
                Section::make('Account Information')
                    ->schema([
                        TextEntry::make('id')
                            ->label('User ID'),
                        TextEntry::make('phone')
                            ->placeholder('No phone'),
                        TextEntry::make('email_verified_at')
                            ->dateTime()
                            ->placeholder('Not verified'),
                        TextEntry::make('created_at')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->dateTime(),
                    ])

                    ->columns(2),
                Section::make('Instructor Status')
                    ->schema([
                        TextEntry::make('instructor_status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'verified' => 'success',
                                'pending' => 'warning',
                                'rejected' => 'danger',
                                'not_instructor' => 'gray',
                                default => 'gray',
                            }),
                        TextEntry::make('two_factor_enabled')
                            ->badge()
                            ->formatStateUsing(
                                fn (bool $state): string =>
                                $state ? 'Enabled' : 'Disabled'
                            )
                            ->color(
                                fn (bool $state): string =>
                                $state ? 'success' : 'gray'
                            ),
                    ])
                    ->columns(2),
            ]);
    }
}