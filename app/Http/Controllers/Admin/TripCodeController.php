<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TripCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TripCodeController extends Controller
{
    public function index(): View
    {
        return view('admin.trip-codes.index');
    }

    public function create(): View
    {
        return view('admin.trip-codes.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|min:2|max:50|unique:trip_codes,code',
            'operator' => 'required|string|min:2|max:255',
            'origin_terminal' => 'required|string|min:2|max:255',
            'destination_terminal' => 'required|string|min:2|max:255',
            'bus_type' => 'required|string|in:regular,deluxe,super_deluxe,elite,sleeper,single_seater,skybus',
            'scheduled_departure_time' => 'required|date_format:H:i',
            'direction' => 'required|string|in:SB,NB',
            'is_active' => 'boolean',
        ]);

        TripCode::create($validated);

        return redirect()->route('admin.trip-codes.index');
    }

    public function edit(TripCode $tripCode): View
    {
        return view('admin.trip-codes.edit', [
            'tripCode' => $tripCode,
        ]);
    }

    public function update(Request $request, TripCode $tripCode): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'min:2', 'max:50', Rule::unique('trip_codes', 'code')->ignore($tripCode->id)],
            'operator' => 'required|string|min:2|max:255',
            'origin_terminal' => 'required|string|min:2|max:255',
            'destination_terminal' => 'required|string|min:2|max:255',
            'bus_type' => 'required|string|in:regular,deluxe,super_deluxe,elite,sleeper,single_seater,skybus',
            'scheduled_departure_time' => 'required|date_format:H:i',
            'direction' => 'required|string|in:SB,NB',
            'is_active' => 'boolean',
        ]);

        $tripCode->update($validated);

        return redirect()->route('admin.trip-codes.index');
    }

    public function destroy(TripCode $tripCode): RedirectResponse
    {
        $tripCode->delete();

        return redirect()->route('admin.trip-codes.index');
    }

    public function toggleActive(TripCode $tripCode): RedirectResponse
    {
        $tripCode->update(['is_active' => ! $tripCode->is_active]);

        return redirect()->back();
    }
}
