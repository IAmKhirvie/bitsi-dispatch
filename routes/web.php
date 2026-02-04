<?php

use App\Http\Controllers\Admin\DriverController;
use App\Http\Controllers\Admin\TripCodeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VehicleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Dispatch\DispatchDayController;
use App\Http\Controllers\Dispatch\DispatchEntryController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\Report\ReportController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
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

    // Tracking
    Route::get('tracking', fn () => Inertia::render('tracking/Index'))->name('tracking.index');

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/daily/{date}', [ReportController::class, 'daily'])->name('daily');
        Route::get('/export/excel/{date}', [ReportController::class, 'exportExcel'])->name('export-excel');
        Route::get('/export/pdf/{date}', [ReportController::class, 'exportPdf'])->name('export-pdf');
    });

    // History
    Route::get('history', [HistoryController::class, 'index'])->name('history.index');

    // Vehicle positions API
    Route::get('api/positions/latest', [\App\Http\Controllers\Api\PositionController::class, 'latest'])->name('api.positions.latest');

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
    });
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
