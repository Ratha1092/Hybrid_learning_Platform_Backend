<?php

use App\Domains\Users\Models\User;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * These roles were auto-seeded but never intentionally used — the team is small
     * and only needs Super Admin, Finance, Instructor, and Student for now. New roles
     * should be created manually by a super-admin via the Roles page, not by the seeder.
     */
    private const REMOVED_ROLES = ['admin', 'accountant', 'content-manager', 'moderator', 'support-staff'];

    /**
     * Demo accounts that exist solely to exercise the roles being retired.
     */
    private const DEMO_EMAILS = [
        'admin@example.com',
        'accountant@example.com',
        'content.manager@example.com',
        'moderator@example.com',
        'support.staff@example.com',
    ];

    public function up(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        User::whereIn('email', self::DEMO_EMAILS)->get()->each(function (User $user) {
            if ($user->roles->pluck('name')->diff(self::REMOVED_ROLES)->isEmpty()) {
                $user->delete();
            }
        });

        // Deleting the role cascades to model_has_roles / role_has_permissions (FK
        // cascadeOnDelete), so any other user unexpectedly holding one of these roles
        // simply loses it rather than being deleted along with it.
        Role::whereIn('name', self::REMOVED_ROLES)->get()->each->delete();

        // Simpler naming for a small team — one finance role, not "finance-manager".
        Role::where('name', 'finance-manager')->update(['name' => 'finance']);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Role::where('name', 'finance')->update(['name' => 'finance-manager']);

        foreach (self::REMOVED_ROLES as $roleName) {
            Role::findOrCreate($roleName);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
