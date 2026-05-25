<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsInstructor
{
    public function handle(Request $request, Closure $next)
    {
        if (! $request->user()) {
            if ($request->is('api/*')) {
                return response()->json(['message' => 'Unauthenticated'], 401);
            }

            return redirect('/login')->with('error', 'Please login first');
        }

        if (! $request->user()->isVerifiedInstructor()) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Verified instructor access only',
                ], 403);
            }

            return redirect('/dashboard')->with(
                'error',
                'Unauthorized access. Only verified instructors can access this resource'
            );
        }

        return $next($request);
    }
}
