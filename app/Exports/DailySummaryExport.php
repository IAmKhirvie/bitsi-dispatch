<?php

namespace App\Exports;

use App\Models\DailySummary;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DailySummaryExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function __construct(private Collection $summaries) {}

    public function collection()
    {
        return $this->summaries;
    }

    public function headings(): array
    {
        return ['Date', 'Total', 'SB', 'NB', 'Naga', 'Legazpi', 'Sorsogon', 'Virac', 'Masbate', 'Tabaco', 'Visayas', 'Cargo'];
    }

    public function map($summary): array
    {
        return [
            $summary->dispatchDay?->service_date?->format('Y-m-d') ?? '',
            $summary->total_trips,
            $summary->sb_trips,
            $summary->nb_trips,
            $summary->naga_trips,
            $summary->legazpi_trips,
            $summary->sorsogon_trips,
            $summary->virac_trips,
            $summary->masbate_trips,
            $summary->tabaco_trips,
            $summary->visayas_trips,
            $summary->cargo_trips,
        ];
    }
}
