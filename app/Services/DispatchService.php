<?php

namespace App\Services;

use App\Models\DispatchDay;
use App\Models\TripCode;
use App\Models\Vehicle;

class DispatchService
{
    public function buildEntryFromTripCode(TripCode $tripCode, ?Vehicle $vehicle = null): array
    {
        $data = [
            'trip_code_id' => $tripCode->id,
            'route' => $tripCode->route_display,
            'bus_type' => $tripCode->bus_type->value,
            'departure_terminal' => $tripCode->origin_terminal,
            'arrival_terminal' => $tripCode->destination_terminal,
            'scheduled_departure' => $tripCode->scheduled_departure_time,
            'direction' => $tripCode->direction->value,
            'status' => 'scheduled',
        ];

        if ($vehicle) {
            $data['vehicle_id'] = $vehicle->id;
            $data['brand'] = $vehicle->brand;
            $data['bus_number'] = $vehicle->bus_number;
        }

        return $data;
    }

    public function populateFromTripCodes(DispatchDay $day): void
    {
        // Skip if the day already has entries
        if ($day->entries()->count() > 0) {
            return;
        }

        $tripCodes = TripCode::active()->orderBy('scheduled_departure_time')->get();

        foreach ($tripCodes as $i => $tc) {
            $day->entries()->create([
                'trip_code_id' => $tc->id,
                'route' => $tc->route_display,
                'bus_type' => $tc->bus_type?->value,
                'departure_terminal' => $tc->origin_terminal,
                'arrival_terminal' => $tc->destination_terminal,
                'scheduled_departure' => $tc->scheduled_departure_time,
                'direction' => $tc->direction?->value,
                'status' => 'scheduled',
                'sort_order' => $i,
            ]);
        }
    }
}
