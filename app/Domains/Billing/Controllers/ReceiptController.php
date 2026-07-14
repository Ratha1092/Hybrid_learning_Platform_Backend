<?php

namespace App\Domains\Billing\Controllers;

use App\Domains\Billing\Models\Receipt;
use App\Domains\Billing\Services\ReceiptService;
use App\Http\Controllers\Controller;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReceiptController extends Controller
{
    public function __construct(private readonly ReceiptService $receiptService) {}

    public function index(): JsonResponse
    {
        $receipts = Receipt::whereHas('order', fn ($q) => $q->where('user_id', auth()->id()))
            ->with('order:id,order_number,currency')
            ->latest()
            ->paginate(15);

        return ApiResponse::success($receipts, 'Receipts retrieved successfully');
    }

    public function show(int $id): JsonResponse
    {
        $receipt = Receipt::with('order:id,order_number,currency,user_id')
            ->whereHas('order', fn ($q) => $q->where('user_id', auth()->id()))
            ->findOrFail($id);

        return ApiResponse::success($receipt, 'Receipt retrieved successfully');
    }

    public function download(int $id): StreamedResponse|JsonResponse
    {
        $receipt = Receipt::whereHas('order', fn ($q) => $q->where('user_id', auth()->id()))
            ->findOrFail($id);

        if (!$receipt->pdf_path || !Storage::disk('r2-private')->exists($receipt->pdf_path)) {
            $receipt->load('order.items');
            $receipt = $this->receiptService->issue($receipt->order);
        }

        return Storage::disk('r2-private')->download(
            $receipt->pdf_path,
            $receipt->receipt_number . '.pdf',
            ['Content-Type' => 'application/pdf']
        );
    }
}
