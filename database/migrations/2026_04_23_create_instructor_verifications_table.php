<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('instructor_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('bio')->nullable();
            $table->text('experience')->nullable();
            $table->string('qualification_type')->nullable();
            $table->string('institution')->nullable();
            $table->year('completion_year')->nullable();
            $table->string('certificate_file')->nullable();
            $table->string('identity_file')->nullable();
            $table->string('portfolio_url')->nullable();
            $table->string('status')->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index('user_id');
            $table->index('status');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->string('instructor_status')->default('not_instructor')->nullable()->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['instructor_status']);
        });
        
        Schema::dropIfExists('instructor_verifications');
    }
};
