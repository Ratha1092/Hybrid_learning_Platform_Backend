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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            // 🔗 RELATION
            $table->foreignId('order_id')
                ->constrained()
                ->cascadeOnDelete();

            // 💳 PAYMENT INFO
            $table->string('payment_gateway'); // stripe | khqr | paypal
            $table->string('transaction_id')->nullable()->unique(); // external payment ID
            $table->string('external_reference')->nullable(); // gateway reference

            // 🔒 IDEMPOTENCY (prevent duplicate processing)
            $table->string('idempotency_key')->nullable()->unique();

            // 💰 MONEY
            $table->decimal('amount', 12, 2);
            $table->string('currency', 10)->default('USD');

            // 📊 STATUS
            $table->string('status')
                ->default('pending'); // pending | paid | failed | refunded

            $table->string('failure_reason')->nullable();

            // 📦 EXTRA DATA (gateway response, metadata)
            $table->json('meta')->nullable();

            // ⏱ TIMING
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();

            // ⚡ PERFORMANCE INDEXES
            $table->index(['order_id', 'status']);
            $table->index('status');
            $table->index('payment_gateway');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};