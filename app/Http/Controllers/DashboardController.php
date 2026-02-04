<?php

namespace App\Http\Controllers;

use App\Models\DispatchDay;
use App\Models\DispatchEntry;
use App\Models\Driver;
use App\Models\TripCode;
use App\Models\Vehicle;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        $today = today();

        $todayDispatch = DispatchDay::with('summary')
            ->whereDate('service_date', $today)
            ->first();

        $stats = [
            'today_trips' => $todayDispatch?->entries()->count() ?? 0,
            'departed' => $todayDispatch?->entries()->where('status', 'departed')->count() ?? 0,
            'on_route' => $todayDispatch?->entries()->where('status', 'on_route')->count() ?? 0,
            'cancelled' => $todayDispatch?->entries()->where('status', 'cancelled')->count() ?? 0,
            'active_vehicles' => Vehicle::where('status', 'OK')->count(),
            'under_repair' => Vehicle::where('status', 'UR')->count(),
            'pms_warning' => Vehicle::whereColumn('current_pms_value', '>=', 'pms_threshold')->count(),
            'active_drivers' => Driver::where('is_active', true)->count(),
        ];

        $recentEntries = DispatchEntry::with(['dispatchDay', 'vehicle', 'tripCode', 'driver', 'driver2'])
            ->latest()
            ->take(10)
            ->get();

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'todaySummary' => $todayDispatch?->summary,
            'recentEntries' => $recentEntries,
        ]);
    }
}
