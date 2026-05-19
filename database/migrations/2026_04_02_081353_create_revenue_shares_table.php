<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('revenue_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('instructor_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('total_amount', 12, 2);
            $table->decimal('platform_amount', 12, 2);
            $table->decimal('instructor_amount', 12, 2);
            $table->decimal('commission_percentage', 5, 2);
            $table->string('status')->default('pending');// pending | distributed
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('revenue_shares');
    }
};