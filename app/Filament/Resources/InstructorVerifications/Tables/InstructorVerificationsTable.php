<?php

namespace App\Filament\Resources\InstructorVerifications\Tables;

use App\Domains\Users\Models\InstructorVerification;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;

class InstructorVerificationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Instructor')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable(),

                Tables\Columns\TextColumn::make('qualification_type')
                    ->label('Qualification')
                    ->badge()
                    ->formatStateUsing(
                        fn ($state) => ucfirst(str_replace('_', ' ', $state))
                    )
                    ->colors([
                        'info' => 'degree',
                        'success' => 'certification',
                        'warning' => 'professional_experience',
                    ]),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Applied')
                    ->dateTime('M d, Y')
                    ->sortable(),
            ])

            ->filters([

                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),

                Tables\Filters\TrashedFilter::make(),
            ])

            ->recordActions([

                Actions\ViewAction::make(),

                Actions\EditAction::make(),

                Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')

                    ->visible(
                        fn (InstructorVerification $record) =>
                        $record->status === 'pending'
                    )

                    ->action(function (InstructorVerification $record) {

                        $record->update([
                            'status' => 'approved',
                            'reviewed_by' => auth()->id(),
                            'reviewed_at' => now(),
                        ]);

                        $user = $record->user;

                        $user->update([
                            'instructor_status' => 'verified',
                        ]);

                        if (!$user->hasRole('instructor')) {
                            $user->assignRole('instructor');
                        }

                        Notification::make()
                            ->title('Instructor Approved')
                            ->body(
                                "{$user->name} has been approved as instructor."
                            )
                            ->success()
                            ->send();
                    }),

                Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')

                    ->visible(
                        fn (InstructorVerification $record) =>
                        $record->status === 'pending'
                    )

                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->required()
                            ->placeholder(
                                'Explain why this application is rejected...'
                            ),
                    ])

                    ->action(function (
                        InstructorVerification $record,
                        array $data
                    ) {

                        $record->update([
                            'status' => 'rejected',
                            'rejection_reason' => $data['rejection_reason'],
                            'reviewed_by' => auth()->id(),
                            'reviewed_at' => now(),
                        ]);

                        $user = $record->user;

                        $user->update([
                            'instructor_status' => 'rejected',
                        ]);

                        $user->removeRole('instructor');

                        Notification::make()
                            ->title('Application Rejected')
                            ->danger()
                            ->send();
                    }),
            ])

            ->toolbarActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                    Actions\ForceDeleteBulkAction::make(),
                    Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }
}