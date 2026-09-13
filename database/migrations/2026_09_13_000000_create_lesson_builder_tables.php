<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lesson_objectives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained()->cascadeOnDelete();
            $table->text('objective');
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });

        Schema::create('lesson_content_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->string('title')->nullable();
            $table->longText('content')->nullable();
            $table->string('media_path')->nullable();
            $table->string('media_url')->nullable();
            $table->string('language')->nullable();
            $table->json('metadata')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });

        Schema::create('lesson_takeaways', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained()->cascadeOnDelete();
            $table->text('takeaway');
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });

        Schema::create('lesson_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('passing_score')->nullable();
            $table->unsignedInteger('attempts')->nullable();
            $table->boolean('is_required')->default(false);
            $table->timestamps();
        });

        Schema::create('lesson_assessment_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained('lesson_assessments')->cascadeOnDelete();
            $table->text('question');
            $table->string('type')->default('single_choice');
            $table->json('options')->nullable();
            $table->json('correct_options')->nullable();
            $table->text('explanation')->nullable();
            $table->unsignedInteger('points')->default(1);
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });

        Schema::create('lesson_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->longText('instructions');
            $table->string('submission_type')->default('file');
            $table->unsignedInteger('max_score')->nullable();
            $table->boolean('is_required')->default(false);
            $table->timestamps();
        });

        Schema::create('lesson_completion_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->unique()->constrained()->cascadeOnDelete();
            $table->boolean('watch_video')->default(false);
            $table->boolean('read_content')->default(false);
            $table->boolean('pass_quiz')->default(false);
            $table->boolean('submit_assignment')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_completion_rules');
        Schema::dropIfExists('lesson_assignments');
        Schema::dropIfExists('lesson_assessment_questions');
        Schema::dropIfExists('lesson_assessments');
        Schema::dropIfExists('lesson_takeaways');
        Schema::dropIfExists('lesson_content_blocks');
        Schema::dropIfExists('lesson_objectives');
    }
};
