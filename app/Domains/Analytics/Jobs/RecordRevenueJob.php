<?php

namespace App\Domains\Analytics\Jobs;

use App\Domains\Analytics\Services\AnalyticsService;
use Illuminate\Contracts\Queue\ShouldQueue;

class RecordRevenueJob implements ShouldQueue
{
    public function __construct(public float $amount) {}

    public function handle(): void
    {
        $analytics = app(AnalyticsService::class);
        $analytics->recordRevenue($this->amount);
    }
}