<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('instructor_payout_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('method');
            $table->string('account_name');
            $table->string('account_number')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('qr_code_path')->nullable();
            $table->string('status')->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instructor_payout_accounts');
    }
};
