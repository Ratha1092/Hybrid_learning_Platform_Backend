<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('certificate_enabled');
        });

        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropColumn('certificate_issued');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->boolean('certificate_enabled')->default(true);
        });

        Schema::table('enrollments', function (Blueprint $table) {
            $table->boolean('certificate_issued')->default(false);
        });
    }
};
