<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('password_reset_tokens') && !Schema::hasColumn('password_reset_tokens', 'user_id')) {
            Schema::drop('password_reset_tokens');
        }

        if (!Schema::hasTable('password_reset_tokens')) {
            Schema::create('password_reset_tokens', function (Blueprint $table) {
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

    public function down(): void
    {
        Schema::dropIfExists('password_reset_tokens');
    }
};
