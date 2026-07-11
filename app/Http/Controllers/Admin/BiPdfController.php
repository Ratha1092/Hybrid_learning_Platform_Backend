<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\PanelAccess;
use App\Support\ReportPdfExporter;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BiPdfController extends Controller
{
    private const PAGES = [
        'stu'    => [\App\Filament\Pages\BI\StudentIntelligence::class, 'bi.view_students'],
        'course' => [\App\Filament\Pages\BI\CourseIntelligence::class, 'bi.view_courses'],
        'exec'   => [\App\Filament\Pages\BI\ExecutiveCenter::class, 'bi.view_executive'],
        'fin'    => [\App\Filament\Pages\BI\FinancialIntelligence::class, 'bi.view_financial'],
        'instr'  => [\App\Filament\Pages\BI\InstructorIntelligence::class, 'bi.view_instructors'],
        'mkt'    => [\App\Filament\Pages\BI\MarketplaceIntelligence::class, 'bi.view_marketplace'],
        'rev'    => [\App\Filament\Pages\BI\RevenueIntelligence::class, 'bi.view_revenue'],
        'ops'    => [\App\Filament\Pages\BI\OperationalIntelligence::class, 'bi.view_operations'],
    ];

    public function show(Request $request, string $type)
    {
        [$pageClass, $permission] = self::PAGES[$type] ?? [null, null];

        if (!$pageClass || !method_exists($pageClass, 'pdfView')) {
            throw new NotFoundHttpException("Unknown BI report type: {$type}");
        }

        abort_unless(PanelAccess::can($permission), 403);

        $filters = $request->query();
        unset($filters['type']);

        return ReportPdfExporter::download(
            $pageClass::pdfView(),
            $pageClass::pdfViewData($filters),
            $type . '-intelligence-' . now()->format('Y-m-d') . '.pdf',
        );
    }
}
