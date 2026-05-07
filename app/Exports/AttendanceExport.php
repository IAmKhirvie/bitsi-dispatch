<?php

namespace App\Exports;

use App\Models\DriverAttendance;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AttendanceExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function __construct(
        private ?string $dateFrom = null,
        private ?string $dateTo = null,
    ) {}

    public function collection(): Collection
    {
        return DriverAttendance::with('driver')
            ->when($this->dateFrom, fn ($q) => $q->whereDate('attendance_date', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->whereDate('attendance_date', '<=', $this->dateTo))
            ->orderBy('attendance_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return ['Driver', 'Date', 'Check In', 'Check Out', 'Status', 'Minutes Late', 'Notes', 'Recorded At'];
    }

    public function map($attendance): array
    {
        return [
            $attendance->driver?->name ?? 'N/A',
            $attendance->attendance_date?->format('Y-m-d') ?? '',
            $attendance->check_in_time ?? '--',
            $attendance->check_out_time ?? '--',
            ucfirst($attendance->status ?? 'pending'),
            $attendance->minutes_late ?? 0,
            $attendance->notes ?? '',
            $attendance->created_at?->format('Y-m-d H:i') ?? '',
        ];
    }
}