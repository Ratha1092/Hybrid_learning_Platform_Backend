<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {

            $table->string('preview_video_url')
                ->nullable()
                ->after('thumbnail');

            $table->string('visibility')
                ->default('public')
                ->after('status');

            $table->boolean('certificate_enabled')
                ->default(false)
                ->after('visibility');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {

            $table->dropColumn([
                'preview_video_url',
                'visibility',
                'certificate_enabled',
            ]);
        });
    }
};