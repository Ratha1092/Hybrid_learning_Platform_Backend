<?php

namespace App\Domains\Auth\Controllers;

use Illuminate\Routing\Controller;
use App\Support\ApiResponse;
use App\Domains\Auth\Services\PasswordResetService;
use App\Domains\Auth\Services\ActivityLogService;
use App\Domains\Auth\Requests\ForgotPasswordRequest;
use App\Domains\Auth\Requests\VerifyResetTokenRequest;
use App\Domains\Auth\Requests\ResetPasswordRequest;

class PasswordResetController extends Controller
{
    public function __construct(
        private PasswordResetService $resetService
    ) {}

    /**
     * Request password reset
     */
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $result = $this->resetService->requestReset($request->validated());

        return ApiResponse::success($result, 'If email exists, reset link has been sent');
    }

    /**
     * Verify reset token
     */
    public function verifyToken(VerifyResetTokenRequest $request)
    {
        $this->resetService->verifyToken($request->validated('token'));

        return ApiResponse::success(null, 'Token is valid');
    }

    /**
     * Reset password
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        try {
            $data = $request->validated();

            $this->resetService->resetPassword($data['token'], $data['password']);

            ActivityLogService::log('password_changed');

            return ApiResponse::success(null, 'Password reset successfully. Please login.');
        } catch (\Exception $e) {
            return ApiResponse::error('Invalid or expired reset token', 400);
        }
    }
}
