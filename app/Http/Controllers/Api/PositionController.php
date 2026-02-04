<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Position;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'speed' => 'nullable|numeric|min:0',
            'heading' => 'nullable|numeric|between:0,360',
        ]);

        $validated['recorded_at'] = now();

        $position = Position::create($validated);

        return response()->json($position, 201);
    }

    public function latest(): JsonResponse
    {
        $vehicles = Vehicle::with(['latestPosition'])
            ->whereHas('latestPosition')
            ->get()
            ->map(fn($v) => [
                'id' => $v->id,
                'bus_number' => $v->bus_number,
                'brand' => $v->brand,
                'bus_type' => $v->bus_type->value ?? $v->bus_type,
                'plate_number' => $v->plate_number,
                'status' => $v->status->value ?? $v->status,
                'position' => [
                    'latitude' => $v->latestPosition->latitude,
                    'longitude' => $v->latestPosition->longitude,
                    'speed' => $v->latestPosition->speed,
                    'heading' => $v->latestPosition->heading,
                    'recorded_at' => $v->latestPosition->recorded_at,
                ],
            ]);

        return response()->json($vehicles);
    }
}
