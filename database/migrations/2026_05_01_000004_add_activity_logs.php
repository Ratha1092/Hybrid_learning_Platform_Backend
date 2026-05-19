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
        // Create activity logs table
        if (!Schema::hasTable('activity_logs')) {
            Schema::create('activity_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
                $table->string('action'); // 'login', 'logout', 'password_changed', 'email_verified', '2fa_enabled', etc.
                $table->string('ip_address')->nullable();
                $table->string('user_agent')->nullable();
                $table->json('data')->nullable(); // Additional context
                $table->timestamp('created_at')->useCurrent();
                $table->index('user_id');
                $table->index('action');
                $table->index('created_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
