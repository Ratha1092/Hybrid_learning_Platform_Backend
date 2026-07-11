<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('payments:verify-pending')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();

Schedule::command('reports:run-scheduled')
    ->hourly()
    ->withoutOverlapping();

Schedule::command('horizon:snapshot')->everyFiveMinutes();

Schedule::command('cache:prune-stale-tags')->hourly();

Schedule::command('wallet:release-pending-balance')
    ->daily()
    ->withoutOverlapping();

Schedule::command('payouts:generate-monthly')
    ->lastDayOfMonth('23:59')
    ->withoutOverlapping();

Schedule::command('app:prune-retained-data')
    ->daily()
    ->withoutOverlapping();
