<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Several Notification classes wrote ad-hoc `data->type` strings
     * (e.g. 'role_changed', 'instructor_approved') instead of the
     * canonical NotificationType enum values, so already-stored rows
     * render with the wrong category/color in the admin notifications UI.
     * This backfills existing rows to match the values the classes now emit.
     */
    private array $typeFixes = [
        'App\\Domains\\Notifications\\Notifications\\RoleChangedNotification' => 'system',
        'App\\Domains\\Notifications\\Notifications\\EnrollmentConfirmedNotification' => 'course',
        'App\\Domains\\Notifications\\Notifications\\InstructorApprovedNotification' => 'instructor_verification',
        'App\\Domains\\Notifications\\Notifications\\InstructorRejectedNotification' => 'instructor_verification',
        'App\\Domains\\Notifications\\Notifications\\CourseCompletionNotification' => 'course',
        'App\\Domains\\Notifications\\Notifications\\CourseApprovedNotification' => 'course',
        'App\\Domains\\Notifications\\Notifications\\CourseRejectedNotification' => 'course',
        'App\\Domains\\Notifications\\Notifications\\AdminNewOrderNotification' => 'order',
        'App\\Domains\\Notifications\\Notifications\\AdminPaymentNotification' => 'payment',
    ];

    public function up(): void
    {
        foreach ($this->typeFixes as $class => $type) {
            DB::table('notifications')
                ->where('type', $class)
                ->update([
                    'data' => DB::raw("jsonb_set(data::jsonb, '{type}', to_jsonb('" . $type . "'::text))"),
                ]);
        }

        // AdminPaymentNotification also stored the message under `body`
        // instead of `message`, and lacked `type` entirely.
        DB::table('notifications')
            ->where('type', 'App\\Domains\\Notifications\\Notifications\\AdminPaymentNotification')
            ->whereRaw("jsonb_exists(data::jsonb, 'body')")
            ->update([
                'data' => DB::raw("(data::jsonb - 'body') || jsonb_build_object('message', data::jsonb->>'body')"),
            ]);
    }

    public function down(): void
    {
        // Historical data backfill — not reversible.
    }
};
