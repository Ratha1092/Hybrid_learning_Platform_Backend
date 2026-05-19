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
        // Add verification fields to users table
        if (!Schema::hasColumn('users', 'email_verified_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('email_verified_at')->nullable()->after('password');
            });
        }

        // Create email verification tokens table
        if (!Schema::hasTable('email_verification_tokens')) {
            Schema::create('email_verification_tokens', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('token')->unique();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('expires_at');
                $table->boolean('used')->default(false);
                $table->index('user_id');
                $table->index('token');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('email_verified_at');
        });

        Schema::dropIfExists('email_verification_tokens');
    }
};
