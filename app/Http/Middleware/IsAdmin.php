<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        if (auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'Admin access only'], 403);
        }

        return $next($request);
    }
}