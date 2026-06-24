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
use App\Domains\Payments\Services\BakongConfig;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(BakongConfig::class, fn () => new BakongConfig(
            merchantName: config('services.bakong.merchant_name'),
            merchantCity: config('services.bakong.merchant_city'),
            merchantAccountId: config('services.bakong.merchant_account_id'),
            merchantCategoryCode: config('services.bakong.merchant_category_code'),
            countryCode: config('services.bakong.country_code'),
            currency: config('services.bakong.currency'),
            qrTtlMinutes: config('services.bakong.qr_ttl_minutes'),
            verifyUrl: config('services.bakong.verify_url'),
            apiToken: config('services.bakong.api_token'),
            timeout: config('services.bakong.timeout'),
            acquiringBank: config('services.bakong.acquiring_bank'),
            merchantId: config('services.bakong.merchant_id'),
        ));
    }

    public function boot(): void
    {
        Gate::before(fn ($user) => $user->hasRole('super-admin') ? true : null);

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

        // Outbound email rate limit for Resend API
        RateLimiter::for('resend-emails', fn () => Limit::perMinute(20));
    }
}