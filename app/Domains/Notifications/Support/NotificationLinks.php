<?php

namespace App\Domains\Notifications\Support;

class NotificationLinks
{
    public static function frontend(string $path): string
    {
        $base = rtrim((string) config('app.frontend_url', env('FRONTEND_URL', 'http://localhost:3000')), '/');

        if (preg_match('/^https?:\/\//i', $path) === 1) {
            return $path;
        }

        return $base . '/' . ltrim($path, '/');
    }

    public static function admin(string $path): string
    {
        $base = rtrim((string) config('app.url', env('APP_URL', 'http://localhost:8000')), '/');

        if (preg_match('/^https?:\/\//i', $path) === 1) {
            return $path;
        }

        return $base . '/' . ltrim($path, '/');
    }
}
