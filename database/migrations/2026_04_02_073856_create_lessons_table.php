<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('title');
            $table->text('content')->nullable();
            $table->enum('type', ['video', 'article', 'quiz'])
                ->default('video');
            $table->boolean('is_preview')->default(false);
            $table->string('video_url')->nullable(); // youtube
            $table->string('video_path')->nullable(); // uploaded file
            $table->json('quiz_data')->nullable();
            $table->integer('duration')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};