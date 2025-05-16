<?php

use App\Console\Commands\CheckPaymentStatus;
use App\Console\Commands\CheckTripStatuses;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command(CheckTripStatuses::class)->everyMinute();
Schedule::command(CheckPaymentStatus::class)->everyMinute();