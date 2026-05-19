<?php

namespace App\Filament\Resources\Courses\Tables;

use App\Domains\Courses\Models\Course;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;

class CoursesTable
{
    public static function configure(Table $table): Table
    {
        return $table

            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail')
                    ->label('Thumbnail')
                    ->square()
                    ->defaultImageUrl('https://placehold.co/100x100/png'),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(40)
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('instructor.name')
                    ->label('Instructor')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->badge(),
                Tables\Columns\TextColumn::make('price')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        Course::STATUS_DRAFT => 'gray',
                        Course::STATUS_PENDING => 'warning',
                        Course::STATUS_PUBLISHED => 'success',
                        Course::STATUS_REJECTED => 'danger',
                        Course::STATUS_ARCHIVED => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\IconColumn::make('is_published')
                    ->boolean()
                    ->label('Published'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        Course::STATUS_DRAFT => 'Draft',
                        Course::STATUS_PENDING => 'Pending Review',
                        Course::STATUS_PUBLISHED => 'Published',
                        Course::STATUS_REJECTED => 'Rejected',
                        Course::STATUS_ARCHIVED => 'Archived',
                    ]),
            ])
            ->recordActions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
                Actions\Action::make('submitReview')
                    ->label('Submit Review')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('warning')
                    ->visible(
                        fn (Course $record) =>
                        $record->isDraft()
                    )
                    ->requiresConfirmation()
                    ->action(function (Course $record) {
                        if (!$record->canBePublished()) {

                            Notification::make()
                                ->title('Course is incomplete')
                                ->body(
                                    'Course must contain sections and lessons before submission.'
                                )
                                ->danger()
                                ->send();
                            return;
                        }
                        $record->submitForReview();
                        Notification::make()
                            ->title('Course Submitted For Review')
                            ->success()
                            ->send();
                    }),
                Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(
                        fn (Course $record) =>
                        $record->isPendingReview()
                    )
                    ->requiresConfirmation()
                    ->action(function (Course $record) {
                        $record->publish(auth()->id());
                        Notification::make()
                            ->title('Course Approved')
                            ->success()
                            ->send();
                    }),
                Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(
                        fn (Course $record) =>
                        $record->isPendingReview()
                    )

                    ->requiresConfirmation()
                    ->action(function (Course $record) {
                        $record->reject();

                        Notification::make()
                            ->title('Course Rejected')
                            ->danger()
                            ->send();
                    }),
                Actions\Action::make('archive')
                    ->label('Archive')
                    ->icon('heroicon-o-archive-box')
                    ->color('gray')
                    ->visible(
                        fn (Course $record) =>
                        $record->isPublished()
                    )
                    ->requiresConfirmation()
                    ->action(function (Course $record) {
                        $record->archive();
                        Notification::make()
                            ->title('Course Archived')
                            ->warning()
                            ->send();
                    }),
                Actions\Action::make('returnDraft')
                    ->label('Return Draft')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('gray')
                    ->visible(
                        fn (Course $record) =>
                        !$record->isDraft()
                    )
                    ->requiresConfirmation()
                    ->action(function (Course $record) {
                        $record->update([
                            'status' => Course::STATUS_DRAFT,
                            'is_published' => false,
                        ]);
                        Notification::make()
                            ->title('Course Returned To Draft')
                            ->success()
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