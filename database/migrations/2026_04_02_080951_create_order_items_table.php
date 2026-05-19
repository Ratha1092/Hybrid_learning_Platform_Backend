<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {

            $table->id();
            $table->foreignId('order_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('course_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('instructor_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->string('course_title');
            $table->decimal('price', 12, 2);
            $table->decimal('discount_amount', 12, 2)
                ->default(0);
            $table->decimal('final_amount', 12, 2);
            $table->decimal('commission_percentage', 5, 2)
                ->default(20);
            $table->decimal('instructor_amount', 12, 2)
                ->default(0);
            $table->decimal('platform_amount', 12, 2)
                ->default(0);
            $table->boolean('is_refunded')
                ->default(false);
            $table->timestamp('refunded_at')
                ->nullable();
            $table->timestamps();
            $table->unique([
                'order_id',
                'course_id',
            ]);

            // Performance
            $table->index('instructor_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};