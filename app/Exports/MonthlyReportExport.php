<?php

namespace App\Exports;

use App\Exports\Concerns\DispatchRowMapper;
use App\Models\DispatchEntry;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MonthlyReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithTitle
{
    use DispatchRowMapper;

    public function __construct(private string $dateFrom, private string $dateTo) {}

    public function collection(): Collection
    {
        return DispatchEntry::query()
            ->with(['vehicle', 'tripCode', 'driver', 'driver2', 'dispatcher', 'dispatchDay'])
            ->whereHas('dispatchDay', function ($q) {
                $q->whereDate('service_date', '>=', $this->dateFrom)
                  ->whereDate('service_date', '<=', $this->dateTo);
            })
            ->join('dispatch_days', 'dispatch_entries.dispatch_day_id', '=', 'dispatch_days.id')
            ->orderBy('dispatch_days.service_date')
            ->orderBy('dispatch_entries.sort_order')
            ->select('dispatch_entries.*')
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
        return "Month {$this->dateFrom} to {$this->dateTo}";
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'E5E7EB']]],
        ];
    }
}
