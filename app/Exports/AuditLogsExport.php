<?php

namespace App\Exports;

use App\Models\AuditLog;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AuditLogsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function __construct(
        private ?string $dateFrom = null,
        private ?string $dateTo = null,
    ) {}

    public function collection(): Collection
    {
        return AuditLog::with('user')
            ->when($this->dateFrom, fn ($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return ['User', 'Action', 'Entity Type', 'Entity ID', 'Old Values', 'New Values', 'IP Address', 'Date'];
    }

    public function map($log): array
    {
        return [
            $log->user?->name ?? 'System',
            $log->action,
            class_basename($log->auditable_type ?? ''),
            $log->auditable_id ?? '',
            $log->old_values ? json_encode($log->old_values) : '',
            $log->new_values ? json_encode($log->new_values) : '',
            $log->ip_address ?? '',
            $log->created_at?->format('Y-m-d H:i') ?? '',
        ];
    }
}