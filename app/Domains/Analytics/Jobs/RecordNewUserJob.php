<?php

namespace App\Domains\Analytics\Jobs;

use App\Domains\Analytics\Services\AnalyticsService;
use Illuminate\Contracts\Queue\ShouldQueue;

class RecordNewUserJob implements ShouldQueue
{
    public function handle(): void
    {
        $analytics = app(AnalyticsService::class);
        $analytics->recordNewUser();
    }
}