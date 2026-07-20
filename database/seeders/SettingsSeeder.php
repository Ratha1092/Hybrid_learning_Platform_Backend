<?php

namespace Database\Seeders;

use App\Domains\System\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * group => [ key => [value, type, description, is_public] ]
     */
    private const CATALOG = [
        'general' => [
            'site_name'            => ['Hybrid Learning', 'string', 'Platform display name shown across the site.', true],
            'site_logo'            => ['', 'string', 'URL/path to the platform logo.', true],
            'site_favicon'         => ['', 'string', 'URL/path to the favicon.', true],
            'site_description'     => ['Learn and grow with us.', 'string', 'Short tagline used for SEO and branding.', true],
            'support_email'        => ['support@example.com', 'string', 'Public support contact email.', true],
            'support_phone'        => ['', 'string', 'Public support contact phone number.', true],
            'contact_address'      => ['8 Charter Street, Bldg 1295, Natalie Tower, Phnom Penh', 'string', 'Physical office address shown on the Contact page.', true],
            'hours_weekday'        => ['8:00 am – 6:00 pm', 'string', 'Office hours for Monday–Friday shown on the Contact page.', true],
            'hours_saturday'       => ['9:00 am – 1:00 pm', 'string', 'Office hours for Saturday shown on the Contact page.', true],
            'hours_sunday'         => ['Closed', 'string', 'Office hours for Sunday shown on the Contact page (e.g. Closed or a time range).', true],
            'default_language'     => ['en', 'string', 'Default platform language.', true],
            'default_timezone'     => ['UTC', 'string', 'Default timezone for date/time display.', true],
            'maintenance_mode'     => ['false', 'boolean', 'When enabled, blocks non-staff access to the platform.', true],
            'registration_enabled' => ['true', 'boolean', 'Whether new user signups are allowed.', true],
        ],
        'branding' => [
            'primary_color'   => ['#7c3aed', 'string', 'Primary brand color (hex).', true],
            'secondary_color' => ['#2563eb', 'string', 'Secondary brand color (hex).', true],
            'footer_text'     => ['© Hybrid Learning. All rights reserved.', 'string', 'Footer copyright text.', true],
            'social_facebook' => ['', 'string', 'Facebook page URL.', true],
            'social_linkedin' => ['', 'string', 'LinkedIn page URL.', true],
            'social_youtube'  => ['', 'string', 'YouTube channel URL.', true],
            'social_twitter'  => ['', 'string', 'Twitter/X profile URL.', true],
        ],
        'auth' => [
            'email_verification_required' => ['false', 'boolean', 'Require a verified email before a user can log in. Off by default to preserve existing behavior — enable once your email delivery is confirmed working.', false],
            'enable_google_login'         => ['true', 'boolean', 'Show the Google login option.', true],
            'enable_facebook_login'       => ['false', 'boolean', 'Show the Facebook login option.', true],
            'enable_2fa'                  => ['false', 'boolean', 'Allow users to enable two-factor authentication.', false],
            'max_login_attempts'          => ['5', 'integer', 'Failed login attempts before an account is locked.', false],
            'account_lock_duration'       => ['15', 'integer', 'Lockout duration in minutes after max login attempts.', false],
            'session_timeout'             => ['120', 'integer', 'Session idle timeout in minutes.', false],
        ],
        'instructor' => [
            'require_instructor_verification' => ['true', 'boolean', 'Require document verification before becoming an instructor.', false],
            'auto_approve_instructors'        => ['false', 'boolean', 'Automatically approve instructor applications.', false],
            'allow_instructor_profile_edit'   => ['true', 'boolean', 'Allow instructors to edit their public profile.', false],
            'featured_instructor_limit'       => ['6', 'integer', 'Number of instructors shown in the featured section.', true],
        ],
        'course' => [
            'course_auto_approval'      => ['false', 'boolean', 'Automatically publish courses without manual review.', false],
            'allow_free_courses'        => ['true', 'boolean', 'Allow instructors to publish $0 courses.', true],
            'certificate_enabled'       => ['true', 'boolean', 'Allow completion certificates platform-wide.', true],
            'max_course_thumbnail_size' => ['2048', 'integer', 'Max course thumbnail size in KB.', true],
            'max_video_upload_size'     => ['512000', 'integer', 'Max lesson video upload size in KB.', true],
            'allowed_video_formats'     => ['mp4,mov,avi,webm', 'string', 'Comma-separated list of accepted video formats.', true],
            'max_lessons_per_course'    => ['200', 'integer', 'Maximum number of lessons allowed per course.', true],
            'course_access_duration_months' => ['6', 'integer', 'Months a student keeps access to a purchased course before it expires. Set to 0 for lifetime access.', true],
        ],
        'finance' => [
            'default_commission_percentage' => ['20', 'decimal', 'Platform commission percentage taken from each sale.', false],
            'minimum_payout_amount'         => ['50', 'decimal', 'Minimum wallet balance required to request a payout.', false],
            'default_currency'              => ['USD', 'string', 'Default platform currency.', true],
            'tax_percentage'                => ['0', 'decimal', 'Tax percentage applied at checkout.', true],
            'refund_period_days'            => ['14', 'integer', 'Number of days a purchase remains eligible for refund.', true],
            'wallet_enabled'                => ['true', 'boolean', 'Enable the instructor wallet system.', false],
            'payout_hold_period_days'       => ['14', 'integer', 'Days to hold new earnings in pending balance before they become withdrawable.', false],
        ],
        'payment_gateway' => [
            'bakong_enabled'        => ['true', 'boolean', 'Enable the Bakong payment gateway.', true],
            'khqr_enabled'          => ['true', 'boolean', 'Enable KHQR payments.', true],
            'paypal_enabled'        => ['false', 'boolean', 'Enable PayPal payments.', true],
            'stripe_enabled'        => ['false', 'boolean', 'Enable Stripe payments.', true],
            'bank_transfer_enabled' => ['false', 'boolean', 'Enable manual bank transfer payments.', true],
        ],
        'notification' => [
            'email_notifications_enabled'    => ['true', 'boolean', 'Master toggle for outgoing email notifications.', false],
            'push_notifications_enabled'     => ['false', 'boolean', 'Master toggle for push notifications.', false],
            'course_purchase_notification'   => ['true', 'boolean', 'Notify on course purchase.', false],
            'course_completion_notification' => ['true', 'boolean', 'Notify on course completion.', false],
            'payout_notification'            => ['true', 'boolean', 'Notify instructors on payout processing.', false],
        ],
        'analytics' => [
            'track_course_views'         => ['true', 'boolean', 'Record course view events.', false],
            'track_user_activity'        => ['true', 'boolean', 'Record general user activity for analytics.', false],
            'retain_analytics_days'      => ['365', 'integer', 'Number of days analytics data is retained.', false],
            'dashboard_refresh_interval' => ['60', 'integer', 'Admin dashboard auto-refresh interval in seconds.', false],
        ],
        'security' => [
            'password_min_length'                 => ['8', 'integer', 'Minimum required password length.', false],
            'password_require_special_characters' => ['true', 'boolean', 'Require at least one special character in passwords.', false],
            'ip_whitelist_enabled'                 => ['false', 'boolean', 'Restrict admin panel access to whitelisted IPs.', false],
            'audit_log_retention_days'             => ['180', 'integer', 'Number of days audit log entries are retained.', false],
            'force_https'                          => ['true', 'boolean', 'Force all traffic over HTTPS.', false],
        ],
        'content_moderation' => [
            'auto_approve_reviews'      => ['false', 'boolean', 'Automatically publish reviews without moderation.', false],
            'review_moderation_required' => ['true', 'boolean', 'Require moderator approval before reviews are published.', false],
            'course_review_required'    => ['true', 'boolean', 'Require moderator approval before courses are published.', false],
            'allow_user_reports'        => ['true', 'boolean', 'Allow users to report courses, reviews, or other users.', true],
        ],
        'email' => [
            'mail_from_name'    => ['Hybrid Learning', 'string', 'Default "from" name on outgoing emails.', false],
            'mail_from_address' => ['no-reply@example.com', 'string', 'Default "from" address on outgoing emails.', false],
            'reply_to_email'    => ['support@example.com', 'string', 'Default reply-to address on outgoing emails.', false],
            'smtp_test_mode'    => ['false', 'boolean', 'Route all outgoing email through a test sink instead of real delivery.', false],
        ],
    ];

    public function run(): void
    {
        foreach (self::CATALOG as $group => $keys) {
            foreach ($keys as $key => [$value, $type, $description, $isPublic]) {
                Setting::firstOrCreate(
                    ['key' => $key],
                    [
                        'value' => $value,
                        'group' => $group,
                        'type' => $type,
                        'description' => $description,
                        'is_public' => $isPublic,
                    ]
                );
            }
        }
    }
}
