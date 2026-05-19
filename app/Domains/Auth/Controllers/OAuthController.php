<?php

namespace App\Domains\Auth\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Support\ApiResponse;
use App\Domains\Auth\Services\OAuthService;
use App\Domains\Auth\Requests\GoogleOAuthRequest;
use App\Domains\Auth\Requests\LinkOAuthRequest;

class OAuthController extends Controller
{
    public function __construct(
        private OAuthService $oauthService
    ) {}

    public function handleGoogleCallback(GoogleOAuthRequest $request)
    {
        $data = $this->oauthService->handleGoogle($request->validated());

        return ApiResponse::success($data, 'Login successful');
    }

    public function handleGithubCallback(Request $request)
    {
        return ApiResponse::error('GitHub OAuth not fully implemented', 501);
    }

    public function linkOAuthAccount(LinkOAuthRequest $request)
    {
        $this->oauthService->link($request->user(), $request->validated());

        return ApiResponse::success(null, 'OAuth account linked successfully');
    }

    public function unlinkOAuthAccount(Request $request, string $provider)
    {
        $this->oauthService->unlink($request->user(), $provider);

        return ApiResponse::success(null, 'OAuth account unlinked');
    }

    public function linkedAccounts(Request $request)
    {
        $accounts = $this->oauthService->getLinkedAccounts($request->user());

        return ApiResponse::success($accounts, 'Linked OAuth accounts');
    }
}