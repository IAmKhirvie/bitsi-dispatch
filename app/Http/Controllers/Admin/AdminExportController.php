<?php

namespace App\Http\Controllers\Admin;

use App\Exports\AttendanceExport;
use App\Exports\AuditLogsExport;
use App\Exports\DriversExport;
use App\Exports\SmsLogsExport;
use App\Exports\TripCodesExport;
use App\Exports\UsersExport;
use App\Exports\VehiclesExport;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AdminExportController extends Controller
{
    /**
     * Resolve date range based on period type.
     */
    private function resolveDateRange(string $period): array
    {
        $now = CarbonImmutable::now();

        return match ($period) {
            'daily' => [
                'date_from' => $now->toDateString(),
                'date_to' => $now->toDateString(),
                'label' => $now->format('Y-m-d'),
            ],
            'weekly' => [
                'date_from' => $now->startOfWeek(Carbon::MONDAY)->toDateString(),
                'date_to' => $now->endOfWeek(Carbon::SUNDAY)->toDateString(),
                'label' => $now->startOfWeek(Carbon::MONDAY)->format('Y-m-d') . '-to-' . $now->endOfWeek(Carbon::SUNDAY)->format('Y-m-d'),
            ],
            'monthly' => [
                'date_from' => $now->startOfMonth()->toDateString(),
                'date_to' => $now->endOfMonth()->toDateString(),
                'label' => $now->format('Y-m'),
            ],
            default => [
                'date_from' => null,
                'date_to' => null,
                'label' => 'all',
            ],
        };
    }

    /**
     * Resolve date range from custom query parameters.
     */
    private function resolveCustomDateRange(Request $request): array
    {
        return [
            'date_from' => $request->query('date_from'),
            'date_to' => $request->query('date_to'),
            'label' => $request->query('date_from') . '-to-' . $request->query('date_to'),
        ];
    }

    // ─── Users ────────────────────────────────────────────────────────

    public function exportUsers(string $period)
    {
        $range = $this->resolveDateRange($period);

        return Excel::download(
            new UsersExport($range['date_from'], $range['date_to']),
            "users-{$range['label']}.xlsx"
        );
    }

    public function exportUsersCustom(Request $request)
    {
        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
        ]);

        $range = $this->resolveCustomDateRange($request);

        return Excel::download(
            new UsersExport($range['date_from'], $range['date_to']),
            "users-{$range['label']}.xlsx"
        );
    }

    // ─── Drivers ──────────────────────────────────────────────────────

    public function exportDrivers(string $period)
    {
        $range = $this->resolveDateRange($period);

        return Excel::download(
            new DriversExport($range['date_from'], $range['date_to']),
            "drivers-{$range['label']}.xlsx"
        );
    }

    public function exportDriversCustom(Request $request)
    {
        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
        ]);

        $range = $this->resolveCustomDateRange($request);

        return Excel::download(
            new DriversExport($range['date_from'], $range['date_to']),
            "drivers-{$range['label']}.xlsx"
        );
    }

    // ─── Vehicles ─────────────────────────────────────────────────────

    public function exportVehicles(string $period)
    {
        $range = $this->resolveDateRange($period);

        return Excel::download(
            new VehiclesExport($range['date_from'], $range['date_to']),
            "vehicles-{$range['label']}.xlsx"
        );
    }

    public function exportVehiclesCustom(Request $request)
    {
        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
        ]);

        $range = $this->resolveCustomDateRange($request);

        return Excel::download(
            new VehiclesExport($range['date_from'], $range['date_to']),
            "vehicles-{$range['label']}.xlsx"
        );
    }

    // ─── Trip Codes ───────────────────────────────────────────────────

    public function exportTripCodes(string $period)
    {
        $range = $this->resolveDateRange($period);

        return Excel::download(
            new TripCodesExport($range['date_from'], $range['date_to']),
            "trip-codes-{$range['label']}.xlsx"
        );
    }

    public function exportTripCodesCustom(Request $request)
    {
        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
        ]);

        $range = $this->resolveCustomDateRange($request);

        return Excel::download(
            new TripCodesExport($range['date_from'], $range['date_to']),
            "trip-codes-{$range['label']}.xlsx"
        );
    }

    // ─── Attendance ───────────────────────────────────────────────────

    public function exportAttendance(string $period)
    {
        $range = $this->resolveDateRange($period);

        return Excel::download(
            new AttendanceExport($range['date_from'], $range['date_to']),
            "attendance-{$range['label']}.xlsx"
        );
    }

    public function exportAttendanceCustom(Request $request)
    {
        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
        ]);

        $range = $this->resolveCustomDateRange($request);

        return Excel::download(
            new AttendanceExport($range['date_from'], $range['date_to']),
            "attendance-{$range['label']}.xlsx"
        );
    }

    // ─── Audit Logs ───────────────────────────────────────────────────

    public function exportAuditLogs(string $period)
    {
        $range = $this->resolveDateRange($period);

        return Excel::download(
            new AuditLogsExport($range['date_from'], $range['date_to']),
            "audit-logs-{$range['label']}.xlsx"
        );
    }

    public function exportAuditLogsCustom(Request $request)
    {
        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
        ]);

        $range = $this->resolveCustomDateRange($request);

        return Excel::download(
            new AuditLogsExport($range['date_from'], $range['date_to']),
            "audit-logs-{$range['label']}.xlsx"
        );
    }

    // ─── SMS Logs ─────────────────────────────────────────────────────

    public function exportSmsLogs(string $period)
    {
        $range = $this->resolveDateRange($period);

        return Excel::download(
            new SmsLogsExport($range['date_from'], $range['date_to']),
            "sms-logs-{$range['label']}.xlsx"
        );
    }

    public function exportSmsLogsCustom(Request $request)
    {
        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
        ]);

        $range = $this->resolveCustomDateRange($request);

        return Excel::download(
            new SmsLogsExport($range['date_from'], $range['date_to']),
            "sms-logs-{$range['label']}.xlsx"
        );
    }
}