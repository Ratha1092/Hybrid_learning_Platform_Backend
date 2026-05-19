<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsInstructor
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {

        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        if (!($user->role === 'instructor' || $user->hasRole('instructor'))) {
            return response()->json([
                'success' => false,
                'message' => 'Instructor access required.',
            ], 403);
        }

        if ($user->instructor_status !== 'verified') {
            return response()->json([
                'success' => false,
                'message' => 'Instructor verification required.',
            ], 403);
        }

        return $next($request);
    }
}