<?php

namespace App\Domains\Auth\Controllers;

use Illuminate\Routing\Controller;
use App\Support\ApiResponse;
use App\Domains\Auth\Services\TwoFactorAuthService;
use App\Domains\Auth\Services\ActivityLogService;
use App\Domains\Auth\Requests\VerifyTwoFactorRequest;
use App\Domains\Auth\Requests\SendTwoFactorCodeRequest;
use App\Domains\Auth\Requests\DisableTwoFactorRequest;

class TwoFactorAuthController extends Controller
{
    public function __construct(
        private TwoFactorAuthService $twoFactorService
    ) {}

    public function enable()
    {
        $data = $this->twoFactorService->enable(request()->user());

        return ApiResponse::success($data, '2FA code sent');
    }

    public function verifyAndEnable(VerifyTwoFactorRequest $request)
    {
        $this->twoFactorService->verifyAndEnable(
            $request->user(),
            $request->validated()
        );

        return ApiResponse::success(null, '2FA enabled successfully');
    }

    public function disable(DisableTwoFactorRequest $request)
    {
        $this->twoFactorService->disable(
            $request->user(),
            $request->validated()
        );

        return ApiResponse::success(null, '2FA disabled successfully');
    }

    public function status()
    {
        $status = $this->twoFactorService->status(request()->user());

        return ApiResponse::success($status, '2FA status');
    }

    public function sendCode(SendTwoFactorCodeRequest $request)
    {
        $data = $this->twoFactorService->sendCode($request->validated());

        return ApiResponse::success($data, '2FA code sent');
    }

    public function verifyCode(VerifyTwoFactorRequest $request)
    {
        $data = $this->twoFactorService->verifyLogin($request->validated());

        return ApiResponse::success($data, 'Login successful');
    }
}