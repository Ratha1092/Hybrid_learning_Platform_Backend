<?php

namespace App\Filament\Resources\Notifications\Tables;

use Filament\Actions;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Notifications\DatabaseNotification;

class NotificationsTable
{
    public static function configure(
        Table $table
    ): Table {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('data.title')
                    ->label('Title')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('data.message')
                    ->label('Message')
                    ->wrap()
                    ->searchable()
                    ->limit(80),

                Tables\Columns\TextColumn::make('data.type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(
                        fn ($state) =>
                        ucfirst(
                            str_replace('_', ' ', $state)
                        )
                    )
                    ->color(fn ($state) => match ($state) {
                        'order' => 'success',
                        'payment' => 'info',
                        'instructor_verification' => 'warning',
                        'finance' => 'danger',
                        'course' => 'primary',
                        'system' => 'gray',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('read_at')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(
                        fn ($state) =>
                        $state
                            ? 'Read'
                            : 'Unread'
                    )
                    ->color(fn ($state) =>
                        $state
                            ? 'gray'
                            : 'warning'
                    ),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Received')
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Notification Type')
                    ->options([
                        'instructor_verification' =>'Instructor Verifications',
                        'order' => 'Orders',
                        'payment' => 'Payments',
                        'course' => 'Courses',
                        'finance' => 'Finance',
                        'system' => 'System',
                    ])
                    ->query(function ($query, array $data) {

                        if (! filled($data['value'])) {
                            return $query;
                        }
                        return $query->where(
                            'data->type',
                            $data['value']
                        );
                    }),
                Tables\Filters\SelectFilter::make('read_status')
                    ->label('Read Status')
                    ->options([
                        'unread' => 'Unread',
                        'read' => 'Read',
                    ])
                    ->query(function ($query, array $data) {

                        if (! filled($data['value'])) {
                            return $query;
                        }
                        return $data['value'] === 'read'
                            ? $query->whereNotNull('read_at')
                            : $query->whereNull('read_at');
                    }),

            ])
            ->recordActions([

                Actions\ViewAction::make(),

                Actions\Action::make('open')
                    ->label('Open')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (
                        DatabaseNotification $record
                    ) => $record->data['action_url'] ?? null)
                    ->openUrlInNewTab(),

                Actions\Action::make('markAsRead')
                    ->label('Mark as read')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (
                        DatabaseNotification $record
                    ) => $record->read_at === null)
                    ->action(fn (
                        DatabaseNotification $record
                    ) => $record->markAsRead()),

            ])
            ->bulkActions([

                Actions\BulkActionGroup::make([

                    Actions\BulkAction::make('markAsRead')
                        ->label('Mark selected as read')
                        ->icon('heroicon-o-check-circle')
                        ->action(fn ($records) =>
                            $records->each->markAsRead()
                        ),

                    Actions\DeleteBulkAction::make(),

                ]),

            ])
            ->recordClasses(fn (
                DatabaseNotification $record
            ) =>
                $record->read_at === null
                    ? 'bg-warning-50 dark:bg-warning-900/10'
                    : null
            )
            ->defaultSort(
                'created_at',
                'desc'
            );
    }
}