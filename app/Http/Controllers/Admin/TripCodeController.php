<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BusType;
use App\Enums\Direction;
use App\Http\Controllers\Controller;
use App\Models\TripCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
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
        $validated = $request->validate(array_merge($this->validationRules(), [
            'code' => 'required|string|min:2|max:50|unique:trip_codes,code',
        ]));

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
        $validated = $request->validate(array_merge($this->validationRules(), [
            'code' => ['required', 'string', 'min:2', 'max:50', Rule::unique('trip_codes', 'code')->ignore($tripCode->id)],
        ]));

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

    private function validationRules(): array
    {
        return [
            'operator' => 'required|string|min:2|max:255',
            'origin_terminal' => 'required|string|min:2|max:255',
            'destination_terminal' => 'required|string|min:2|max:255',
            'bus_type' => ['required', 'string', new Enum(BusType::class)],
            'scheduled_departure_time' => 'required|date_format:H:i',
            'direction' => ['required', 'string', new Enum(Direction::class)],
            'is_active' => 'boolean',
        ];
    }
}
