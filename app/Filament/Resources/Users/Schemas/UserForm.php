<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Profile')
                    ->description('Personal information and contact details')
                    ->icon('heroicon-o-user-circle')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Full Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->prefixIcon('heroicon-o-user')
                                    ->placeholder('John Doe'),

                                TextInput::make('email')
                                    ->label('Email Address')
                                    ->email()
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255)
                                    ->prefixIcon('heroicon-o-envelope')
                                    ->placeholder('john@example.com'),

                                TextInput::make('phone')
                                    ->label('Phone Number')
                                    ->tel()
                                    ->maxLength(20)
                                    ->prefixIcon('heroicon-o-phone')
                                    ->placeholder('+1 (555) 000-0000'),
                            ]),
                    ]),

                Section::make('Access Control')
                    ->description('Manage roles and account status')
                    ->icon('heroicon-o-shield-check')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('roles')
                                    ->label('Assigned Roles')
                                    ->relationship('roles', 'name')
                                    ->multiple()
                                    ->preload()
                                    ->searchable()
                                    ->required()
                                    ->prefixIcon('heroicon-o-identification'),

                                Select::make('status')
                                    ->label('Account Status')
                                    ->options([
                                        'active'    => 'Active',
                                        'suspended' => 'Suspended',
                                    ])
                                    ->default('active')
                                    ->required()
                                    ->native(false)
                                    ->prefixIcon('heroicon-o-check-badge'),
                            ]),
                    ]),

                Section::make('Security')
                    ->description('Leave blank to keep the current password unchanged')
                    ->icon('heroicon-o-lock-closed')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('password')
                                    ->label('New Password')
                                    ->password()
                                    ->revealable()
                                    ->required(fn ($livewire) => $livewire instanceof CreateRecord)
                                    ->dehydrateStateUsing(fn ($state) => bcrypt($state))
                                    ->dehydrated(fn ($state) => filled($state))
                                    ->maxLength(255)
                                    ->prefixIcon('heroicon-o-key')
                                    ->placeholder('••••••••'),
                            ]),
                    ]),

            ]);
    }
}
