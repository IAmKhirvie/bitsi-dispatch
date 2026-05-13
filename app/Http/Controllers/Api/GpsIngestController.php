<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\VehiclePosition;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GpsIngestController extends Controller
{
    public function ingest(Request $request): JsonResponse
    {
        $expected = config('services.gps.ingest_token') ?: env('GPS_INGEST_TOKEN');
        if (!$expected) {
            return response()->json(['error' => 'gps ingest disabled'], 503);
        }
        $token = $request->bearerToken() ?: $request->header('X-Ingest-Token');
        if (!hash_equals((string) $expected, (string) $token)) {
            return response()->json(['error' => 'unauthorized'], 401);
        }

        $data = $request->validate([
            'device_id'   => 'required|string|max:100',
            'latitude'    => 'required|numeric|between:-90,90',
            'longitude'   => 'required|numeric|between:-180,180',
            'speed_kph'   => 'nullable|integer|min:0|max:300',
            'heading'     => 'nullable|integer|min:0|max:359',
            'kmr'         => 'nullable|integer|min:0',
            'recorded_at' => 'nullable|date',
        ]);

        $vehicle = Vehicle::where('gps_device_id', $data['device_id'])->first();
        if (!$vehicle) {
            return response()->json(['error' => 'device not registered'], 404);
        }

        $recordedAt = !empty($data['recorded_at']) ? Carbon::parse($data['recorded_at']) : now();

        DB::transaction(function () use ($vehicle, $data, $recordedAt) {
            VehiclePosition::create([
                'vehicle_id'  => $vehicle->id,
                'latitude'    => $data['latitude'],
                'longitude'   => $data['longitude'],
                'speed_kph'   => $data['speed_kph'] ?? null,
                'heading'     => $data['heading'] ?? null,
                'kmr'         => $data['kmr'] ?? null,
                'recorded_at' => $recordedAt,
                'source'      => 'device',
            ]);

            $vehicle->last_lat = $data['latitude'];
            $vehicle->last_lng = $data['longitude'];
            $vehicle->last_position_at = $recordedAt;
            if (!empty($data['kmr']) && $data['kmr'] > (int) $vehicle->current_kmr) {
                $vehicle->current_kmr = (int) $data['kmr'];
            }
            $vehicle->save();
        });

        return response()->json(['ok' => true]);
    }
}
