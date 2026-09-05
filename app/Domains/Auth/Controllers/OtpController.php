<?php

namespace App\Domains\Auth\Controllers;

use App\Domains\Auth\Services\OtpService;
use App\Domains\Users\Models\User;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class OtpController extends Controller
{
    public function __construct(private OtpService $otp) {}

    public function send(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        $email = strtolower(trim($request->input('email')));

        if (User::where('email', $email)->exists()) {
            return ApiResponse::error('This email is already registered', 422);
        }

        $plain = $this->otp->send($email);

        $data = [];

        if (in_array(app()->environment(), ['local', 'testing'], true)) {
            $data['code'] = $plain;
        }

        return ApiResponse::success($data, 'Verification code sent');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'code'  => ['required', 'string', 'size:6'],
        ]);

        $ok = $this->otp->verify(
            $request->input('email'),
            $request->input('code')
        );

        if (! $ok) {
            return ApiResponse::error('Invalid or expired code', 400);
        }

        return ApiResponse::success(['verified' => true], 'Email verified');
    }
}
