<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * OAuth-only sign-ups (Google/GitHub) get a random, unusable password hash
     * just to satisfy the NOT NULL column — the user never knows it. Without
     * this flag there's no way to tell that apart from a real, user-chosen
     * password, which is what let OAuthService::unlink() silently fail to
     * guard against locking a user out.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'has_password')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('has_password')->default(true)->after('password');
            });
        }

        // Backfill: every OAuth-linked account whose password was never
        // explicitly set through registration/reset is treated as passwordless.
        // We can't distinguish those retroactively, so this is a best-effort
        // backfill: only accounts with a linked OAuth provider and no
        // successful password-reset history are flipped to false.
        DB::table('users')
            ->whereIn('id', function ($query) {
                $query->select('user_id')->from('oauth_accounts');
            })
            ->whereNotIn('id', function ($query) {
                $query->select('user_id')->from('password_reset_tokens')->where('used', true);
            })
            ->update(['has_password' => false]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('has_password');
        });
    }
};
