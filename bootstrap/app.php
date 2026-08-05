<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use App\Support\ApiResponse;
use App\Exceptions\AccountSuspendedException;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        channels: __DIR__ . '/../routes/channels.php',
        health: '/up',
    )

    ->withMiddleware(function (Middleware $middleware): void {

        $middleware->alias([
            'role'               => RoleMiddleware::class,
            'permission'         => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
            'is_admin'           => \App\Http\Middleware\IsAdmin::class,
            'is_instructor'      => \App\Http\Middleware\IsInstructor::class,
            'verified_instructor'=> \App\Http\Middleware\VerifiedInstructor::class,
            'instructor'         => \App\Http\Middleware\EnsureUserIsInstructor::class,
        ]);

        $middleware->api(prepend: [
            \App\Http\Middleware\ForceHttps::class,
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            \App\Http\Middleware\CheckMaintenanceMode::class,
        ]);

        $middleware->web(prepend: [
            \App\Http\Middleware\ForceHttps::class,
            \App\Http\Middleware\ApplySessionTimeoutSetting::class,
            \App\Http\Middleware\CheckIpBlocklist::class,
        ]);
    })

    ->withProviders([
        App\Providers\EventServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
    ])

    ->withExceptions(function (Exceptions $exceptions): void {

        // Account Suspended Exception
        $exceptions->render(function (
            AccountSuspendedException $e,
            $request
        ) {
            if ($request->is('api/*')) {
                return ApiResponse::error(
                    $e->getMessage(),
                    $e->getStatusCode(),
                    null,
                    ['error_code' => AccountSuspendedException::ERROR_CODE]
                );
            }
        });

        // Authentication Exception
        $exceptions->render(function (
            AuthenticationException $e,
            $request
        ) {
            if ($request->is('api/*')) {
                return ApiResponse::error(
                    'Unauthenticated',
                    401
                );
            }
        });

        // Validation Exception
        $exceptions->render(function (
            ValidationException $e,
            $request
        ) {
            if ($request->is('api/*')) {
                return ApiResponse::error(
                    'Validation failed',
                    422,
                    $e->errors()
                );
            }
        });

        // Model Not Found Exception
        $exceptions->render(function (
            ModelNotFoundException $e,
            $request
        ) {
            if ($request->is('api/*')) {
                return ApiResponse::error(
                    'Resource not found',
                    404
                );
            }
        });

        // Livewire update endpoint hit with GET (browser history / back-button cache)
        $exceptions->render(function (
            MethodNotAllowedHttpException $e,
            $request
        ) {
            if (!$request->is('api/*') && str_contains($request->path(), '/update') && str_contains($request->path(), 'livewire')) {
                return redirect('/admin');
            }
        });

        // Runtime Exception
        $exceptions->render(function (
            \RuntimeException $e,
            $request
        ) {
            if ($request->is('api/*')) {
                return ApiResponse::error(
                    $e->getMessage(),
                    400
                );
            }
        });
    })

    ->create();
