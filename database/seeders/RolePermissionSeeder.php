<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    private const MODULES = [
        'users'      => ['view', 'create', 'update', 'delete'],
        'courses'    => ['view', 'create', 'update', 'delete', 'approve', 'reject', 'enroll'],
        'lessons'    => ['view', 'create', 'update', 'delete'],
        'categories' => ['view', 'create', 'update', 'delete'],
        'tags'       => ['view', 'create', 'update', 'delete'],
        'orders'     => ['view', 'create', 'update', 'delete'],
        'coupons'    => ['view', 'create', 'update', 'delete'],
        'payments'   => ['view', 'create', 'update', 'delete'],
        'payouts'    => ['view', 'create', 'update', 'delete'],
        'wallets'    => ['view', 'update', 'view-own'],
        'analytics'  => ['view', 'view-own'],
        'settings'   => ['view', 'update'],
        'settings_finance'  => ['view', 'update'],
        'settings_security' => ['view', 'update'],
        'roles'      => ['view', 'create', 'update', 'delete'],
        'reviews'    => ['view', 'create', 'update', 'delete', 'approve', 'reject'],
        'reports'    => ['export'],
        'progress'   => ['view-own'],
        'wishlist'   => ['manage'],
        'profile'    => ['manage'],
    ];
    private const ROLE_PERMISSIONS = [
        'admin' => ['users.*', 'courses.*', 'orders.*', 'coupons.*', 'analytics.view', 'reviews.*', 'settings.*',],
        'finance-manager' => ['payments.*', 'wallets.*', 'payouts.*', 'coupons.*', 'analytics.view', 'reports.export', 'settings_finance.*',],
        'accountant' => ['payments.view', 'wallets.view', 'payouts.view', 'reports.export',],
        'content-manager' => ['courses.*', 'categories.*', 'tags.*', 'reviews.*',],
        'moderator' => ['courses.approve', 'courses.reject', 'reviews.approve', 'reviews.reject',],
        'support-staff' => ['users.view', 'users.update', 'orders.view', 'reviews.view',],
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
