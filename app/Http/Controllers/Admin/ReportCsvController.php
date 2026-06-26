<?php

namespace App\Http\Controllers\Admin;

use App\Domains\Reports\Contracts\Schedulable;
use App\Http\Controllers\Controller;
use App\Support\CsvExporter;
use App\Support\PanelAccess;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ReportCsvController extends Controller
{
    private const REPORTS = [
        'executive'               => [\App\Filament\Pages\Dashboard::class,'reports.view_executive'],
        'payment'                 => [\App\Filament\Pages\Reports\PaymentReport::class,'reports.view_payment'],
        'payout'                  => [\App\Filament\Pages\Reports\PayoutReport::class,'reports.view_payout'],
        'revenue'                 => [\App\Filament\Pages\Reports\RevenueReport::class,'reports.view_revenue'],
        'course_intelligence'     => [\App\Filament\Pages\Reports\CourseIntelligenceReport::class,'reports.view_course_intelligence'],
        'instructor_intelligence' => [\App\Filament\Pages\Reports\InstructorIntelligenceReport::class,'reports.view_instructor_intelligence'],
        'learning_intelligence'   => [\App\Filament\Pages\Reports\LearningIntelligenceReport::class,'reports.view_learning_intelligence'],
        'user'                    => [\App\Filament\Pages\Reports\UserReport::class,'reports.view_user'],
    ];

    public function show(Request $request, string $type)
    {
        [$reportClass, $permission] = self::REPORTS[$type] ?? [null, null];

        if (! $reportClass || ! is_subclass_of($reportClass, Schedulable::class)) {
            throw new NotFoundHttpException("Unknown report type: {$type}");
        }

        abort_unless(PanelAccess::can($permission), 403);

        $filters = $request->query();
        unset($filters['type']);

        [$header, $rows] = $reportClass::csvHeaderAndRows($filters);

        $content  = CsvExporter::build($header, $rows);
        $filename = $reportClass::reportKey() . '-report-' . now()->format('Y-m-d') . '.csv';

        return response($content, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
