<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifiedInstructor
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (!$user) {
            if ($request->is('api/*')) {
                return response()->json(['message' => 'Unauthenticated'], 401);
            }

            return redirect()->route('login');
        }

        if (!$user->isVerifiedInstructor()) {
            if ($request->is('api/*')) {
                return response()->json(['message' => 'Verified instructor access only'], 403);
            }

            return redirect()->route('instructor.dashboard')
                ->with('error', 'You need to be verified as an instructor to access this feature.');
        }

        return $next($request);
    }
}
