<?php

namespace App\Exports;

use App\Models\Driver;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DriversExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function __construct(
        private ?string $dateFrom = null,
        private ?string $dateTo = null,
    ) {}

    public function collection(): Collection
    {
        return Driver::query()
            ->when($this->dateFrom, fn ($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return ['Name', 'Phone', 'License Number', 'Active', 'Status', 'Created At', 'Updated At'];
    }

    public function map($driver): array
    {
        return [
            $driver->name,
            $driver->phone ?? '--',
            $driver->license_number ?? '--',
            $driver->is_active ? 'Yes' : 'No',
            $driver->status?->label() ?? '--',
            $driver->created_at?->format('Y-m-d H:i') ?? '',
            $driver->updated_at?->format('Y-m-d H:i') ?? '',
        ];
    }
}