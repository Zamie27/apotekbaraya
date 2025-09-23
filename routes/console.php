<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled Commands
|--------------------------------------------------------------------------
|
| Here you may define all of your scheduled commands. These commands will
| be executed automatically based on the schedule you define.
|
*/

// Cancel expired orders every hour
Schedule::command('orders:cancel-expired')
    ->hourly()
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/cancel-expired-orders.log'));

// Cancel expired orders daily at 2 AM (alternative schedule)
// Schedule::command('orders:cancel-expired')->dailyAt('02:00');

// Example of other scheduling commands:
// Schedule::command('addresses:geocode')->daily();
