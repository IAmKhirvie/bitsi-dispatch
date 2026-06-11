<?php

namespace App\Exports;

use App\Models\SmsLog;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SmsLogsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function __construct(
        private ?string $dateFrom = null,
        private ?string $dateTo = null,
    ) {}

    public function collection(): Collection
    {
        return SmsLog::with('dispatchEntry')
            ->when($this->dateFrom, fn ($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return ['Recipient Phone', 'Message', 'Status', 'Dispatch Entry', 'Sent At', 'Created At'];
    }

    public function map($log): array
    {
        return [
            $log->recipient_phone ?? '--',
            $log->message ?? '',
            $log->status?->label() ?? '--',
            $log->dispatchEntry?->id ? 'Entry #' . $log->dispatchEntry->id : 'N/A',
            $log->sent_at?->format('Y-m-d H:i') ?? '--',
            $log->created_at?->format('Y-m-d H:i') ?? '',
        ];
    }
}