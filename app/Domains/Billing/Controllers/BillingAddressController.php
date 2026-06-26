<?php

namespace App\Domains\Billing\Controllers;

use App\Domains\Billing\Models\BillingAddress;
use App\Domains\Billing\Services\BillingAddressService;
use App\Http\Controllers\Controller;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BillingAddressController extends Controller
{
    public function __construct(private readonly BillingAddressService $service) {}

    public function index(): JsonResponse
    {
        $addresses = BillingAddress::where('user_id', auth()->id())
            ->orderByDesc('is_default')
            ->get();

        return ApiResponse::success($addresses, 'Billing addresses retrieved successfully');
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'line1'      => ['required', 'string', 'max:255'],
            'line2'      => ['nullable', 'string', 'max:255'],
            'city'       => ['required', 'string', 'max:100'],
            'country'    => ['required', 'string', 'max:100'],
            'tax_id'     => ['nullable', 'string', 'max:100'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        $address = $this->service->store(auth()->user(), $data);

        return ApiResponse::success($address, 'Billing address created successfully', 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $address = BillingAddress::where('user_id', auth()->id())->findOrFail($id);

        $data = $request->validate([
            'name'       => ['sometimes', 'string', 'max:255'],
            'line1'      => ['sometimes', 'string', 'max:255'],
            'line2'      => ['nullable', 'string', 'max:255'],
            'city'       => ['sometimes', 'string', 'max:100'],
            'country'    => ['sometimes', 'string', 'max:100'],
            'tax_id'     => ['nullable', 'string', 'max:100'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        $address = $this->service->update($address, $data);

        return ApiResponse::success($address, 'Billing address updated successfully');
    }

    public function destroy(int $id): JsonResponse
    {
        $address = BillingAddress::where('user_id', auth()->id())->findOrFail($id);
        $this->service->destroy($address);

        return ApiResponse::success(null, 'Billing address deleted successfully');
    }

    public function setDefault(int $id): JsonResponse
    {
        $address = BillingAddress::where('user_id', auth()->id())->findOrFail($id);
        $this->service->setDefault($address);

        return ApiResponse::success($address->fresh(), 'Default billing address updated');
    }
}
