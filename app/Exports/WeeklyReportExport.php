<?php

namespace App\Exports;

use App\Models\DailySummary;
use App\Models\DailySummaryItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class WeeklyReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function __construct(
        private string $dateFrom,
        private string $dateTo,
    ) {}

    public function collection(): Collection
    {
        $categories = ['sb', 'nb', 'naga', 'legazpi', 'sorsogon', 'virac', 'masbate', 'tabaco', 'visayas', 'cargo'];

        $weeklyTotals = DB::table('daily_summaries')
            ->join('dispatch_days', 'daily_summaries.dispatch_day_id', '=', 'dispatch_days.id')
            ->whereDate('dispatch_days.service_date', '>=', $this->dateFrom)
            ->whereDate('dispatch_days.service_date', '<=', $this->dateTo)
            ->selectRaw("
                YEAR(dispatch_days.service_date) as yr,
                WEEK(dispatch_days.service_date, 1) as wk,
                SUM(daily_summaries.total_trips) as total_trips,
                COUNT(*) as days_count
            ")
            ->groupByRaw('YEAR(dispatch_days.service_date), WEEK(dispatch_days.service_date, 1)')
            ->orderByRaw('yr ASC, wk ASC')
            ->get();

        $weeklyCategories = DB::table('daily_summaries')
            ->join('dispatch_days', 'daily_summaries.dispatch_day_id', '=', 'dispatch_days.id')
            ->join('daily_summary_items', 'daily_summary_items.daily_summary_id', '=', 'daily_summaries.id')
            ->whereDate('dispatch_days.service_date', '>=', $this->dateFrom)
            ->whereDate('dispatch_days.service_date', '<=', $this->dateTo)
            ->selectRaw("
                YEAR(dispatch_days.service_date) as yr,
                WEEK(dispatch_days.service_date, 1) as wk,
                daily_summary_items.category,
                SUM(daily_summary_items.trip_count) as total
            ")
            ->groupByRaw('YEAR(dispatch_days.service_date), WEEK(dispatch_days.service_date, 1), daily_summary_items.category')
            ->get()
            ->groupBy(fn ($row) => $row->yr . '-' . $row->wk);

        return $weeklyTotals->map(function ($row) use ($weeklyCategories, $categories) {
            $key = $row->yr . '-' . $row->wk;
            $catData = $weeklyCategories->get($key, collect())->pluck('total', 'category');
            $data = [
                'week_label' => "Week {$row->wk}, {$row->yr}",
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
        return ['Week', 'Days', 'Total', 'SB', 'NB', 'Naga', 'Legazpi', 'Sorsogon', 'Virac', 'Masbate', 'Tabaco', 'Visayas', 'Cargo'];
    }

    public function map($row): array
    {
        return [
            $row->week_label,
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
