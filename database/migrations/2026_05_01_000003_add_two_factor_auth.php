<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add 2FA fields to users table
        if (!Schema::hasColumn('users', 'two_factor_enabled')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('two_factor_enabled')->default(false)->after('email_verified_at');
                $table->string('two_factor_secret')->nullable()->after('two_factor_enabled');
            });
        }

        // Create 2FA codes table
        if (!Schema::hasTable('two_factor_codes')) {
            Schema::create('two_factor_codes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('code'); // 6-digit OTP
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('expires_at');
                $table->boolean('used')->default(false);
                $table->index('user_id');
                $table->index('code');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('two_factor_enabled');
            $table->dropColumn('two_factor_secret');
        });

        Schema::dropIfExists('two_factor_codes');
    }
};
