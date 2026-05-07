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
        return Vehicle::query()
            ->when($this->dateFrom, fn ($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return ['Bus Number', 'Brand', 'Bus Type', 'Plate Number', 'Status', 'Location', 'Total KM', 'PMS Threshold', 'Current PMS', 'PMS %', 'Last PMS', 'Next PMS', 'Idle Days', 'Created At'];
    }

    public function map($vehicle): array
    {
        return [
            $vehicle->bus_number,
            $vehicle->brand,
            $vehicle->bus_type?->label() ?? $vehicle->bus_type,
            $vehicle->plate_number,
            $vehicle->status?->label() ?? '--',
            $vehicle->current_location ?? '--',
            $vehicle->total_kilometers ?? 0,
            $vehicle->pms_threshold ?? 0,
            $vehicle->current_pms_value ?? 0,
            round($vehicle->pms_percentage ?? 0) . '%',
            $vehicle->last_pms_date?->format('Y-m-d') ?? '--',
            $vehicle->next_pms_date?->format('Y-m-d') ?? '--',
            $vehicle->idle_days ?? 0,
            $vehicle->created_at?->format('Y-m-d H:i') ?? '',
        ];
    }
}