<?php

namespace App\Http\Controllers\Dispatch;

use App\Http\Controllers\Controller;
use App\Models\DispatchDay;
use App\Models\DispatchEntry;
use App\Models\TripCode;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DispatchEntryController extends Controller
{
    public function store(Request $request, DispatchDay $dispatchDay): RedirectResponse
    {
        $validated = $request->validate([
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'trip_code_id' => 'nullable|exists:trip_codes,id',
            'driver_id' => 'nullable|exists:drivers,id',
            'brand' => 'nullable|string|max:100',
            'bus_number' => 'nullable|string|max:20',
            'route' => 'nullable|string|max:200',
            'bus_type' => 'nullable|string',
            'departure_terminal' => 'nullable|string|max:100',
            'arrival_terminal' => 'nullable|string|max:100',
            'scheduled_departure' => 'nullable|date_format:H:i',
            'actual_departure' => 'nullable|date_format:H:i',
            'direction' => 'nullable|string|in:SB,NB',
            'status' => 'nullable|string|in:scheduled,departed,on_route,delayed,cancelled,arrived',
            'remarks' => 'nullable|string|max:1000',
        ]);

        $maxOrder = $dispatchDay->entries()->max('sort_order') ?? -1;
        $validated['dispatch_day_id'] = $dispatchDay->id;
        $validated['sort_order'] = $maxOrder + 1;

        DispatchEntry::create($validated);

        return redirect()->back();
    }

    public function update(Request $request, DispatchDay $dispatchDay, DispatchEntry $entry): RedirectResponse
    {
        $validated = $request->validate([
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'trip_code_id' => 'nullable|exists:trip_codes,id',
            'driver_id' => 'nullable|exists:drivers,id',
            'brand' => 'nullable|string|max:100',
            'bus_number' => 'nullable|string|max:20',
            'route' => 'nullable|string|max:200',
            'bus_type' => 'nullable|string',
            'departure_terminal' => 'nullable|string|max:100',
            'arrival_terminal' => 'nullable|string|max:100',
            'scheduled_departure' => 'nullable|date_format:H:i',
            'actual_departure' => 'nullable|date_format:H:i',
            'direction' => 'nullable|string|in:SB,NB',
            'status' => 'nullable|string|in:scheduled,departed,on_route,delayed,cancelled,arrived',
            'remarks' => 'nullable|string|max:1000',
        ]);

        $entry->update($validated);

        return redirect()->back();
    }

    public function destroy(DispatchDay $dispatchDay, DispatchEntry $entry): RedirectResponse
    {
        $entry->delete();

        return redirect()->back();
    }

    public function updateStatus(Request $request, DispatchDay $dispatchDay, DispatchEntry $entry): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|string|in:scheduled,departed,on_route,delayed,cancelled,arrived',
        ]);

        $entry->update($validated);

        return redirect()->back();
    }

    public function autofill(TripCode $tripCode): JsonResponse
    {
        return response()->json([
            'trip_code_id' => $tripCode->id,
            'route' => $tripCode->route_display,
            'bus_type' => $tripCode->bus_type->value,
            'departure_terminal' => $tripCode->origin_terminal,
            'arrival_terminal' => $tripCode->destination_terminal,
            'scheduled_departure' => $tripCode->scheduled_departure_time,
            'direction' => $tripCode->direction->value,
        ]);
    }
}
