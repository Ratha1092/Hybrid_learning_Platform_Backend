<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payout_requests', function (Blueprint $table) {
            $table->foreignId('payout_account_id')->nullable()->after('instructor_id')
                ->constrained('instructor_payout_accounts')->nullOnDelete();
            $table->string('source')->default('manual')->after('payment_method');
        });
    }

    public function down(): void
    {
        Schema::table('payout_requests', function (Blueprint $table) {
            $table->dropConstrainedForeignId('payout_account_id');
            $table->dropColumn('source');
        });
    }
};
