<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {

            $table->text('khqr_payload')
                ->nullable()
                ->after('failure_reason');
            $table->string('payer_account')
                ->nullable()
                ->after('khqr_payload');
            $table->json('gateway_response')
                ->nullable()
                ->after('meta');
            $table->timestamp('expires_at')
                ->nullable()
                ->after('paid_at');

            $table->index('external_reference');
            $table->index('expires_at');

        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {

            $table->dropIndex(['external_reference']);
            $table->dropIndex(['expires_at']);

            $table->dropColumn([
                'khqr_payload',
                'payer_account',
                'gateway_response',
                'expires_at',
            ]);

        });
    }
};
