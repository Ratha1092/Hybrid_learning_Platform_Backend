<?php

namespace App\Domains\Reports\Concerns;

use App\Domains\Reports\Models\ScheduledReport;
use App\Support\PanelAccess;
use Filament\Notifications\Notification;

/**
 * Shared "schedule this report" form state + action for every report Page
 * implementing App\Domains\Reports\Contracts\Schedulable. Pairs with
 * resources/views/filament/pages/partials/_schedule-modal.blade.php.
 *
 * The using class must provide a `currentFilters(): array` method (every
 * report page already builds one for its own getViewData()/exportCsv()).
 */
trait HasScheduleAction
{
    public string $scheduleFrequency = 'monthly';
    public string $scheduleFormat = 'csv';
    public string $scheduleEmails = '';

    public function scheduleReport(): void
    {
        if (!PanelAccess::can('reports.schedule')) {
            return;
        }

        $recipientEmails = array_values(array_filter(array_map('trim', explode(',', $this->scheduleEmails))));

        if (empty($recipientEmails)) {
            Notification::make()->title('At least one recipient email is required.')->danger()->send();
            return;
        }

        ScheduledReport::create([
            'report_type' => static::reportKey(),
            'frequency' => $this->scheduleFrequency,
            'format' => $this->scheduleFormat,
            'recipient_emails' => $recipientEmails,
            'filters' => $this->currentFilters(),
            'created_by' => auth()->id(),
        ]);

        Notification::make()
            ->title('Report scheduled')
            ->body(static::reportLabel() . " report will be emailed {$this->scheduleFrequency}.")
            ->success()
            ->send();

        $this->scheduleEmails = '';
    }
}
