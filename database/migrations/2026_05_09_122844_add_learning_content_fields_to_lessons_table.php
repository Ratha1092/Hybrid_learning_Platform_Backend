<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            if (! Schema::hasColumn('lessons', 'video_provider')) {
                $table->string('video_provider')->nullable()->after('video_path');
            }
            if (! Schema::hasColumn('lessons', 'attachment')) {
                $table->string('attachment')->nullable()->after('video_provider');
            }
            if (! Schema::hasColumn('lessons', 'attachment_name')) {
                $table->string('attachment_name')->nullable()->after('attachment');
            }
        });
    }

    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropColumn([
                'video_provider',
                'attachment',
                'attachment_name',
            ]);
        });
    }
};