<?php

namespace App\Filament\Pages;

use App\Domains\Courses\Models\Course;
use App\Domains\System\Models\Setting;
use App\Support\PanelAccess;
use BackedEnum;
use Filament\Pages\Page;

class Settings extends Page
{
    protected string $view = 'filament.pages.settings';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Settings';
    protected static string|\UnitEnum|null $navigationGroup = 'System';
    protected static ?int $navigationSort = 1;
    protected static ?string $slug = 'settings';

    public const GROUPS = [
        'general' => ['General', 'M3.75 6A2.25 2.25 0 0 1 6 3.75h12A2.25 2.25 0 0 1 20.25 6v12A2.25 2.25 0 0 1 18 20.25H6A2.25 2.25 0 0 1 3.75 18V6Z M8.25 8.25h7.5v7.5h-7.5z', '#2563eb', 'Core site identity and availability.', 'settings'],
        'branding' => ['Branding', 'M9.53 16.122a3 3 0 0 0-5.78 1.128 2.25 2.25 0 0 1-2.4 2.245 4.5 4.5 0 0 0 8.4-2.245c0-.399-.078-.78-.22-1.128Zm0 0a15.998 15.998 0 0 0 3.388-1.62m-5.043-.025a15.994 15.994 0 0 1 1.622-3.395m3.42 3.42a15.995 15.995 0 0 0 4.764-4.648l3.876-5.814a1.151 1.151 0 0 0-1.597-1.597L14.146 6.32a15.996 15.996 0 0 0-4.649 4.763m3.42 3.42a6.776 6.776 0 0 0-3.42-3.42', '#d946ef', 'Colors, footer, and social links.', 'settings'],
        'auth' => ['User & Authentication', 'M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z', '#0d9488', 'Login, verification, and session policy.', 'settings'],
        'instructor' => ['Instructor', 'M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342', '#d97706', 'Verification and instructor-facing limits.', 'settings'],
        'course' => ['Course', 'M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25', '#16a34a', 'Approval flow and upload limits.', 'settings'],
        'finance' => ['Finance', 'M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z', '#7c3aed', 'Commission, payouts, and currency.', 'settings_finance'],
        'payment_gateway' => ['Payment Gateways', 'M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z', '#0891b2', 'Which checkout methods are enabled.', 'settings_finance'],
        'notification' => ['Notifications', 'M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0', '#ec4899', 'Which events trigger notifications.', 'settings'],
        'analytics' => ['Analytics', 'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z', '#3b82f6', 'Tracking and data retention.', 'settings'],
        'security' => ['Security', 'M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z', '#dc2626', 'Password policy and platform-wide hardening.', 'settings_security'],
        'content_moderation' => ['Content Moderation', 'm11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z', '#f59e0b', 'Review and report moderation policy.', 'settings'],
        'email' => ['Email', 'M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75', '#64748b', 'Outgoing mail identity.', 'settings'],
    ];

    public const LANGUAGES = [
        'en' => 'English',
        'km' => 'Khmer',
    ];

    public const TIMEZONES = [
        'UTC' => 'UTC',
        'Asia/Phnom_Penh' => 'Asia/Phnom Penh',
        'Europe/London' => 'Europe/London',
        'America/New_York' => 'America/New York',
    ];

    public const SECTIONS = [
        'general' => [
            'Site Identity' => ['site_name', 'site_logo', 'site_favicon', 'site_description'],
            'Contact' => ['support_email', 'support_phone', 'contact_address', 'hours_weekday', 'hours_saturday', 'hours_sunday'],
            'Localization' => ['default_language', 'default_timezone'],
            'Platform Status' => ['maintenance_mode', 'registration_enabled'],
        ],
        'branding' => [
            'Colors' => ['primary_color', 'secondary_color'],
            'Footer' => ['footer_text'],
            'Social Links' => ['social_facebook', 'social_linkedin', 'social_youtube', 'social_twitter'],
        ],
        'auth' => [
            'Verification & Security' => ['email_verification_required'],
            'Social Login' => ['enable_google_login', 'enable_facebook_login'],
            'Login Protection' => ['max_login_attempts', 'account_lock_duration', 'session_timeout'],
        ],
        'instructor' => [
            'Verification' => ['require_instructor_verification', 'auto_approve_instructors'],
            'Profile' => ['allow_instructor_profile_edit', 'featured_instructor_limit'],
        ],
        'course' => [
            'Approval' => ['course_auto_approval', 'free_course_auto_approval', 'allow_free_courses'],
            'Upload Limits' => ['max_course_thumbnail_size', 'max_video_upload_size', 'allowed_video_formats', 'max_lessons_per_course'],
            'Access' => ['course_access_duration_months', 'lesson_resources_downloadable'],
        ],
        'finance' => [
            'Payouts & Currency' => ['minimum_payout_amount', 'payout_hold_period_days', 'default_currency', 'wallet_enabled', 'auto_verify_payout_accounts'],
            'Tax' => ['tax_percentage'],
        ],
        'payment_gateway' => [
            'Checkout Methods' => ['bakong_enabled', 'khqr_enabled', 'paypal_enabled', 'stripe_enabled', 'bank_transfer_enabled'],
        ],
        'notification' => [
            'Channels' => ['email_notifications_enabled', 'push_notifications_enabled'],
            'Events' => ['course_purchase_notification', 'course_completion_notification', 'payout_notification'],
        ],
        'analytics' => [
            'Tracking' => ['track_course_views', 'track_user_activity'],
            'Retention' => ['retain_analytics_days', 'dashboard_refresh_interval'],
        ],
        'security' => [
            'Password Policy' => ['password_min_length', 'password_require_special_characters'],
            'Platform Hardening' => ['ip_whitelist_enabled', 'force_https', 'audit_log_retention_days'],
        ],
        'content_moderation' => [
            'Reviews' => ['auto_approve_reviews', 'review_moderation_required'],
            'Courses & Reports' => ['course_review_required', 'allow_user_reports'],
        ],
        'email' => [
            'Sender Identity' => ['mail_from_name', 'mail_from_address', 'reply_to_email'],
            'Delivery' => ['smtp_test_mode'],
        ],
    ];

    public static function canAccess(): bool
    {
        return PanelAccess::can('settings.view')
            || PanelAccess::can('settings_finance.view')
            || PanelAccess::can('settings_security.view');
    }

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return '';
    }

    protected function getViewData(): array
    {
        $user = auth()->user();

        $visibleGroups = [];
        foreach (self::GROUPS as $key => [$label, $icon, $color, $description, $permission]) {
            if (!$user->can("{$permission}.view")) {
                continue;
            }

            $groupSettings = Setting::byGroup($key);

            $visibleGroups[$key] = [
                'key' => $key,
                'label' => $label,
                'icon' => $icon,
                'color' => $color,
                'description' => $description,
                'permission' => $permission,
                'canUpdate' => $user->can("{$permission}.update"),
                'settings' => $groupSettings,
                'sections' => $this->buildSections($key, $groupSettings),
            ];
        }

        $commissionValue = (float) Setting::get('default_commission_percentage', 20);

        $courseStats = [
            'total'   => Course::withoutGlobalScopes()->count(),
            'at_rate' => Course::withoutGlobalScopes()->where('commission_percentage', $commissionValue)->count(),
        ];

        $availableLanguages = self::LANGUAGES;
        $availableTimezones = self::TIMEZONES;

        return compact('visibleGroups', 'commissionValue', 'courseStats', 'availableLanguages', 'availableTimezones');
    }
    private function buildSections(string $groupKey, $groupSettings): array
    {
        $byKey = $groupSettings->keyBy('key');
        $sections = [];
        $used = [];

        foreach (self::SECTIONS[$groupKey] ?? [] as $sectionLabel => $keys) {
            $items = collect($keys)
                ->map(fn (string $key) => $byKey->get($key))
                ->filter();

            if ($items->isEmpty()) {
                continue;
            }

            $used = [...$used, ...$items->pluck('key')->all()];
            $sections[] = ['label' => $sectionLabel, 'settings' => $items->values()];
        }

        $leftover = $groupSettings
            ->reject(fn ($setting) => in_array($setting->key, $used, true) || $setting->key === 'default_commission_percentage')
            ->values();

        if ($leftover->isNotEmpty()) {
            $sections[] = ['label' => 'Other', 'settings' => $leftover];
        }

        return $sections;
    }
}
