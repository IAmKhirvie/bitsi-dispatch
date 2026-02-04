<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TripCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class TripCodeController extends Controller
{
    public function index(Request $request): Response
    {
        $tripCodes = TripCode::query()
            ->when($request->search, fn ($q, $s) => $q->where(function ($q2) use ($s) {
                $q2->where('code', 'like', "%{$s}%")
                    ->orWhere('operator', 'like', "%{$s}%")
                    ->orWhere('origin_terminal', 'like', "%{$s}%")
                    ->orWhere('destination_terminal', 'like', "%{$s}%");
            }))
            ->when($request->direction, fn ($q, $d) => $q->where('direction', $d))
            ->when($request->has('active'), fn ($q) => $q->where('is_active', $request->boolean('active')))
            ->orderBy('code')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('admin/TripCodes/Index', [
            'tripCodes' => $tripCodes,
            'filters' => $request->only(['search', 'direction', 'active']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/TripCodes/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:trip_codes,code',
            'operator' => 'required|string|max:255',
            'origin_terminal' => 'required|string|max:255',
            'destination_terminal' => 'required|string|max:255',
            'bus_type' => 'required|string|in:regular,deluxe,super_deluxe,elite,sleeper,single_seater,skybus',
            'scheduled_departure_time' => 'required|string',
            'direction' => 'required|string|in:SB,NB',
            'is_active' => 'boolean',
        ]);

        TripCode::create($validated);

        return redirect()->route('admin.trip-codes.index');
    }

    public function edit(TripCode $tripCode): Response
    {
        return Inertia::render('admin/TripCodes/Edit', [
            'tripCode' => $tripCode,
        ]);
    }

    public function update(Request $request, TripCode $tripCode): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', Rule::unique('trip_codes', 'code')->ignore($tripCode->id)],
            'operator' => 'required|string|max:255',
            'origin_terminal' => 'required|string|max:255',
            'destination_terminal' => 'required|string|max:255',
            'bus_type' => 'required|string|in:regular,deluxe,super_deluxe,elite,sleeper,single_seater,skybus',
            'scheduled_departure_time' => 'required|string',
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
