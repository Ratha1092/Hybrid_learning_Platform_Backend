<?php

namespace App\Support;

class PanelAccess
{
    /**
     * Whether the current admin-panel user has the given Spatie permission.
     * super-admin always passes (Gate::before bypass in AppServiceProvider).
     */
    public static function can(string $permission): bool
    {
        return auth()->user()?->can($permission) ?? false;
    }

    /**
     * Whether the current admin-panel user is allowed into the panel at all (any staff role).
     */
    public static function isStaff(): bool
    {
        return auth()->user()?->isStaff() ?? false;
    }
}
