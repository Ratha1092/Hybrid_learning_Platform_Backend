<?php

namespace App\Filament\Resources\ActivityLogs\Pages;

use App\Domains\Auth\Models\ActivityLog;
use App\Domains\System\Models\Setting;
use App\Filament\Resources\ActivityLogs\ActivityLogResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewActivityLog extends ViewRecord
{
    protected static string $resource = ActivityLogResource::class;
    protected string $view = 'filament.resources.activity-logs.view-audit-log';

    public function getBreadcrumbs(): array
    {
        return [];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('resetToOldValues')
                ->label('Reset Changes')
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Reset to Previous State')
                ->modalDescription('This will restore the record to its state before this event was logged. This cannot be undone.')
                ->modalSubmitActionLabel('Yes, reset it')
                ->visible(fn () => ! empty($this->record->old_values)
                    && $this->record->subject_type
                    && $this->record->subject_id)
                ->action(fn () => $this->resetRecord()),
        ];
    }

    public function resetRecord(): void
    {
        $log = $this->record;

        if (empty($log->old_values) || ! $log->subject_type || ! $log->subject_id) {
            Notification::make()->title('Nothing to reset')->body('This log entry has no previous values.')->warning()->send();
            return;
        }

        if (! class_exists($log->subject_type)) {
            Notification::make()->title('Cannot reset')->body('The subject model class no longer exists.')->danger()->send();
            return;
        }

        // Settings are stored as key→value pairs across multiple rows
        if ($log->subject_type === Setting::class || is_a($log->subject_type, Setting::class, true)) {
            foreach ($log->old_values as $key => $value) {
                Setting::where('key', $key)->update(['value' => $value]);
            }
        } else {
            $record = ($log->subject_type)::find($log->subject_id);

            if (! $record) {
                Notification::make()->title('Cannot reset')->body('The original record no longer exists in the database.')->danger()->send();
                return;
            }

            $record->fill($log->old_values)->save();
        }

        // Log the reset itself
        ActivityLog::create([
            'user_id'      => auth()->id(),
            'action'       => 'record.reset',
            'subject_type' => $log->subject_type,
            'subject_id'   => $log->subject_id,
            'old_values'   => $log->new_values,
            'new_values'   => $log->old_values,
            'data'         => ['reset_from_log_id' => $log->id, 'reset_by' => auth()->user()?->name],
            'ip_address'   => request()->ip(),
            'user_agent'   => request()->userAgent(),
        ]);

        Notification::make()
            ->title('Record Reset')
            ->body('The record has been restored to its previous state.')
            ->success()
            ->send();
    }

    protected function getViewData(): array
    {
        $log = $this->record->load('user');

        return [
            'backUrl'   => route('filament.admin.pages.audit-log'),
            'log'       => $log,
            'canReset'  => ! empty($log->old_values) && $log->subject_type && $log->subject_id,
        ];
    }
}
