<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\DispatchDay;
use App\Models\DispatchEntry;
use App\Models\Driver;
use App\Models\SmsLog;
use App\Models\TripCode;
use App\Models\Vehicle;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $today = today();
        $user = auth()->user();

        $todayDispatch = DispatchDay::with('summary.items')
            ->whereDate('service_date', $today)
            ->first();

        $tripStats = $todayDispatch
            ? $todayDispatch->entries()
                ->selectRaw("
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'departed' THEN 1 ELSE 0 END) as departed,
                    SUM(CASE WHEN status = 'on_route' THEN 1 ELSE 0 END) as on_route,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
                ")
                ->first()
            : null;

        $stats = [
            'today_trips' => (int) ($tripStats->total ?? 0),
            'departed' => (int) ($tripStats->departed ?? 0),
            'on_route' => (int) ($tripStats->on_route ?? 0),
            'cancelled' => (int) ($tripStats->cancelled ?? 0),
        ];

        // Vehicle stats — for admin and operations_manager
        $vehicleStats = null;
        if ($user->hasRole(['admin', 'operations_manager'])) {
            $vehicleStats = Vehicle::selectRaw("
                SUM(CASE WHEN status = 'OK' THEN 1 ELSE 0 END) as active,
                SUM(CASE WHEN status = 'UR' THEN 1 ELSE 0 END) as under_repair,
                SUM(CASE WHEN current_pms_value >= pms_threshold AND pms_threshold > 0 THEN 1 ELSE 0 END) as pms_warning
            ")->first();

            $stats['active_vehicles'] = (int) ($vehicleStats->active ?? 0);
            $stats['under_repair'] = (int) ($vehicleStats->under_repair ?? 0);
            $stats['pms_warning'] = (int) ($vehicleStats->pms_warning ?? 0);
            $stats['active_drivers'] = Driver::where('is_active', true)->count();
        }

        // PMS alerts — vehicles at >= 80% threshold
        $pmsAlerts = collect();
        if ($user->hasRole(['admin', 'operations_manager'])) {
            $pmsAlerts = Vehicle::pmsAlert()
                ->orderByRaw('current_pms_value / pms_threshold DESC')
                ->take(5)
                ->get();
        }

        // Recent entries
        $recentEntries = DispatchEntry::with(['dispatchDay', 'vehicle', 'tripCode', 'driver', 'driver2'])
            ->latest()
            ->take(10)
            ->get();

        // Admin-only data
        $recentAuditLogs = collect();
        $smsStats = null;
        if ($user->hasRole('admin')) {
            $recentAuditLogs = AuditLog::with('user')
                ->latest()
                ->take(5)
                ->get();

            $smsStats = [
                'sent_today' => SmsLog::where('status', 'sent')->whereDate('created_at', $today)->count(),
                'failed_today' => SmsLog::where('status', 'failed')->whereDate('created_at', $today)->count(),
            ];
        }

        return view('dashboard', [
            'stats' => $stats,
            'todaySummary' => $todayDispatch?->summary,
            'recentEntries' => $recentEntries,
            'pmsAlerts' => $pmsAlerts,
            'recentAuditLogs' => $recentAuditLogs,
            'smsStats' => $smsStats,
        ]);
    }
}
