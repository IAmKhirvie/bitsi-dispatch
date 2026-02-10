<?php

namespace App\Http\Controllers;

use App\Models\DispatchDay;
use App\Models\DispatchEntry;
use App\Models\Driver;
use App\Models\TripCode;
use App\Models\Vehicle;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $today = today();

        $todayDispatch = DispatchDay::with('summary')
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

        $vehicleStats = Vehicle::selectRaw("
            SUM(CASE WHEN status = 'OK' THEN 1 ELSE 0 END) as active,
            SUM(CASE WHEN status = 'UR' THEN 1 ELSE 0 END) as under_repair,
            SUM(CASE WHEN current_pms_value >= pms_threshold THEN 1 ELSE 0 END) as pms_warning
        ")->first();

        $stats = [
            'today_trips' => (int) ($tripStats->total ?? 0),
            'departed' => (int) ($tripStats->departed ?? 0),
            'on_route' => (int) ($tripStats->on_route ?? 0),
            'cancelled' => (int) ($tripStats->cancelled ?? 0),
            'active_vehicles' => (int) ($vehicleStats->active ?? 0),
            'under_repair' => (int) ($vehicleStats->under_repair ?? 0),
            'pms_warning' => (int) ($vehicleStats->pms_warning ?? 0),
            'active_drivers' => Driver::where('is_active', true)->count(),
        ];

        $recentEntries = DispatchEntry::with(['dispatchDay', 'vehicle', 'tripCode', 'driver', 'driver2'])
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard', [
            'stats' => $stats,
            'todaySummary' => $todayDispatch?->summary,
            'recentEntries' => $recentEntries,
        ]);
    }
}
