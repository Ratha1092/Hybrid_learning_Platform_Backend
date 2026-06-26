<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_sequences', function (Blueprint $table) {
            $table->string('type')->primary();
            $table->unsignedInteger('last_number')->default(0);
        });

        DB::table('document_sequences')->insert([
            ['type' => 'invoice',     'last_number' => 0],
            ['type' => 'receipt',     'last_number' => 0],
            ['type' => 'credit_note', 'last_number' => 0],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('document_sequences');
    }
};
