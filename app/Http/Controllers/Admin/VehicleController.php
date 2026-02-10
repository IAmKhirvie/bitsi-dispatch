<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BusType;
use App\Enums\PmsUnit;
use App\Enums\VehicleStatus;
use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\View\View;

class VehicleController extends Controller
{
    public function index(): View
    {
        return view('admin.vehicles.index');
    }

    public function create(): View
    {
        return view('admin.vehicles.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(array_merge($this->validationRules(), [
            'bus_number' => 'required|string|min:2|max:20|unique:vehicles,bus_number',
            'plate_number' => ['required', 'string', 'min:4', 'max:20', 'regex:/^[A-Z0-9\- ]+$/i', 'unique:vehicles,plate_number'],
        ]));

        Vehicle::create($validated);

        return redirect()->route('admin.vehicles.index');
    }

    public function edit(Vehicle $vehicle): View
    {
        return view('admin.vehicles.edit', [
            'vehicle' => array_merge($vehicle->toArray(), [
                'is_pms_warning' => $vehicle->is_pms_warning,
                'pms_percentage' => $vehicle->pms_percentage,
            ]),
        ]);
    }

    public function update(Request $request, Vehicle $vehicle): RedirectResponse
    {
        $validated = $request->validate(array_merge($this->validationRules(), [
            'bus_number' => ['required', 'string', 'min:2', 'max:20', Rule::unique('vehicles', 'bus_number')->ignore($vehicle->id)],
            'plate_number' => ['required', 'string', 'min:4', 'max:20', 'regex:/^[A-Z0-9\- ]+$/i', Rule::unique('vehicles', 'plate_number')->ignore($vehicle->id)],
        ]));

        $vehicle->update($validated);

        return redirect()->route('admin.vehicles.index');
    }

    public function destroy(Vehicle $vehicle): RedirectResponse
    {
        $vehicle->delete();

        return redirect()->route('admin.vehicles.index');
    }

    private function validationRules(): array
    {
        return [
            'brand' => 'required|string|min:2|max:100',
            'bus_type' => ['required', 'string', new Enum(BusType::class)],
            'status' => ['required', 'string', new Enum(VehicleStatus::class)],
            'gps_device_id' => 'nullable|string|max:100',
            'pms_unit' => ['required', 'string', new Enum(PmsUnit::class)],
            'pms_threshold' => 'required|integer|min:0',
            'current_pms_value' => 'required|integer|min:0',
            'last_pms_date' => 'nullable|date',
        ];
    }
}
