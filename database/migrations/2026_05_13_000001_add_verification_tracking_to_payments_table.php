<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->unsignedInteger('verification_attempts')
                ->default(0)
                ->after('status');
            $table->timestamp('last_verified_at')
                ->nullable()
                ->after('paid_at');
            $table->index('last_verified_at');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['last_verified_at']);
            $table->dropColumn([
                'verification_attempts',
                'last_verified_at',
            ]);
        });
    }
};
