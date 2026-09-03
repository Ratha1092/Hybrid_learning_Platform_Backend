<?php

namespace App\Providers;

use App\Domains\Courses\Models\Course;
use App\Domains\Courses\Models\Lesson;
use App\Domains\Courses\Models\Section;
use App\Domains\Courses\Observers\CourseObserver;
use App\Domains\Courses\Observers\LessonObserver;
use App\Domains\Courses\Observers\SectionObserver;
use App\Domains\Learning\Models\Review;
use App\Domains\Payments\Services\BakongConfig;
use App\Domains\System\Models\Setting;
use App\Policies\CoursePolicy;
use App\Policies\LessonPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        // Behind Railway's proxy, request-based scheme detection (trustProxies)
        // isn't reliable this early in the boot cycle — some URLs (e.g. the
        // Filament panel favicon, built during provider boot) get generated
        // before the TrustProxies middleware has run. Force https explicitly
        // instead of relying on per-request detection timing.
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        Course::observe(CourseObserver::class);
        Section::observe(SectionObserver::class);
        Lesson::observe(LessonObserver::class);

        Relation::morphMap([
            'course' => Course::class,
            'review' => Review::class,
        ]);

        Gate::before(fn ($user) => $user->hasRole('super-admin') ? true : null);

        config(['app.name' => Setting::get('site_name', config('app.name'))]);
        config(['app.locale' => Setting::get('default_language', config('app.locale'))]);
        config(['app.timezone' => Setting::get('default_timezone', config('app.timezone'))]);
        app()->setLocale(config('app.locale'));
        date_default_timezone_set(config('app.timezone'));

        View::share([
            'siteName' => Setting::get('site_name', config('app.name')),
            'siteLogo' => Setting::get('site_logo', ''),
            'siteFavicon' => Setting::get('site_favicon', ''),
            'siteDescription' => Setting::get('site_description', ''),
            'supportEmail' => Setting::get('support_email', ''),
            'supportPhone' => Setting::get('support_phone', ''),
            'contactAddress' => Setting::get('contact_address', ''),
            'footerText' => Setting::get('footer_text', "© " . now()->year . " " . Setting::get('site_name', config('app.name')) . ". All rights reserved."),
            'hoursWeekday' => Setting::get('hours_weekday', ''),
            'hoursSaturday' => Setting::get('hours_saturday', ''),
            'hoursSunday' => Setting::get('hours_sunday', ''),
        ]);

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

        config(['app.locale' => Setting::get('default_language', config('app.locale'))]);
        app()->setLocale(config('app.locale'));

        config(['app.timezone' => Setting::get('default_timezone', config('app.timezone'))]);
        date_default_timezone_set(config('app.timezone'));

        // Authentication APIs
        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(10)->by(
                $request->user()?->id
                    ?: $request->ip()
            );
        });

        // Payment APIs — status polling during KHQR QR scan happens every few
        // seconds, so this needs to be polling-friendly, not auth-style strict.
        RateLimiter::for('payments', function (Request $request) {
            return Limit::perMinute(60)->by(
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

        // Site-wide Community APIs
        RateLimiter::for('community', function (Request $request) {
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

        // Profile dashboard
        RateLimiter::for('profile', function (Request $request) {
            return Limit::perMinute(30)->by(
                $request->user()?->id ?: $request->ip()
            );
        });

        // email rate limit for Resend API
        RateLimiter::for('resend-emails', fn () => Limit::perMinute(20));

        // Public contact form — unauthenticated, so keyed by IP only.
        RateLimiter::for('contact', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });
    }
}