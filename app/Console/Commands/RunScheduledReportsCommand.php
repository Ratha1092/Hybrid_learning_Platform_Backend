<?php

namespace App\Console\Commands;

use App\Domains\Reports\Contracts\Schedulable;
use App\Domains\Reports\Mail\ScheduledReportMail;
use App\Domains\Reports\Models\ScheduledReport;
use App\Domains\System\Models\Setting;
use App\Support\CsvExporter;
use App\Support\ReportPdfExporter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class RunScheduledReportsCommand extends Command
{
    protected $signature   = 'reports:run-scheduled';
    protected $description = 'Generate and email any scheduled reports that are due';

    /**
     * report_type => fully-qualified Page class implementing Schedulable.
     * Filled in as each report page is built; a type without an entry here
     * is simply skipped with a log warning rather than crashing the run.
     */
    private const REPORT_CLASSES = [
        'executive' => \App\Filament\Pages\Dashboard::class,
        'payment' => \App\Filament\Pages\Reports\PaymentReport::class,
        'payout' => \App\Filament\Pages\Reports\PayoutReport::class,
        'revenue' => \App\Filament\Pages\Reports\RevenueReport::class,
        'course_intelligence' => \App\Filament\Pages\Reports\CourseIntelligenceReport::class,
        'instructor_intelligence' => \App\Filament\Pages\Reports\InstructorIntelligenceReport::class,
        'learning_intelligence' => \App\Filament\Pages\Reports\LearningIntelligenceReport::class,
    ];

    public function handle(): int
    {
        $due = ScheduledReport::due()->get();

        if ($due->isEmpty()) {
            $this->info('No scheduled reports due.');
            return self::SUCCESS;
        }

        $this->info("Processing {$due->count()} due report(s)...");

        foreach ($due as $scheduledReport) {
            try {
                $this->runOne($scheduledReport);
                $this->line("  ✓ {$scheduledReport->report_type} (#{$scheduledReport->id})");
            } catch (\Throwable $e) {
                Log::error('Scheduled report failed', [
                    'scheduled_report_id' => $scheduledReport->id,
                    'report_type' => $scheduledReport->report_type,
                    'error' => $e->getMessage(),
                ]);
                $this->error("  ✗ {$scheduledReport->report_type} (#{$scheduledReport->id}): {$e->getMessage()}");
            }
        }

        return self::SUCCESS;
    }

    private function runOne(ScheduledReport $scheduledReport): void
    {
        $reportClass = self::REPORT_CLASSES[$scheduledReport->report_type] ?? null;

        if (!$reportClass || !is_subclass_of($reportClass, Schedulable::class)) {
            Log::warning('Scheduled report skipped — no Schedulable class registered for type', [
                'report_type' => $scheduledReport->report_type,
            ]);
            return;
        }

        $filters = $scheduledReport->filters ?? [];
        $siteName = Setting::get('site_name', config('app.name'));
        $filtersSummary = $reportClass::filtersSummary($filters);

        [$content, $filename, $mimeType] = match ($scheduledReport->format) {
            'pdf' => [
                ReportPdfExporter::render($reportClass::pdfView(), $reportClass::pdfViewData($filters)),
                "{$reportClass::reportKey()}-report.pdf",
                'application/pdf',
            ],
            default => (function () use ($reportClass, $filters) {
                [$headerRow, $rows] = $reportClass::csvHeaderAndRows($filters);
                return [
                    CsvExporter::build($headerRow, $rows),
                    "{$reportClass::reportKey()}-report.csv",
                    'text/csv',
                ];
            })(),
        };

        foreach ($scheduledReport->recipient_emails as $email) {
            Mail::to($email)->send(new ScheduledReportMail(
                reportLabel: $reportClass::reportLabel(),
                periodSummary: $filtersSummary,
                attachmentContent: $content,
                attachmentFilename: $filename,
                mimeType: $mimeType,
            ));
        }

        $scheduledReport->update([
            'last_run_at' => now(),
            'next_run_at' => $scheduledReport->computeNextRunAt(),
        ]);
    }
}
