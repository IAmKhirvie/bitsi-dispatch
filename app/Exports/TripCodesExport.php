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
            ->when($this->dateFrom, fn ($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return ['Code', 'Operator', 'Origin Terminal', 'Destination Terminal', 'Bus Type', 'Direction', 'Scheduled Departure', 'Active', 'Created At'];
    }

    public function map($tripCode): array
    {
        return [
            $tripCode->code,
            $tripCode->operator,
            $tripCode->origin_terminal,
            $tripCode->destination_terminal,
            $tripCode->bus_type?->label() ?? '--',
            $tripCode->direction?->label() ?? '--',
            $tripCode->scheduled_departure_time ?? '--',
            $tripCode->is_active ? 'Yes' : 'No',
            $tripCode->created_at?->format('Y-m-d H:i') ?? '',
        ];
    }
}