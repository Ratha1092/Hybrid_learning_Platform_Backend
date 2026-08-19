<?php

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * The Business Intelligence pages (app/Filament/Pages/BI/*) gate access via
     * bi.view_* permissions, but those permissions were never created by the
     * seeder — so only super-admin (via the Gate::before bypass) could ever
     * reach them. This backfills the permission rows for databases that were
     * already migrated before RolePermissionSeeder learned about the 'bi' module.
     */
    private const BI_PERMISSIONS = [
        'bi.view_executive',
        'bi.view_students',
        'bi.view_courses',
        'bi.view_financial',
        'bi.view_instructors',
        'bi.view_marketplace',
        'bi.view_revenue',
        'bi.view_operations',
    ];

    public function up(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (self::BI_PERMISSIONS as $name) {
            Permission::findOrCreate($name);
        }

        if ($superAdmin = Role::where('name', 'super-admin')->first()) {
            $superAdmin->givePermissionTo(self::BI_PERMISSIONS);
        }

        // Mirrors finance's existing reports.view_revenue/view_payment/view_payout access.
        if ($finance = Role::where('name', 'finance')->first()) {
            $finance->givePermissionTo(['bi.view_revenue', 'bi.view_financial']);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Permission::whereIn('name', self::BI_PERMISSIONS)->get()->each->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
