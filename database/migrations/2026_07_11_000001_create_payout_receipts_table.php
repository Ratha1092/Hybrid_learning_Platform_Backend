<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payout_receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payout_request_id')->unique()->constrained('payout_requests')->cascadeOnDelete();
            $table->string('receipt_number')->unique();
            $table->decimal('amount', 12, 2);
            $table->string('currency')->default('USD');
            $table->string('payment_method');
            $table->timestamp('paid_at');
            $table->string('pdf_path')->nullable();
            $table->timestamps();
        });

        DB::table('document_sequences')->insertOrIgnore([
            ['type' => 'payout_receipt', 'last_number' => 0],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('payout_receipts');
        DB::table('document_sequences')->where('type', 'payout_receipt')->delete();
    }
};
