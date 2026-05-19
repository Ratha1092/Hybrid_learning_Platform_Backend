<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

use App\Domains\Payments\Events\PaymentSuccessEvent;
use App\Domains\Learning\Listeners\EnrollStudentListener;
use App\Domains\Finance\Listeners\ProcessRevenueListener;
use App\Domains\Finance\Listeners\UpdateInstructorWalletListener;
use App\Domains\Finance\Listeners\RecordWalletTransactionListener;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        PaymentSuccessEvent::class => [
            \App\Domains\Learning\Listeners\EnrollStudentListener::class,
            \App\Domains\Finance\Listeners\ProcessRevenueListener::class,
            \App\Domains\Finance\Listeners\RecordWalletTransactionListener::class,
            \App\Domains\Finance\Listeners\UpdateInstructorWalletListener::class,
        ],
    ];
}