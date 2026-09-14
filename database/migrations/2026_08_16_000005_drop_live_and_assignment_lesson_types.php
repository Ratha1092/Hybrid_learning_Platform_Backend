<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Live and Assignment were never backed by real functionality (no streaming
        // integration, no submission/grading model) — retire them like quiz, falling
        // back to article rather than leaving lessons in an invalid state.
        DB::table('lessons')->whereIn('type', ['live', 'assignment'])->update(['type' => 'article']);

        // Duration only applies to video lessons; clear stray values left on articles.
        DB::table('lessons')->where('type', 'article')->update(['duration' => null]);

        DB::statement('ALTER TABLE lessons DROP CONSTRAINT IF EXISTS lessons_type_check');
        DB::statement("ALTER TABLE lessons ADD CONSTRAINT lessons_type_check CHECK (type IN ('video','article','file'))");
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE lessons DROP CONSTRAINT IF EXISTS lessons_type_check');
        DB::statement("ALTER TABLE lessons ADD CONSTRAINT lessons_type_check CHECK (type IN ('video','article','file','live','assignment'))");
    }
};
