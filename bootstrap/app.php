<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use App\Support\ApiResponse;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )

    ->withMiddleware(function (Middleware $middleware): void {

        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
            'is_admin' => \App\Http\Middleware\IsAdmin::class,
            'is_instructor' => \App\Http\Middleware\IsInstructor::class,
            'verified_instructor' => \App\Http\Middleware\VerifiedInstructor::class,
            'instructor' => \App\Http\Middleware\EnsureUserIsInstructor::class,
        ]);

    })
    ->withProviders([
        App\Providers\EventServiceProvider::class,
    ])

    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (AuthenticationException $e, $request) {
            if ($request->is('api/*')) {
                return ApiResponse::error('Unauthenticated', 401);
            }
        });

        $exceptions->render(function (ValidationException $e, $request) {
            if ($request->is('api/*')) {
                return ApiResponse::error('Validation failed', 422, $e->errors());
            }
        });

        $exceptions->render(function (ModelNotFoundException $e, $request) {
            if ($request->is('api/*')) {
                return ApiResponse::error('Resource not found', 404);
            }
        });

        $exceptions->render(function (RuntimeException $e, $request) {
            if ($request->is('api/*')) {
                return ApiResponse::error($e->getMessage(), 400);
            }
        });
    })

    ->create();
