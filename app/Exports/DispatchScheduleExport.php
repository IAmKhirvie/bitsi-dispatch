<?php

namespace App\Exports;

use App\Exports\Concerns\DispatchRowMapper;
use App\Models\DispatchEntry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DispatchScheduleExport implements FromCollection, WithHeadings, WithMapping, WithColumnWidths, WithStyles, WithTitle
{
    use DispatchRowMapper;

    public function __construct(
        private ?string $dateFrom = null,
        private ?string $dateTo = null,
        private array $filters = [],
        private string $title = 'Trip Schedule',
    ) {}

    public function collection(): Collection
    {
        return DispatchEntry::query()
            ->with(['vehicle', 'tripCode', 'driver', 'driver2', 'dispatcher', 'dispatchDay'])
            ->leftJoin('dispatch_days', 'dispatch_entries.dispatch_day_id', '=', 'dispatch_days.id')
            ->leftJoin('trip_codes', 'dispatch_entries.trip_code_id', '=', 'trip_codes.id')
            ->select('dispatch_entries.*')
            ->when($this->dateFrom, fn (Builder $q) => $q->whereDate('dispatch_days.service_date', '>=', $this->dateFrom))
            ->when($this->dateTo, fn (Builder $q) => $q->whereDate('dispatch_days.service_date', '<=', $this->dateTo))
            ->when($this->filters['search'] ?? null, fn (Builder $q, string $search) => $q->where(function (Builder $q) use ($search) {
                $q->where('dispatch_entries.bus_number', 'like', "%{$search}%")
                    ->orWhere('dispatch_entries.brand', 'like', "%{$search}%")
                    ->orWhere('dispatch_entries.route', 'like', "%{$search}%")
                    ->orWhere('dispatch_entries.departure_terminal', 'like', "%{$search}%")
                    ->orWhere('dispatch_entries.arrival_terminal', 'like', "%{$search}%")
                    ->orWhere('trip_codes.code', 'like', "%{$search}%");
            }))
            ->when($this->filters['direction'] ?? null, fn (Builder $q, string $direction) => $q->where('dispatch_entries.direction', $direction))
            ->when($this->filters['status'] ?? null, fn (Builder $q, string $status) => $q->where('dispatch_entries.status', $status))
            ->orderBy('dispatch_days.service_date')
            ->orderBy('dispatch_entries.direction')
            ->orderBy('dispatch_entries.scheduled_departure')
            ->orderBy('dispatch_entries.sort_order')
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
        return mb_substr($this->title, 0, 31);
    }

    public function columnWidths(): array
    {
        return $this->dispatchColumnWidths();
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'E5E7EB']],
            ],
        ];
    }
}
