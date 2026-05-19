<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            $table->string('payment_method')
                ->nullable()
                ->after('payment_status');

            $table->index('status');
            $table->index('payment_status');
            $table->index('created_at');

        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            $table->dropIndex(['status']);
            $table->dropIndex(['payment_status']);
            $table->dropIndex(['created_at']);

            $table->dropColumn('payment_method');

        });
    }
};
