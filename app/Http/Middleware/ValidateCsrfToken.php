<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken as Middleware;

class ValidateCsrfToken extends Middleware
{
    // API routes are CSRF-safe via SameSite=Lax session cookies.
    // Cross-site POST requests from attacker origins cannot include
    // the laravel-session cookie, so session-authenticated actions
    // are already protected without a separate XSRF token.
    protected $except = [
        'api/*',
    ];
}
