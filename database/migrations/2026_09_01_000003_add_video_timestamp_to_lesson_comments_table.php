<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lesson_comments', function (Blueprint $table) {
            // Seconds into the lesson's video the commenter was referring to,
            // so a question like "what's happening here?" stays anchored to
            // the moment it's about instead of just floating under the video.
            $table->unsignedInteger('video_timestamp')->nullable()->after('body');
        });
    }

    public function down(): void
    {
        Schema::table('lesson_comments', function (Blueprint $table) {
            $table->dropColumn('video_timestamp');
        });
    }
};
