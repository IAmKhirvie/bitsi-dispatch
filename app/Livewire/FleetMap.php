<?php

namespace App\Livewire;

use App\Models\Vehicle;
use Livewire\Component;

class FleetMap extends Component
{
    public int $staleMinutes = 15;

    public function getPositionsProperty(): array
    {
        return Vehicle::query()
            ->whereNotNull('last_lat')
            ->whereNotNull('last_lng')
            ->get(['id', 'bus_number', 'brand', 'status', 'last_lat', 'last_lng', 'last_position_at', 'current_kmr'])
            ->map(fn ($v) => [
                'id'          => $v->id,
                'bus_number'  => $v->bus_number,
                'brand'       => $v->brand,
                'status'      => $v->status?->value ?? (string) $v->status,
                'lat'         => (float) $v->last_lat,
                'lng'         => (float) $v->last_lng,
                'kmr'         => $v->current_kmr,
                'recorded_at' => optional($v->last_position_at)->toIso8601String(),
                'stale'       => $v->last_position_at
                    ? $v->last_position_at->lt(now()->subMinutes($this->staleMinutes))
                    : true,
            ])
            ->values()
            ->all();
    }

    public function render()
    {
        return view('livewire.fleet-map', [
            'positions' => $this->positions,
        ]);
    }
}
