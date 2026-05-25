<?php

namespace App\Domains\Users\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Users\Requests\ApplyInstructorRequest;
use App\Domains\Users\Services\InstructorVerificationService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InstructorVerificationController extends Controller
{
    public function __construct(
        protected InstructorVerificationService $service
    ) {}

    public function store(
        ApplyInstructorRequest $request
    ): JsonResponse {

        try {

            $verification = $this->service->apply(
                $request->user(),
                $request->validated()
            );

            return response()->json([
                'success' => true,
                'message' => 'Instructor application submitted successfully.',
                'data' => $verification,
            ], 201);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function status(Request $request): JsonResponse
    {
        $user = $request->user();
        $verification = $this->service->latestForUser($user);

        return ApiResponse::success([
            'instructor_status' => $user->instructor_status,
            'application' => $verification,
        ], 'Instructor application status retrieved successfully');
    }
}
