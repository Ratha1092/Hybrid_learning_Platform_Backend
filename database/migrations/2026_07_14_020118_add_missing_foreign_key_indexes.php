<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->index('course_id');
        });

        Schema::table('sections', function (Blueprint $table) {
            $table->index('course_id');
        });

        Schema::table('lessons', function (Blueprint $table) {
            $table->index('section_id');
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->index('instructor_id');
            $table->index('category_id');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->index('course_id');
        });

        Schema::table('wishlists', function (Blueprint $table) {
            $table->index('course_id');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->index('course_id');
        });
    }

    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropIndex(['course_id']);
        });

        Schema::table('sections', function (Blueprint $table) {
            $table->dropIndex(['course_id']);
        });

        Schema::table('lessons', function (Blueprint $table) {
            $table->dropIndex(['section_id']);
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->dropIndex(['instructor_id']);
            $table->dropIndex(['category_id']);
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropIndex(['course_id']);
        });

        Schema::table('wishlists', function (Blueprint $table) {
            $table->dropIndex(['course_id']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex(['course_id']);
        });
    }
};
