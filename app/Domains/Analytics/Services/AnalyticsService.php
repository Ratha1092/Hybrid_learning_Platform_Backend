<?php

namespace App\Domains\Analytics\Services;

use App\Domains\Analytics\Models\DailyMetric;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    protected function getTodayMetric(): DailyMetric
    {
        $today = Carbon::today()->toDateString();

        return DailyMetric::firstOrCreate([
            'date' => $today
        ]);
    }

    public function recordRevenue(float $amount): void
    {
        DB::transaction(function () use ($amount) {

            $metric = $this->getTodayMetric();

            $metric->increment('total_revenue', $amount);
            $metric->increment('total_orders');

        });
    }

    public function recordEnrollment(): void
    {
        DB::transaction(function () {

            $metric = $this->getTodayMetric();

            $metric->increment('total_enrollments');

        });
    }

    public function recordNewUser(): void
    {
        DB::transaction(function () {

            $metric = $this->getTodayMetric();

            $metric->increment('new_users');
            $metric->increment('total_users');

        });
    }
}