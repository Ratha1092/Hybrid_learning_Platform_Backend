<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('role')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'danger',
                        'instructor' => 'warning',
                        'student' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'suspended' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('instructor_status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'verified' => 'success',
                        'pending' => 'warning',
                        'rejected' => 'danger',
                        'not_instructor' => 'gray',
                        default => 'gray',
                    })
                    ->label('Instructor'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('M d, Y')
                    ->sortable(),
            ])

            ->filters([
                //
            ])
            ->recordActions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
                Actions\Action::make('suspend')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->visible(
                        fn ($record) =>
                        $record->status === 'active'
                    )
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update([
                            'status' => 'suspended',
                        ]);
                        Notification::make()
                            ->title('User Suspended')
                            ->danger()
                            ->send();
                    }),
                Actions\Action::make('activate')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(
                        fn ($record) =>
                        $record->status === 'suspended'
                    )
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update([
                            'status' => 'active',
                        ]);
                        Notification::make()
                            ->title('User Activated')
                            ->success()
                            ->send();
                    }),
                Actions\Action::make('approveInstructor')
                    ->label('Approve')
                    ->icon('heroicon-o-academic-cap')
                    ->color('success')
                    ->visible(
                        fn ($record) =>
                        $record->instructor_status === 'pending'
                    )

                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update([
                            'role' => 'instructor',

                            'instructor_status' => 'verified',
                        ]);
                        Notification::make()
                            ->title('Instructor Approved')
                            ->success()
                            ->send();
                    }),
                Actions\Action::make('rejectInstructor')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(
                        fn ($record) =>
                        $record->instructor_status === 'pending'
                    )
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update([
                            'instructor_status' => 'rejected',
                        ]);
                        Notification::make()
                            ->title('Instructor Rejected')
                            ->danger()
                            ->send();
                    }),
            ]);
    }
}