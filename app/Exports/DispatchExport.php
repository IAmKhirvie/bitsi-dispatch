<?php

namespace App\Exports;

use App\Exports\Concerns\DispatchRowMapper;
use App\Models\DispatchDay;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DispatchExport implements FromCollection, WithHeadings, WithMapping, WithColumnWidths, WithStyles, WithTitle
{
    use DispatchRowMapper;

    public function __construct(private DispatchDay $dispatchDay) {}

    public function collection()
    {
        return $this->dispatchDay->entries()
            ->with(['vehicle', 'tripCode', 'driver', 'driver2', 'dispatcher', 'dispatchDay'])
            ->orderBy('sort_order')
            ->get();
    }

    public function headings(): array
    {
        return $this->dispatchHeadings();
    }

    public function map($entry): array
    {
        return $this->dispatchRow($entry);
    }

    public function title(): string
    {
        return $this->dispatchDay->service_date?->format('Y-m-d') ?? 'Dispatch';
    }

    public function columnWidths(): array
    {
        return $this->dispatchColumnWidths();
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'E5E7EB']]],
        ];
    }
}
