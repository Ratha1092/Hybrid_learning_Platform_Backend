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
        // Create OAuth providers table
        if (!Schema::hasTable('oauth_accounts')) {
            Schema::create('oauth_accounts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('provider'); // 'google', 'github'
                $table->string('provider_id')->unique();
                $table->string('email');
                $table->string('name')->nullable();
                $table->string('avatar')->nullable();
                $table->json('data')->nullable(); // Raw provider data
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent();
                $table->unique(['user_id', 'provider']);
                $table->index('provider_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oauth_accounts');
    }
};
