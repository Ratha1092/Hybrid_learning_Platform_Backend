<?php

namespace App\Domains\Auth\Controllers;

use Illuminate\Routing\Controller;
use App\Support\ApiResponse;
use App\Domains\Auth\Services\TwoFactorAuthService;
use App\Domains\Auth\Services\ActivityLogService;
use App\Domains\Auth\Requests\VerifyTwoFactorRequest;
use App\Domains\Auth\Requests\VerifyLoginTwoFactorRequest;
use App\Domains\Auth\Requests\SendTwoFactorCodeRequest;
use App\Domains\Auth\Requests\DisableTwoFactorRequest;

class TwoFactorAuthController extends Controller
{
    public function __construct(
        private TwoFactorAuthService $twoFactorService
    ) {}

    public function enable()
    {
        try {
            $data = $this->twoFactorService->enable(request()->user());

            return ApiResponse::success($data, '2FA code sent');
        } catch (\RuntimeException $e) {
            return ApiResponse::error($e->getMessage(), 400);
        }
    }

    public function verifyAndEnable(VerifyTwoFactorRequest $request)
    {
        try {
            $this->twoFactorService->verifyAndEnable(
                $request->user(),
                $request->validated()
            );

            return ApiResponse::success(null, '2FA enabled successfully');
        } catch (\RuntimeException $e) {
            return ApiResponse::error($e->getMessage(), 400);
        }
    }

    public function disable(DisableTwoFactorRequest $request)
    {
        try {
            $this->twoFactorService->disable(
                $request->user(),
                $request->validated()
            );

            return ApiResponse::success(null, '2FA disabled successfully');
        } catch (\RuntimeException $e) {
            return ApiResponse::error($e->getMessage(), 400);
        }
    }

    public function status()
    {
        $status = $this->twoFactorService->status(request()->user());

        return ApiResponse::success($status, '2FA status');
    }

    public function sendCode(SendTwoFactorCodeRequest $request)
    {
        try {
            $data = $this->twoFactorService->sendCode($request->validated());

            return ApiResponse::success($data, '2FA code sent');
        } catch (\RuntimeException $e) {
            return ApiResponse::error($e->getMessage(), 400);
        }
    }

    public function verifyCode(VerifyLoginTwoFactorRequest $request)
    {
        try {
            $data = $this->twoFactorService->verifyLogin($request->validated());

            return ApiResponse::success($data, 'Login successful');
        } catch (\RuntimeException $e) {
            return ApiResponse::error($e->getMessage(), 400);
        }
    }
}
