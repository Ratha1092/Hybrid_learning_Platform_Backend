<?php

namespace App\Domains\Auth\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Support\ApiResponse;
use App\Domains\Auth\Services\EmailVerificationService;
use App\Domains\Auth\Services\ActivityLogService;
use App\Domains\Auth\Requests\VerifyEmailRequest;

class EmailVerificationController extends Controller
{
    public function __construct(
        private EmailVerificationService $emailService
    ) {}

    /**
     * Send verification email
     */
    public function send(Request $request)
    {
        $user = $request->user();

        if ($this->emailService->isVerified($user)) {
            return ApiResponse::error('Email already verified', 400);
        }

        $token = $this->emailService->generateToken($user);
        $verificationLink = $this->emailService->getVerificationLink($token['token']);

        $data = [
            'message' => 'Verification email sent',
        ];

        if (!app()->environment('production')) {
            $data['verification_link'] = $verificationLink;
            $data['token'] = $token['token'];
        }

        return ApiResponse::success($data, 'Verification email sent');
    }

    /**
     * Verify email with token
     */
    public function verify(VerifyEmailRequest $request)
    {
        try {
            $user = $this->emailService->verifyEmail($request->token);

            ActivityLogService::log('email_verified', $user);

            return ApiResponse::success(null, 'Email verified successfully');
        } catch (\Exception $e) {
            return ApiResponse::error('Invalid or expired verification token', 400);
        }
    }

    /**
     * Check verification status
     */
    public function status(Request $request)
    {
        $user = $request->user();

        return ApiResponse::success([
            'is_verified' => $this->emailService->isVerified($user),
            'verified_at' => $user->email_verified_at,
        ], 'Verification status');
    }
}
