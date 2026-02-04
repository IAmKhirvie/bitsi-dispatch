<?php

namespace App\Exports;

use App\Models\DispatchDay;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DispatchExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function __construct(private DispatchDay $dispatchDay) {}

    public function collection()
    {
        return $this->dispatchDay->entries()->with(['vehicle', 'tripCode', 'driver'])->orderBy('sort_order')->get();
    }

    public function headings(): array
    {
        return ['#', 'Brand', 'Bus No.', 'Trip Code', 'Route', 'Bus Type', 'Dep. Terminal', 'Arr. Terminal', 'Sched. Departure', 'Actual Departure', 'Direction', 'Driver', 'Status', 'Remarks'];
    }

    public function map($entry): array
    {
        return [
            $entry->sort_order + 1,
            $entry->brand,
            $entry->bus_number,
            $entry->tripCode?->code,
            $entry->route,
            $entry->bus_type,
            $entry->departure_terminal,
            $entry->arrival_terminal,
            $entry->scheduled_departure,
            $entry->actual_departure,
            $entry->direction,
            $entry->driver?->name,
            $entry->status->value ?? $entry->status,
            $entry->remarks,
        ];
    }
}
