<?php

namespace App\Support;

use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\Response;

/**
 * Wraps DomPDF for report PDFs, mirroring the pattern in
 * App\Domains\Orders\Controllers\OrderController::receipt().
 */
class ReportPdfExporter
{
    public static function download(string $view, array $data, string $filename): Response
    {
        return Pdf::loadView($view, $data)->setPaper('a4')->download($filename);
    }

    /**
     * Render to a raw binary string instead of streaming an HTTP response —
     * used by the scheduled-report command to attach the file to an email.
     */
    public static function render(string $view, array $data): string
    {
        return Pdf::loadView($view, $data)->setPaper('a4')->output();
    }
}
