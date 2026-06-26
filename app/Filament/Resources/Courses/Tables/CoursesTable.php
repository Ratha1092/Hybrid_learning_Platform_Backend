<?php

namespace App\Filament\Resources\Courses\Tables;

use App\Domains\Courses\Models\Course;
use App\Domains\Notifications\Notifications\CourseApprovedNotification;
use App\Domains\Notifications\Notifications\CourseRejectedNotification;
use Filament\Actions;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CoursesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                // ── ID ───────────────────────────────────────────────────────
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->formatStateUsing(fn ($state) => 'COUR-' . str_pad($state, 5, '0', STR_PAD_LEFT))
                    ->sortable()
                    ->width('130px'),

                // ── COURSE (thumbnail + title + short description) ────────────
                Tables\Columns\ImageColumn::make('thumbnail')
                    ->label('')
                    ->disk('public')
                    ->square()
                    ->size(42)
                    ->defaultImageUrl(fn ($record) =>
                        'https://ui-avatars.com/api/?name=' . urlencode(Str::limit($record->title, 2, '')) .
                        '&background=' . substr(md5($record->title ?? ''), 0, 6) . '&color=fff&bold=true&size=64'
                    ),

                Tables\Columns\TextColumn::make('title')
                    ->label('Course')
                    ->searchable()
                    ->sortable()
                    ->limit(40)
                    ->weight('semibold')
                    ->description(fn (Course $record) => Str::limit($record->short_description ?? '', 45)),

                // ── INSTRUCTOR (avatar circle + name) ─────────────────────────
                Tables\Columns\TextColumn::make('instructor.name')
                    ->label('Instructor')
                    ->html()
                    ->getStateUsing(function (Course $record): string {
                        $name = $record->instructor?->name ?? '—';
                        $safe = e($name);
                        $bg   = substr(md5($name), 0, 6);
                        $url  = 'https://ui-avatars.com/api/?name=' . urlencode($name)
                              . '&background=' . $bg . '&color=fff&bold=true&size=64';
                        return "<div style='display:flex;align-items:center;gap:8px;white-space:nowrap'>"
                             . "<img src='{$url}' style='width:28px;height:28px;border-radius:50%;flex-shrink:0' />"
                             . "<span>{$safe}</span></div>";
                    })
                    ->searchable(query: fn ($query, $search) =>
                        $query->whereHas('instructor', fn ($q) => $q->where('name', 'like', "%{$search}%"))
                    ),

                // ── CATEGORY ──────────────────────────────────────────────────
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->badge()
                    ->color(function (?string $state): string {
                        $colors = ['primary', 'success', 'warning', 'danger', 'info'];
                        return $colors[abs(crc32($state ?? '')) % count($colors)];
                    }),

                // ── PRICE ─────────────────────────────────────────────────────
                Tables\Columns\TextColumn::make('price')
                    ->label('Price')
                    ->money('USD')
                    ->sortable(),

                // ── STATUS ────────────────────────────────────────────────────
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        Course::STATUS_DRAFT     => 'Draft',
                        Course::STATUS_PENDING   => 'Pending Review',
                        Course::STATUS_PUBLISHED => 'Published',
                        Course::STATUS_REJECTED  => 'Rejected',
                        Course::STATUS_ARCHIVED  => 'Archived',
                        default                  => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        Course::STATUS_PUBLISHED => 'success',
                        Course::STATUS_PENDING   => 'warning',
                        Course::STATUS_REJECTED  => 'danger',
                        default                  => 'gray',
                    }),

                // ── STUDENTS ──────────────────────────────────────────────────
                Tables\Columns\TextColumn::make('enrollments_count')
                    ->label('Students')
                    ->counts('enrollments')
                    ->sortable()
                    ->icon('heroicon-m-user-group')
                    ->alignCenter(),

            ])

            // ── FILTERS ───────────────────────────────────────────────────────
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        Course::STATUS_DRAFT     => 'Draft',
                        Course::STATUS_PENDING   => 'Pending Review',
                        Course::STATUS_PUBLISHED => 'Published',
                        Course::STATUS_REJECTED  => 'Rejected',
                        Course::STATUS_ARCHIVED  => 'Archived',
                    ]),
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('level')
                    ->options([
                        'beginner'     => 'Beginner',
                        'intermediate' => 'Intermediate',
                        'advanced'     => 'Advanced',
                    ]),
            ])

            // ── ROW ACTIONS ───────────────────────────────────────────────────
            ->recordActions([
                Actions\ViewAction::make()->iconButton(),
                Actions\EditAction::make()->iconButton(),

                Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->iconButton()
                    ->hidden(fn (Course $record) => ! $record->isPendingReview())
                    ->requiresConfirmation()
                    ->modalHeading('Approve Course')
                    ->modalDescription('This will publish the course and notify the instructor.')
                    ->action(function (Course $record) {
                        $record->publish(auth()->id());
                        $record->instructor?->notify(new CourseApprovedNotification($record));
                        Notification::make()
                            ->title('Course Approved & Published')
                            ->body("\"{$record->title}\" is now live.")
                            ->success()->send();
                    }),

                Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->iconButton()
                    ->hidden(fn (Course $record) => ! $record->isPendingReview())
                    ->form([
                        Textarea::make('reason')
                            ->label('Rejection Reason')
                            ->placeholder('Explain why this course is being rejected…')
                            ->rows(3)
                            ->required(),
                    ])
                    ->modalHeading('Reject Course')
                    ->action(function (Course $record, array $data) {
                        $record->reject($data['reason']);
                        $record->instructor?->notify(new CourseRejectedNotification($record, $data['reason']));
                        Notification::make()->title('Course Rejected')->body('The instructor has been notified.')->danger()->send();
                    }),

                Actions\ActionGroup::make([
                    Actions\Action::make('submitReview')
                        ->label('Submit for Review')
                        ->icon('heroicon-o-paper-airplane')
                        ->color('warning')
                        ->visible(fn (Course $record) => $record->isDraft())
                        ->requiresConfirmation()
                        ->action(function (Course $record) {
                            if (!$record->canBePublished()) {
                                Notification::make()->title('Course is incomplete')->body('Course must contain sections and lessons before submission.')->danger()->send();
                                return;
                            }
                            $record->submitForReview();
                            Notification::make()->title('Course Submitted For Review')->success()->send();
                        }),

                    Actions\Action::make('archive')
                        ->label('Archive')
                        ->icon('heroicon-o-archive-box')
                        ->color('gray')
                        ->visible(fn (Course $record) => $record->isPublished())
                        ->requiresConfirmation()
                        ->action(function (Course $record) {
                            $record->archive();
                            Notification::make()->title('Course Archived')->warning()->send();
                        }),

                    Actions\Action::make('returnDraft')
                        ->label('Return to Draft')
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->color('gray')
                        ->visible(fn (Course $record) => !$record->isDraft())
                        ->requiresConfirmation()
                        ->action(function (Course $record) {
                            $record->update(['status' => Course::STATUS_DRAFT, 'is_published' => false]);
                            Notification::make()->title('Course Returned To Draft')->success()->send();
                        }),
                ]),
            ])

            // ── BULK ACTIONS ──────────────────────────────────────────────────
            ->toolbarActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                    Actions\ForceDeleteBulkAction::make(),
                    Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }
}
