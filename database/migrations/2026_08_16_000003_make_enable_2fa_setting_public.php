<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

// The frontend now hides the 2FA UI when this setting is off, but that
// requires reading it from the public settings endpoint, which filters on
// is_public. It was seeded as private before this UI existed.
return new class extends Migration
{
    public function up(): void
    {
        DB::table('settings')->where('key', 'enable_2fa')->update(['is_public' => true]);
        $this->flushSettingsCache();
    }

    public function down(): void
    {
        DB::table('settings')->where('key', 'enable_2fa')->update(['is_public' => false]);
        $this->flushSettingsCache();
    }

    // Setting::allPublic()/allCached() cache forever and are only invalidated
    // by Setting::set() — a raw DB update bypasses that, so clear it here too.
    private function flushSettingsCache(): void
    {
        Cache::forget('settings.all');
        Cache::forget('settings.public');
    }
};
