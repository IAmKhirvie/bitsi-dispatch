<?php

use App\Http\Controllers\Api\DriverAttendanceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes for Mobile App
|--------------------------------------------------------------------------
|
| These routes are for future mobile app integration.
| Drivers can use these endpoints to check in/out and view their schedule.
|
*/

Route::prefix('v1')->group(function () {
    // Driver Attendance Endpoints
    Route::post('/driver/check-in', [DriverAttendanceController::class, 'checkIn']);
    Route::post('/driver/check-out', [DriverAttendanceController::class, 'checkOut']);
    Route::get('/driver/my-schedule', [DriverAttendanceController::class, 'mySchedule']);
    Route::get('/driver/my-attendance', [DriverAttendanceController::class, 'myAttendance']);
});
