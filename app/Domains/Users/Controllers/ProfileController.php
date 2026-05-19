<?php

namespace App\Domains\Users\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Users\Services\UserService;
use App\Domains\Users\Requests\UpdateProfileRequest;
use App\Domains\Users\Resources\UserResource;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {}

    public function me(Request $request)
    {
        $user = $this->userService->getProfile($request->user());

        return ApiResponse::success(
            new UserResource($user),
            'Profile retrieved successfully'
        );
    }

    public function update(UpdateProfileRequest $request)
    {
        $user = $this->userService->updateProfile(
            $request->user(),
            $request->validated()
        );

        return ApiResponse::success(
            new UserResource($user),
            'Profile updated successfully'
        );
    }
}