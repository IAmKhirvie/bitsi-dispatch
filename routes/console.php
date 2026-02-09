<?php

use App\Console\Commands\CheckAttendanceAlerts;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule attendance check every 5 minutes
Schedule::command(CheckAttendanceAlerts::class)
    ->everyFiveMinutes()
    ->description('Check for attendance issues and create alerts');
