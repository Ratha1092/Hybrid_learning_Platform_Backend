<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    private const MODULES = [
        'users'                  => ['view', 'create', 'update', 'delete'],
        'instructor_verifications' => ['view', 'approve', 'reject'],
        'courses'    => ['view', 'create', 'update', 'delete', 'approve', 'reject', 'enroll'],
        'lessons'    => ['view', 'create', 'update', 'delete'],
        'categories' => ['view', 'create', 'update', 'delete'],
        'tags'       => ['view', 'create', 'update', 'delete'],
        'orders'     => ['view', 'create', 'update', 'delete'],
        'coupons'    => ['view', 'create', 'update', 'delete'],
        'payments'   => ['view', 'create', 'update', 'delete'],
        'invoices'   => ['view', 'download', 'resend'],
        'receipts'   => ['view', 'download', 'resend'],
        'payouts'    => ['view', 'create', 'update', 'delete', 'download'],
        'payout_accounts' => ['view', 'approve', 'reject'],
        'wallets'    => ['view', 'update', 'view-own'],
        'analytics'  => ['view', 'view-own'],
        'settings'   => ['view', 'update'],
        'settings_finance'  => ['view', 'update'],
        'settings_security' => ['view', 'update'],
        'roles'      => ['view', 'create', 'update', 'delete'],
        'reviews'    => ['view', 'create', 'update', 'delete', 'approve', 'reject'],
        'content_reports' => ['view', 'update'],
        'contact_messages' => ['view', 'update'],
        'reports'    => ['export', 'view_executive', 'view_revenue', 'view_payment', 'view_payout','view_course_intelligence', 'view_instructor_intelligence','view_learning_intelligence', 'view_user', 'view_audit', 'schedule'],
        'bi'         => ['view_executive', 'view_students', 'view_courses', 'view_financial', 'view_instructors', 'view_marketplace', 'view_revenue', 'view_operations'],
        'system'     => ['view_logs', 'view_security', 'view_health'],
        'progress'   => ['view-own'],
        'wishlist'   => ['manage'],
        'profile'    => ['manage'],
    ];
    private const ROLE_PERMISSIONS = [
        'finance' => ['payments.*', 'invoices.*', 'receipts.*', 'wallets.*', 'payouts.*', 'payout_accounts.*', 'coupons.*', 'analytics.view', 'reports.export','reports.view_revenue', 'reports.view_payment', 'reports.view_payout', 'reports.schedule', 'settings_finance.*', 'bi.view_revenue', 'bi.view_financial',],
        'instructor' => ['courses.create', 'courses.update', 'lessons.create', 'lessons.update','analytics.view-own', 'wallets.view-own', 'payouts.create',],
        'student' => ['courses.enroll', 'progress.view-own', 'reviews.create', 'wishlist.manage', 'profile.manage',],
    ];

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $allPermissions = [];
        foreach (self::MODULES as $module => $actions) {
            foreach ($actions as $action) {
                $name = "{$module}.{$action}";
                Permission::findOrCreate($name);
                $allPermissions[$module][] = $name;
            }
        }

        $flatPermissions = collect($allPermissions)->flatten()->all();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $superAdmin = Role::findOrCreate('super-admin');
        $superAdmin->syncPermissions($flatPermissions);
        foreach (self::ROLE_PERMISSIONS as $roleName => $entries) {
            $role = Role::findOrCreate($roleName);
            $resolved = collect($entries)->flatMap(function (string $entry) use ($allPermissions) {
                if (str_ends_with($entry, '.*')) {
                    $module = substr($entry, 0, -2);
                    return $allPermissions[$module] ?? [];
                }
                return [$entry];
            })->unique()->values()->all();

            $role->syncPermissions($resolved);
        }
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
