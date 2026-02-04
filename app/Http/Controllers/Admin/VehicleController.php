<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class VehicleController extends Controller
{
    public function index(Request $request): Response
    {
        $vehicles = Vehicle::query()
            ->when($request->search, fn ($q, $s) => $q->where(function ($q2) use ($s) {
                $q2->where('bus_number', 'like', "%{$s}%")
                    ->orWhere('brand', 'like', "%{$s}%")
                    ->orWhere('plate_number', 'like', "%{$s}%");
            }))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->bus_type, fn ($q, $t) => $q->where('bus_type', $t))
            ->orderBy('bus_number')
            ->paginate(15)
            ->withQueryString()
            ->through(fn ($vehicle) => array_merge($vehicle->toArray(), [
                'is_pms_warning' => $vehicle->is_pms_warning,
                'pms_percentage' => $vehicle->pms_percentage,
            ]));

        return Inertia::render('admin/Vehicles/Index', [
            'vehicles' => $vehicles,
            'filters' => $request->only(['search', 'status', 'bus_type']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/Vehicles/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'bus_number' => 'required|string|max:20|unique:vehicles,bus_number',
            'brand' => 'required|string|max:100',
            'bus_type' => 'required|string|in:regular,deluxe,super_deluxe,elite,sleeper,single_seater,skybus',
            'plate_number' => 'required|string|max:20|unique:vehicles,plate_number',
            'status' => 'required|string|in:OK,UR,PMS,In Transit,Lutaw',
            'gps_device_id' => 'nullable|string|max:100',
            'pms_unit' => 'required|string|in:kilometers,trips',
            'pms_threshold' => 'required|integer|min:0',
            'current_pms_value' => 'required|integer|min:0',
            'last_pms_date' => 'nullable|date',
        ]);

        Vehicle::create($validated);

        return redirect()->route('admin.vehicles.index');
    }

    public function edit(Vehicle $vehicle): Response
    {
        return Inertia::render('admin/Vehicles/Edit', [
            'vehicle' => array_merge($vehicle->toArray(), [
                'is_pms_warning' => $vehicle->is_pms_warning,
                'pms_percentage' => $vehicle->pms_percentage,
            ]),
        ]);
    }

    public function update(Request $request, Vehicle $vehicle): RedirectResponse
    {
        $validated = $request->validate([
            'bus_number' => ['required', 'string', 'max:20', Rule::unique('vehicles', 'bus_number')->ignore($vehicle->id)],
            'brand' => 'required|string|max:100',
            'bus_type' => 'required|string|in:regular,deluxe,super_deluxe,elite,sleeper,single_seater,skybus',
            'plate_number' => ['required', 'string', 'max:20', Rule::unique('vehicles', 'plate_number')->ignore($vehicle->id)],
            'status' => 'required|string|in:OK,UR,PMS,In Transit,Lutaw',
            'gps_device_id' => 'nullable|string|max:100',
            'pms_unit' => 'required|string|in:kilometers,trips',
            'pms_threshold' => 'required|integer|min:0',
            'current_pms_value' => 'required|integer|min:0',
            'last_pms_date' => 'nullable|date',
        ]);

        $vehicle->update($validated);

        return redirect()->route('admin.vehicles.index');
    }

    public function destroy(Vehicle $vehicle): RedirectResponse
    {
        $vehicle->delete();

        return redirect()->route('admin.vehicles.index');
    }
}
