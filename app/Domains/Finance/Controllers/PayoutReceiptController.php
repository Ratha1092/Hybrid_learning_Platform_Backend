<?php

namespace App\Domains\Finance\Controllers;

use App\Domains\Finance\Models\PayoutReceipt;
use App\Domains\Finance\Services\PayoutReceiptService;
use App\Http\Controllers\Controller;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PayoutReceiptController extends Controller
{
    public function __construct(private readonly PayoutReceiptService $receiptService) {}

    public function index(): JsonResponse
    {
        $receipts = PayoutReceipt::whereHas('payoutRequest', fn ($q) => $q->where('instructor_id', auth()->id()))
            ->with('payoutRequest:id,amount,currency,status')
            ->latest()
            ->paginate(15);

        return ApiResponse::success($receipts, 'Payout receipts retrieved successfully');
    }

    public function show(int $id): JsonResponse
    {
        $receipt = PayoutReceipt::with('payoutRequest:id,amount,currency,status,payment_method')
            ->whereHas('payoutRequest', fn ($q) => $q->where('instructor_id', auth()->id()))
            ->findOrFail($id);

        return ApiResponse::success($receipt, 'Payout receipt retrieved successfully');
    }

    public function download(int $id): StreamedResponse|JsonResponse
    {
        $receipt = PayoutReceipt::whereHas('payoutRequest', fn ($q) => $q->where('instructor_id', auth()->id()))
            ->findOrFail($id);

        if (!$receipt->pdf_path || !Storage::disk('local')->exists($receipt->pdf_path)) {
            $this->receiptService->regeneratePdf($receipt);
            $receipt->refresh();
        }

        return Storage::disk('local')->download(
            $receipt->pdf_path,
            $receipt->receipt_number . '.pdf',
            ['Content-Type' => 'application/pdf']
        );
    }
}
