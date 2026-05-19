<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('type'); // credit | debit
            $table->string('status')->default('completed');
            $table->foreignId('revenue_share_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('payout_request_id')->nullable()->constrained()->nullOnDelete();
            $table->string('reference_id')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};