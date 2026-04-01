<?php

namespace App\Http\Controllers\Admin;

use App\Enums\DriverStatus;
use App\Http\Controllers\Controller;
use App\Models\Driver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;
use Illuminate\View\View;

class DriverController extends Controller
{
    public function index(): View
    {
        return view('admin.drivers.index');
    }

    public function create(): View
    {
        return view('admin.drivers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->validationRules());

        Driver::create($validated);

        return redirect()->route('admin.drivers.index');
    }

    public function edit(Driver $driver): View
    {
        return view('admin.drivers.edit', [
            'driver' => $driver,
        ]);
    }

    public function update(Request $request, Driver $driver): RedirectResponse
    {
        $validated = $request->validate($this->validationRules());

        $driver->update($validated);

        return redirect()->route('admin.drivers.index');
    }

    public function destroy(Driver $driver): RedirectResponse
    {
        $driver->delete();

        return redirect()->route('admin.drivers.index');
    }

    public function toggleActive(Driver $driver): RedirectResponse
    {
        $driver->update(['is_active' => ! $driver->is_active]);

        return redirect()->back();
    }

    public function updateStatus(Request $request, Driver $driver): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', new Enum(DriverStatus::class)],
        ]);

        $driver->update($validated);

        return redirect()->back();
    }

    private function validationRules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:255', 'regex:/^[a-zA-ZÀ-ÿ\s.\-]+$/'],
            'phone' => ['nullable', 'string', 'regex:/^09\d{9}$/'],
            'license_number' => ['nullable', 'string', 'min:5', 'max:50', 'regex:/^[A-Z0-9\-]+$/i'],
            'is_active' => 'boolean',
            'status' => ['nullable', 'string', new Enum(DriverStatus::class)],
        ];
    }
}
