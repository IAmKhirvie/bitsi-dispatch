<?php

use App\Http\Controllers\Admin\AdminExportController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\DriverController;
use App\Http\Controllers\Admin\DriverNotificationController;
use App\Http\Controllers\Admin\TripCodeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VehicleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Dispatch\DispatchDayController;
use App\Http\Controllers\Dispatch\DispatchEntryController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\Report\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    // Dispatch
    Route::get('dispatch', [DispatchDayController::class, 'index'])->name('dispatch.index');
    Route::post('dispatch', [DispatchDayController::class, 'store'])->name('dispatch.store');

    Route::prefix('dispatch/{dispatchDay}')->name('dispatch.entries.')->group(function () {
        Route::post('entries', [DispatchEntryController::class, 'store'])->name('store');
        Route::put('entries/{entry}', [DispatchEntryController::class, 'update'])->name('update');
        Route::delete('entries/{entry}', [DispatchEntryController::class, 'destroy'])->name('destroy');
        Route::patch('entries/{entry}/status', [DispatchEntryController::class, 'updateStatus'])->name('update-status');
    });

    // Trip code autofill API
    Route::get('api/trip-codes/{tripCode}/autofill', [DispatchEntryController::class, 'autofill'])->name('api.trip-codes.autofill');

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/daily/{date}', [ReportController::class, 'daily'])->name('daily');
        Route::get('/export/excel/{date}', [ReportController::class, 'exportExcel'])->name('export-excel');
        Route::get('/export/pdf/{date}', [ReportController::class, 'exportPdf'])->name('export-pdf');
        Route::get('/export/weekly-excel', [ReportController::class, 'exportWeeklyExcel'])->name('export-weekly-excel');
        Route::get('/export/monthly-excel', [ReportController::class, 'exportMonthlyExcel'])->name('export-monthly-excel');
    });

    // History
    Route::get('history', [HistoryController::class, 'index'])->name('history.index');

    // Admin routes
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', UserController::class);
        Route::patch('users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');

        Route::resource('trip-codes', TripCodeController::class);
        Route::patch('trip-codes/{tripCode}/toggle-active', [TripCodeController::class, 'toggleActive'])->name('trip-codes.toggle-active');

        Route::resource('vehicles', VehicleController::class);

        Route::resource('drivers', DriverController::class);
        Route::patch('drivers/{driver}/toggle-active', [DriverController::class, 'toggleActive'])->name('drivers.toggle-active');
        Route::patch('drivers/{driver}/update-status', [DriverController::class, 'updateStatus'])->name('drivers.update-status');
        Route::post('drivers/{driver}/send-schedule-sms', [DriverNotificationController::class, 'sendScheduleSms'])->name('drivers.send-schedule-sms');
        Route::post('drivers/{driver}/send-custom-sms', [DriverNotificationController::class, 'sendCustomSms'])->name('drivers.send-custom-sms');
        Route::get('drivers/{driver}/schedule-preview', [DriverNotificationController::class, 'getSchedulePreview'])->name('drivers.schedule-preview');

        // Audit Logs
        Route::get('audit-logs', fn () => view('admin.audit-logs.index'))->name('audit-logs.index');

        // SMS Logs
        Route::get('sms-logs', fn () => view('admin.sms-logs.index'))->name('sms-logs.index');

        // Attendance Management
        Route::prefix('attendance')->name('attendance.')->group(function () {
            Route::get('/', [AttendanceController::class, 'index'])->name('index');
            Route::get('/settings', [AttendanceController::class, 'settings'])->name('settings');
            Route::put('/settings', [AttendanceController::class, 'updateSettings'])->name('settings.update');
            Route::post('/mark-late', [AttendanceController::class, 'markLate'])->name('mark-late');
            Route::post('/mark-absent', [AttendanceController::class, 'markAbsent'])->name('mark-absent');
            Route::post('/override', [AttendanceController::class, 'override'])->name('override');
            Route::post('/initialize-today', [AttendanceController::class, 'initializeToday'])->name('initialize-today');
            Route::post('/mark-alerts-read', [AttendanceController::class, 'markAlertsRead'])->name('mark-alerts-read');
        });
        Route::get('/api/attendance/alerts', [AttendanceController::class, 'getAlerts'])->name('api.attendance.alerts');
        Route::get('/api/attendance/pending', [AttendanceController::class, 'getPendingAttendance'])->name('api.attendance.pending');

        // ─── Export Routes ────────────────────────────────────────
        Route::prefix('export')->name('export.')->group(function () {
            Route::get('users/{period}', [AdminExportController::class, 'exportUsers'])->name('users');
            Route::get('users', [AdminExportController::class, 'exportUsersCustom'])->name('users.custom');
            Route::get('drivers/{period}', [AdminExportController::class, 'exportDrivers'])->name('drivers');
            Route::get('drivers', [AdminExportController::class, 'exportDriversCustom'])->name('drivers.custom');
            Route::get('vehicles/{period}', [AdminExportController::class, 'exportVehicles'])->name('vehicles');
            Route::get('vehicles', [AdminExportController::class, 'exportVehiclesCustom'])->name('vehicles.custom');
            Route::get('trip-codes/{period}', [AdminExportController::class, 'exportTripCodes'])->name('trip-codes');
            Route::get('trip-codes', [AdminExportController::class, 'exportTripCodesCustom'])->name('trip-codes.custom');
            Route::get('attendance/{period}', [AdminExportController::class, 'exportAttendance'])->name('attendance');
            Route::get('attendance', [AdminExportController::class, 'exportAttendanceCustom'])->name('attendance.custom');
            Route::get('audit-logs/{period}', [AdminExportController::class, 'exportAuditLogs'])->name('audit-logs');
            Route::get('audit-logs', [AdminExportController::class, 'exportAuditLogsCustom'])->name('audit-logs.custom');
            Route::get('sms-logs/{period}', [AdminExportController::class, 'exportSmsLogs'])->name('sms-logs');
            Route::get('sms-logs', [AdminExportController::class, 'exportSmsLogsCustom'])->name('sms-logs.custom');
        });
    });
});

require __DIR__.'/settings.php';
