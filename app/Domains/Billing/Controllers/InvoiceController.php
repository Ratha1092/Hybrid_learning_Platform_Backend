<?php

namespace App\Domains\Billing\Controllers;

use App\Domains\Billing\Models\Invoice;
use App\Domains\Billing\Services\InvoiceService;
use App\Http\Controllers\Controller;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InvoiceController extends Controller
{
    public function __construct(private readonly InvoiceService $invoiceService) {}

    public function index(): JsonResponse
    {
        $invoices = Invoice::whereHas('order', fn ($q) => $q->where('user_id', auth()->id()))
            ->with('order:id,order_number,currency')
            ->latest()
            ->paginate(15);

        return ApiResponse::success($invoices, 'Invoices retrieved successfully');
    }

    public function show(int $id): JsonResponse
    {
        $invoice = Invoice::with('items', 'order:id,order_number,currency', 'billingAddress')
            ->whereHas('order', fn ($q) => $q->where('user_id', auth()->id()))
            ->findOrFail($id);

        return ApiResponse::success($invoice, 'Invoice retrieved successfully');
    }

    public function download(int $id): StreamedResponse|JsonResponse
    {
        $invoice = Invoice::whereHas('order', fn ($q) => $q->where('user_id', auth()->id()))
            ->findOrFail($id);

        if (!$invoice->pdf_path || !Storage::disk('local')->exists($invoice->pdf_path)) {
            $this->invoiceService->regeneratePdf($invoice);
            $invoice->refresh();
        }

        return Storage::disk('local')->download(
            $invoice->pdf_path,
            $invoice->invoice_number . '.pdf',
            ['Content-Type' => 'application/pdf']
        );
    }
}
