<?php

namespace App\Services;

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
}
