<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lesson_comments', function (Blueprint $table) {
            // Which specific video part (for a lesson split into several
            // videos) the timestamp belongs to. Null for a single-video
            // lesson, where there's only one part to begin with. Set null
            // (not cascade-deleted) if that part is later removed — the
            // comment thread itself is still worth keeping.
            $table->foreignId('video_id')
                ->nullable()
                ->after('video_timestamp')
                ->constrained('lesson_videos')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('lesson_comments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('video_id');
        });
    }
};
