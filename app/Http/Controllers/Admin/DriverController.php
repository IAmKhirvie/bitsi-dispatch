<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DriverController extends Controller
{
    public function index(Request $request): View
    {
        $drivers = Driver::query()
            ->when($request->search, fn ($q, $s) => $q->where(function ($q2) use ($s) {
                $q2->where('name', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%")
                    ->orWhere('license_number', 'like', "%{$s}%");
            }))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->has('active'), fn ($q) => $q->where('is_active', $request->boolean('active')))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.drivers.index', [
            'drivers' => $drivers,
            'filters' => $request->only(['search', 'active', 'status']),
        ]);
    }

    public function create(): View
    {
        return view('admin.drivers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'license_number' => 'nullable|string|max:50',
            'is_active' => 'boolean',
            'status' => 'nullable|string|in:available,dispatched,on_route,on_leave',
        ]);

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
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'license_number' => 'nullable|string|max:50',
            'is_active' => 'boolean',
            'status' => 'nullable|string|in:available,dispatched,on_route,on_leave',
        ]);

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
            'status' => 'required|string|in:available,dispatched,on_route,on_leave',
        ]);

        $driver->update($validated);

        return redirect()->back();
    }
}
