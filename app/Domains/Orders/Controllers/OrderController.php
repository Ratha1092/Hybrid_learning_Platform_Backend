<?php

namespace App\Domains\Orders\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Orders\Services\OrderService;
use App\Domains\Orders\Models\Order;
use App\Support\ApiResponse;
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
        ]);

        $order = $this->orderService->createOrder($request->user(), $validated['course_id']);

        return ApiResponse::success($order->load('items', 'payment'), 'Order created successfully', 201);
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
}
