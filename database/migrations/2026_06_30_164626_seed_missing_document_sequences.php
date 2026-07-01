<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('document_sequences')->insertOrIgnore([
            ['type' => 'invoice',     'last_number' => 0],
            ['type' => 'receipt',     'last_number' => 0],
            ['type' => 'credit_note', 'last_number' => 0],
        ]);
    }

    public function down(): void
    {
        //
    }
};
