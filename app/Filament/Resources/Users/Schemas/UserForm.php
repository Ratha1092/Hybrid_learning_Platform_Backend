<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Full Name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                Select::make('role')
                    ->options([
                        'admin' => 'Admin',
                        'instructor' => 'Instructor',
                        'student' => 'Student',
                    ])
                    ->required()
                    ->default('student'),

                TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->required(fn ($livewire) => $livewire instanceof CreateRecord)
                    ->dehydrateStateUsing(fn ($state) => bcrypt($state))
                    ->dehydrated(fn ($state) => filled($state))
                    ->maxLength(255),
            ]);
    }
}