<?php

namespace App\Domains\Analytics\Jobs;

use App\Domains\Analytics\Services\AnalyticsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RecordRevenueJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public float $amount) {}

    public function tags(): array
    {
        return ['analytics', 'revenue'];
    }

    public function handle(): void
    {
        $analytics = app(AnalyticsService::class);
        $analytics->recordRevenue($this->amount);
    }
}
