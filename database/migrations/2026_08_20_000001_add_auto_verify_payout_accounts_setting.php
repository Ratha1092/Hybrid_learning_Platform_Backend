<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!DB::table('settings')->where('key', 'auto_verify_payout_accounts')->exists()) {
            DB::table('settings')->insert([
                'key' => 'auto_verify_payout_accounts',
                'value' => 'false',
                'group' => 'finance',
                'type' => 'boolean',
                'description' => 'Automatically verify instructor payout account submissions (bank details/QR code) without manual review.',
                'is_public' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->flushSettingsCache();
    }

    public function down(): void
    {
        DB::table('settings')->where('key', 'auto_verify_payout_accounts')->delete();
        $this->flushSettingsCache();
    }

    private function flushSettingsCache(): void
    {
        Cache::forget('settings.all');
        Cache::forget('settings.public');
    }
};
