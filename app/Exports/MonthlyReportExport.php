<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MonthlyReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function __construct(
        private string $dateFrom,
        private string $dateTo,
    ) {}

    public function collection(): Collection
    {
        $categories = ['sb', 'nb', 'naga', 'legazpi', 'sorsogon', 'virac', 'masbate', 'tabaco', 'visayas', 'cargo'];

        $monthlyTotals = DB::table('daily_summaries')
            ->join('dispatch_days', 'daily_summaries.dispatch_day_id', '=', 'dispatch_days.id')
            ->whereDate('dispatch_days.service_date', '>=', $this->dateFrom)
            ->whereDate('dispatch_days.service_date', '<=', $this->dateTo)
            ->selectRaw("
                YEAR(dispatch_days.service_date) as yr,
                MONTH(dispatch_days.service_date) as mo,
                SUM(daily_summaries.total_trips) as total_trips,
                COUNT(*) as days_count
            ")
            ->groupByRaw('YEAR(dispatch_days.service_date), MONTH(dispatch_days.service_date)')
            ->orderByRaw('yr ASC, mo ASC')
            ->get();

        $monthlyCategories = DB::table('daily_summaries')
            ->join('dispatch_days', 'daily_summaries.dispatch_day_id', '=', 'dispatch_days.id')
            ->join('daily_summary_items', 'daily_summary_items.daily_summary_id', '=', 'daily_summaries.id')
            ->whereDate('dispatch_days.service_date', '>=', $this->dateFrom)
            ->whereDate('dispatch_days.service_date', '<=', $this->dateTo)
            ->selectRaw("
                YEAR(dispatch_days.service_date) as yr,
                MONTH(dispatch_days.service_date) as mo,
                daily_summary_items.category,
                SUM(daily_summary_items.trip_count) as total
            ")
            ->groupByRaw('YEAR(dispatch_days.service_date), MONTH(dispatch_days.service_date), daily_summary_items.category')
            ->get()
            ->groupBy(fn ($row) => $row->yr . '-' . $row->mo);

        return $monthlyTotals->map(function ($row) use ($monthlyCategories, $categories) {
            $key = $row->yr . '-' . $row->mo;
            $catData = $monthlyCategories->get($key, collect())->pluck('total', 'category');
            $monthName = \Carbon\Carbon::create($row->yr, $row->mo)->format('F Y');
            $data = [
                'month_label' => $monthName,
                'days_count' => $row->days_count,
                'total_trips' => (int) $row->total_trips,
            ];
            foreach ($categories as $cat) {
                $data[$cat] = (int) ($catData[$cat] ?? 0);
            }
            return (object) $data;
        });
    }

    public function headings(): array
    {
        return ['Month', 'Days', 'Total', 'SB', 'NB', 'Naga', 'Legazpi', 'Sorsogon', 'Virac', 'Masbate', 'Tabaco', 'Visayas', 'Cargo'];
    }

    public function map($row): array
    {
        return [
            $row->month_label,
            $row->days_count,
            $row->total_trips,
            $row->sb ?? 0,
            $row->nb ?? 0,
            $row->naga ?? 0,
            $row->legazpi ?? 0,
            $row->sorsogon ?? 0,
            $row->virac ?? 0,
            $row->masbate ?? 0,
            $row->tabaco ?? 0,
            $row->visayas ?? 0,
            $row->cargo ?? 0,
        ];
    }
}
