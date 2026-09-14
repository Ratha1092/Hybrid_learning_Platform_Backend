<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Quiz is being retired as a lesson type — any lessons still using it
        // fall back to a plain article rather than being left in an invalid state.
        DB::table('lessons')->where('type', 'quiz')->update(['type' => 'article']);

        Schema::table('lessons', function (Blueprint $table) {
            $table->dropColumn('quiz_data');
        });

        DB::statement('ALTER TABLE lessons DROP CONSTRAINT IF EXISTS lessons_type_check');
        DB::statement("ALTER TABLE lessons ADD CONSTRAINT lessons_type_check CHECK (type IN ('video','article','file','live','assignment'))");
    }

    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->json('quiz_data')->nullable();
        });

        DB::statement('ALTER TABLE lessons DROP CONSTRAINT IF EXISTS lessons_type_check');
        DB::statement("ALTER TABLE lessons ADD CONSTRAINT lessons_type_check CHECK (type IN ('video','article','quiz','file','live','assignment'))");
    }
};
