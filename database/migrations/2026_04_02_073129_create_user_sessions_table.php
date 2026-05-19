<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Track user sessions (security + device tracking)
     */
    public function up(): void
    {
        Schema::create('user_sessions', function (Blueprint $table) {
            $table->id();

            // RELATION
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // DEVICE INFO
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();

            // LAST ACTIVITY
            $table->timestamp('last_activity')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_sessions');
    }
};