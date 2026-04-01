<?php

use App\Console\Commands\CheckAttendanceAlerts;
use App\Console\Commands\CheckPmsSchedule;
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

// Check PMS schedule daily at 6 AM
Schedule::command(CheckPmsSchedule::class)
    ->dailyAt('06:00')
    ->description('Check for vehicles with overdue or approaching PMS schedule');
