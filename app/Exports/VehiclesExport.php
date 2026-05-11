<?php

namespace App\Exports;

use App\Models\Vehicle;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class VehiclesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function __construct(
        private ?string $dateFrom = null,
        private ?string $dateTo = null,
    ) {}

    public function collection(): Collection
    {
        // Buses are a static reference list — always export the full fleet.
        return Vehicle::query()->orderBy('bus_number')->get();
    }

    public function headings(): array
    {
        return ['Bus No.', 'Brand', 'Bus Type', 'Seating', 'Plate Number', 'Status', 'Current KMR', 'Last PMS KMR', 'KM Since PMS', 'PMS Band', 'Last PMS Date', 'Next PMS Date', 'Idle Days'];
    }

    public function map($vehicle): array
    {
        return [
            $vehicle->bus_number,
            $vehicle->brand,
            $vehicle->bus_type?->label() ?? $vehicle->bus_type,
            $vehicle->seating_capacity ?? '--',
            $vehicle->plate_number,
            $vehicle->status?->label() ?? '--',
            $vehicle->current_kmr ?? 0,
            $vehicle->last_pms_kmr ?? 0,
            $vehicle->km_since_pms ?? 0,
            strtoupper($vehicle->pms_band ?? '--'),
            $vehicle->last_pms_date?->format('Y-m-d') ?? '--',
            $vehicle->next_pms_date?->format('Y-m-d') ?? '--',
            $vehicle->idle_days ?? 0,
        ];
    }
}