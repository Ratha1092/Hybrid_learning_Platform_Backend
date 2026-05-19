<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use App\Domains\Courses\Models\Course;
use App\Domains\Courses\Models\Lesson;
use App\Policies\CoursePolicy;
use App\Policies\LessonPolicy;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(
            Course::class,
            CoursePolicy::class
        );

        Gate::policy(
            Lesson::class,
            LessonPolicy::class
        );
        // Rate Limiters
        // Register APIs
        RateLimiter::for('register', function (Request $request) {
            return Limit::perMinute(3)->by(
                $request->ip()
            );
        });
        // Login APIs
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(3)->by(
                $request->user()?->id
                    ?: $request->ip()
            );
        });

        // Authentication APIs
        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(10)->by(
                $request->user()?->id
                    ?: $request->ip()
            );
        });

        // Payment APIs
        RateLimiter::for('payments', function (Request $request) {
            return Limit::perMinute(10)->by(
                $request->user()?->id
                    ?: $request->ip()
            );
        });

        // Learning Progress APIs
        RateLimiter::for('learning', function (Request $request) {
            return Limit::perMinute(120)->by(
                $request->user()?->id
                    ?: $request->ip()
            );
        });

        // Course APIs
        RateLimiter::for('courses', function (Request $request) {
            return Limit::perMinute(60)->by(
                $request->user()?->id
                    ?: $request->ip()
            );
        });

        // Search APIs
        RateLimiter::for('search', function (Request $request) {
            return Limit::perMinute(30)->by(
                $request->user()?->id
                    ?: $request->ip()
            );
        });
        // Finance APIs
        RateLimiter::for('finance', function (Request $request) {
            return Limit::perMinute(20)->by(
                $request->user()?->id
                    ?: $request->ip()
            );
        });
    }
}