<?php

namespace App\Domains\Finance\Controllers;

use App\Domains\Finance\Models\InstructorPayoutAccount;
use App\Domains\Finance\Requests\SavePayoutAccountRequest;
use App\Domains\Finance\Services\PayoutAccountService;
use App\Http\Controllers\Controller;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PayoutAccountController extends Controller
{
    public function __construct(private readonly PayoutAccountService $service) {}

    public function show(Request $request): JsonResponse
    {
        $account = InstructorPayoutAccount::where('instructor_id', $request->user()->id)->first();

        return ApiResponse::success($account, 'Payout account retrieved successfully');
    }

    public function store(SavePayoutAccountRequest $request): JsonResponse
    {
        $account = $this->service->save($request->user(), $request->validated());

        return ApiResponse::success($account, 'Payout account submitted for verification', 201);
    }
}
