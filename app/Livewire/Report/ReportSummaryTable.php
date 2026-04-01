<?php

namespace App\Livewire\Report;

use App\Models\DailySummary;
use App\Models\DailySummaryItem;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class ReportSummaryTable extends Component
{
    use WithPagination;

    public string $dateFrom = '';
    public string $dateTo = '';
    public string $reportType = 'daily'; // daily, weekly, monthly

    protected $queryString = [
        'dateFrom' => ['as' => 'date_from'],
        'dateTo' => ['as' => 'date_to'],
        'reportType' => ['as' => 'type'],
    ];

    public function mount(): void
    {
        $this->dateFrom = $this->dateFrom ?: now()->startOfMonth()->toDateString();
        $this->dateTo = $this->dateTo ?: now()->toDateString();
    }

    public function updatingDateFrom(): void
    {
        $this->resetPage();
    }

    public function updatingDateTo(): void
    {
        $this->resetPage();
    }

    public function updatingReportType(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $categories = ['sb', 'nb', 'naga', 'legazpi', 'sorsogon', 'virac', 'masbate', 'tabaco', 'visayas', 'cargo'];

        if ($this->reportType === 'weekly') {
            return $this->renderWeekly($categories);
        }

        if ($this->reportType === 'monthly') {
            return $this->renderMonthly($categories);
        }

        return $this->renderDaily($categories);
    }

    private function renderDaily(array $categories)
    {
        $query = DailySummary::query()
            ->whereHas('dispatchDay', function ($q) {
                $q->whereDate('service_date', '>=', $this->dateFrom)
                  ->whereDate('service_date', '<=', $this->dateTo);
            })
            ->with(['dispatchDay', 'items']);

        $summaryIds = (clone $query)->pluck('id');

        $totalsRaw = DailySummaryItem::whereIn('daily_summary_id', $summaryIds)
            ->selectRaw('category, SUM(trip_count) as total')
            ->groupBy('category')
            ->pluck('total', 'category');

        $totalTrips = DailySummary::whereIn('id', $summaryIds)->sum('total_trips');
        $daysCount = $summaryIds->count();

        $totals = ['total_trips' => (int) $totalTrips];
        foreach ($categories as $cat) {
            $totals[$cat . '_trips'] = (int) ($totalsRaw[$cat] ?? 0);
        }

        $summaries = $query
            ->join('dispatch_days', 'daily_summaries.dispatch_day_id', '=', 'dispatch_days.id')
            ->orderBy('dispatch_days.service_date', 'desc')
            ->select('daily_summaries.*')
            ->paginate(15);

        return view('livewire.report.report-summary-table', compact('summaries', 'totals', 'daysCount'));
    }

    private function renderWeekly(array $categories)
    {
        // Aggregate by ISO week
        $rows = DB::table('daily_summaries')
            ->join('dispatch_days', 'daily_summaries.dispatch_day_id', '=', 'dispatch_days.id')
            ->join('daily_summary_items', 'daily_summary_items.daily_summary_id', '=', 'daily_summaries.id')
            ->whereDate('dispatch_days.service_date', '>=', $this->dateFrom)
            ->whereDate('dispatch_days.service_date', '<=', $this->dateTo)
            ->selectRaw("
                YEAR(dispatch_days.service_date) as yr,
                WEEK(dispatch_days.service_date, 1) as wk,
                MIN(dispatch_days.service_date) as week_start,
                MAX(dispatch_days.service_date) as week_end,
                COUNT(DISTINCT daily_summaries.id) as days_count,
                SUM(daily_summaries.total_trips) / COUNT(DISTINCT daily_summary_items.daily_summary_id) * COUNT(DISTINCT daily_summaries.id) as raw_trips
            ")
            ->groupByRaw('YEAR(dispatch_days.service_date), WEEK(dispatch_days.service_date, 1)')
            ->orderByRaw('yr DESC, wk DESC')
            ->get();

        // Get weekly category breakdowns
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

        // Get weekly total trips
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
            ->orderByRaw('yr DESC, wk DESC')
            ->get();

        $weeklyRows = $weeklyTotals->map(function ($row) use ($weeklyCategories, $categories) {
            $key = $row->yr . '-' . $row->wk;
            $catData = $weeklyCategories->get($key, collect())->pluck('total', 'category');
            $data = [
                'week_label' => "Week {$row->wk}, {$row->yr}",
                'days_count' => $row->days_count,
                'total_trips' => (int) $row->total_trips,
            ];
            foreach ($categories as $cat) {
                $data[$cat . '_trips'] = (int) ($catData[$cat] ?? 0);
            }
            return (object) $data;
        });

        // Grand totals
        $totals = ['total_trips' => $weeklyRows->sum('total_trips')];
        foreach ($categories as $cat) {
            $totals[$cat . '_trips'] = $weeklyRows->sum($cat . '_trips');
        }
        $daysCount = $weeklyRows->sum('days_count');

        return view('livewire.report.report-summary-table', [
            'summaries' => null,
            'weeklyRows' => $weeklyRows,
            'totals' => $totals,
            'daysCount' => $daysCount,
        ]);
    }

    private function renderMonthly(array $categories)
    {
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
            ->orderByRaw('yr DESC, mo DESC')
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

        $monthlyRows = $monthlyTotals->map(function ($row) use ($monthlyCategories, $categories) {
            $key = $row->yr . '-' . $row->mo;
            $catData = $monthlyCategories->get($key, collect())->pluck('total', 'category');
            $monthName = \Carbon\Carbon::create($row->yr, $row->mo)->format('F Y');
            $data = [
                'month_label' => $monthName,
                'days_count' => $row->days_count,
                'total_trips' => (int) $row->total_trips,
            ];
            foreach ($categories as $cat) {
                $data[$cat . '_trips'] = (int) ($catData[$cat] ?? 0);
            }
            return (object) $data;
        });

        $totals = ['total_trips' => $monthlyRows->sum('total_trips')];
        foreach ($categories as $cat) {
            $totals[$cat . '_trips'] = $monthlyRows->sum($cat . '_trips');
        }
        $daysCount = $monthlyRows->sum('days_count');

        return view('livewire.report.report-summary-table', [
            'summaries' => null,
            'monthlyRows' => $monthlyRows,
            'totals' => $totals,
            'daysCount' => $daysCount,
        ]);
    }
}
