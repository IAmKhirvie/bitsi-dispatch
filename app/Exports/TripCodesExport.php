<?php

namespace App\Exports;

use App\Models\TripCode;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TripCodesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function __construct(
        private ?string $dateFrom = null,
        private ?string $dateTo = null,
    ) {}

    public function collection(): Collection
    {
        return TripCode::query()
            ->with('defaultVehicle')
            ->when($this->dateFrom, fn ($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->orderBy('code')
            ->get();
    }

    public function headings(): array
    {
        return ['Code', 'Operator', 'Bus No.', 'Brand', 'Bus Type', 'Seating', 'KMR', 'Origin', 'Destination', 'Direction', 'Scheduled', 'Active'];
    }

    public function map($tripCode): array
    {
        $v = $tripCode->defaultVehicle;
        return [
            $tripCode->code,
            $tripCode->operator,
            $v?->bus_number ?? '--',
            $v?->brand ?? $tripCode->default_brand ?? '--',
            $tripCode->bus_type?->label() ?? '--',
            $v?->seating_capacity ?? $tripCode->default_seating_capacity ?? '--',
            $v?->current_kmr !== null ? number_format($v->current_kmr) : '--',
            $tripCode->origin_terminal,
            $tripCode->destination_terminal,
            $tripCode->direction?->label() ?? '--',
            $tripCode->scheduled_departure_time ?? '--',
            $tripCode->is_active ? 'Yes' : 'No',
        ];
    }
}