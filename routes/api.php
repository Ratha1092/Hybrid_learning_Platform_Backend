<?php

use Illuminate\Support\Facades\Route;
use App\Domains\Auth\Resources\UserResource;
use App\Support\ApiResponse;

/*
API Routes (v1)
Domain-based routing system with versioning
*/

// Broadcast auth for Sanctum token clients (mobile / SPA with Bearer token).
// The default /broadcasting/auth uses web (session) guard; this one uses sanctum.
Route::post('/broadcasting/auth', function (\Illuminate\Http\Request $request) {
    return \Illuminate\Support\Facades\Broadcast::auth($request);
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {

    $routes = glob(app_path('Domains/*/routes.php'));
    sort($routes);

    foreach ($routes as $route) {
        require $route;
    }
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', function () {
            $user = auth()->user();
            if (!$user) {
                return ApiResponse::error('Unauthenticated', 401);
            }
            return ApiResponse::success(
                new UserResource($user),
                'User retrieved successfully'
            );
        });

    });
});