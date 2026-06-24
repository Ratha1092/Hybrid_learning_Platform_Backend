<?php

namespace App\Domains\Reports\Contracts;

/**
 * Implemented by every report Page that supports scheduled (emailed) delivery.
 * These same methods back the interactive page's own getViewData()/export
 * actions, so scheduled output never drifts from what a user sees on-demand.
 */
interface Schedulable
{
    /**
     * The App\Domains\Reports\Models\ScheduledReport.report_type key this
     * report answers to, e.g. 'revenue', 'payment', 'course_intelligence'.
     */
    public static function reportKey(): string;

    /** Human label for email subjects, e.g. "Revenue". */
    public static function reportLabel(): string;

    /**
     * [headerRow, dataRows] for CSV export. $filters carries the same shape
     * read from request() interactively, or from ScheduledReport.filters when
     * run headlessly by the scheduler.
     */
    public static function csvHeaderAndRows(array $filters): array;

    /**
     * Data array to pass into this report's PDF Blade view (must include
     * 'siteName', 'title', and 'filtersSummary' for the shared
     * resources/views/reports/_pdf-layout.blade.php partial).
     */
    public static function pdfViewData(array $filters): array;

    /** The Blade view name used for this report's PDF export. */
    public static function pdfView(): string;

    /** Short, human-readable summary of the filters in effect, e.g. "This Month". */
    public static function filtersSummary(array $filters): string;
}
