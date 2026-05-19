<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('short_description')->nullable();
            $table->text('description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->string('level')->default('beginner');
            $table->string('language')->default('English');
            $table->integer('duration')->nullable();
            $table->text('requirements')->nullable();
            $table->text('what_you_will_learn')->nullable();
            $table->string('status')->default('draft')->index();
            $table->boolean('is_published')->default(false);
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('commission_percentage', 5, 2)->default(20);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};