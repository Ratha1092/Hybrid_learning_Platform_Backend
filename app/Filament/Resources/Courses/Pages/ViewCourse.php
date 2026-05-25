<?php

namespace App\Filament\Resources\Courses\Pages;

use App\Domains\Courses\Models\Course;
use App\Domains\Notifications\Notifications\CourseApprovedNotification;
use App\Domains\Notifications\Notifications\CourseRejectedNotification;
use App\Filament\Resources\Courses\CourseResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewCourse extends ViewRecord
{
    protected static string $resource = CourseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('approve')
                ->label('Approve & Publish')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn () => $this->record->isPendingReview())
                ->requiresConfirmation()
                ->modalHeading('Approve Course')
                ->modalDescription('This will publish the course immediately and notify the instructor.')
                ->modalSubmitActionLabel('Yes, Approve')
                ->action(function () {
                    /** @var Course $course */
                    $course = $this->record;
                    $course->publish(auth()->id());

                    $course->instructor?->notify(new CourseApprovedNotification($course));

                    Notification::make()
                        ->title('Course Approved & Published')
                        ->body("\"{$course->title}\" is now live.")
                        ->success()
                        ->send();

                    $this->refreshFormData(['status', 'is_published', 'approved_at', 'approved_by']);
                }),

            Action::make('reject')
                ->label('Reject')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn () => $this->record->isPendingReview())
                ->form([
                    Textarea::make('reason')
                        ->label('Rejection Reason')
                        ->placeholder('Explain what needs to be fixed so the instructor can improve the course…')
                        ->rows(4)
                        ->required(),
                ])
                ->modalHeading('Reject Course')
                ->modalSubmitActionLabel('Reject Course')
                ->action(function (array $data) {
                    /** @var Course $course */
                    $course = $this->record;
                    $course->reject($data['reason']);

                    $course->instructor?->notify(
                        new CourseRejectedNotification($course, $data['reason'])
                    );

                    Notification::make()
                        ->title('Course Rejected')
                        ->body('The instructor has been notified with your feedback.')
                        ->danger()
                        ->send();

                    $this->refreshFormData(['status', 'is_published', 'rejection_reason']);
                }),

            EditAction::make(),
        ];
    }
}
