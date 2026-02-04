<?php

namespace App\Http\Controllers\Dispatch;

use App\Http\Controllers\Controller;
use App\Models\DispatchDay;
use App\Models\Driver;
use App\Models\TripCode;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DispatchDayController extends Controller
{
    public function index(Request $request): Response
    {
        $date = $request->input('date', today()->toDateString());

        $dispatchDay = DispatchDay::with([
            'entries.vehicle',
            'entries.tripCode',
            'entries.driver',
            'entries.driver2',
            'summary',
            'creator',
        ])
            ->whereDate('service_date', $date)
            ->first();

        return Inertia::render('dispatch/Index', [
            'dispatchDay' => $dispatchDay,
            'date' => $date,
            'tripCodes' => TripCode::where('is_active', true)->orderBy('code')->get(),
            'vehicles' => Vehicle::where('status', 'OK')->orderBy('bus_number')->get(),
            'drivers' => Driver::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'service_date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $date = $validated['service_date'];

        $existing = DispatchDay::whereDate('service_date', $date)->first();

        if (! $existing) {
            DispatchDay::create([
                'service_date' => $date,
                'created_by' => $request->user()->id,
                'notes' => $validated['notes'] ?? null,
            ]);
        }

        return redirect()->route('dispatch.index', ['date' => $date]);
    }
}
