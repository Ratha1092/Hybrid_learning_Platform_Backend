<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDailyMetricsTable extends Migration
{
    public function up()
    {
        Schema::create('daily_metrics', function (Blueprint $table) {
            $table->id();

            $table->date('date')->unique();

            $table->integer('total_users')->default(0);
            $table->integer('new_users')->default(0);

            $table->integer('total_orders')->default(0);
            $table->decimal('total_revenue', 12, 2)->default(0);

            $table->integer('total_enrollments')->default(0);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('daily_metrics');
    }
}