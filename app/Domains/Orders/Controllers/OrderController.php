<?php

namespace App\Domains\Orders\Controllers;

use App\Domains\System\Models\Setting;
use App\Http\Controllers\Controller;
use App\Domains\Orders\Services\OrderService;
use App\Domains\Orders\Models\Order;
use App\Support\ApiResponse;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => ['required', 'integer', 'exists:courses,id'],
            'coupon_code' => ['nullable', 'string', 'max:64'],
        ]);

        $order = $this->orderService->createOrder(
            $request->user(),
            $validated['course_id'],
            $validated['coupon_code'] ?? null,
        );

        return ApiResponse::success($order->load('items', 'payment'), 'Order created successfully', 201);
    }

    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 15);
        $perPage = in_array($perPage, [10, 15, 25, 50], true) ? $perPage : 15;

        $orders = Order::with('items', 'payment')
            ->where('user_id', $request->user()->id)
            ->orderByDesc('id')
            ->paginate($perPage);

        return ApiResponse::success($orders, 'Orders retrieved successfully');
    }

    public function show(Request $request, $id)
    {
        $order = Order::with('items', 'payment')
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$order) {
            return ApiResponse::error('Order not found', 404);
        }

        return ApiResponse::success($order, 'Order retrieved successfully');
    }

    public function receipt(Request $request, $id)
    {
        $order = Order::with('items.instructor', 'payment')
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$order) {
            return ApiResponse::error('Order not found', 404);
        }

        if (!$order->isPaid()) {
            return ApiResponse::error('Receipt is only available for paid orders', 422);
        }

        $pdf = Pdf::loadView('receipts.order', [
            'order' => $order,
            'payment' => $order->payment,
            'siteName' => Setting::get('site_name', config('app.name')),
        ])->setPaper('a4');

        return $pdf->download("receipt-{$order->order_number}.pdf");
    }
}
