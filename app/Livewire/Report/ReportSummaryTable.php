<?php

namespace App\Livewire\Report;

use App\Models\DailySummary;
use App\Models\DailySummaryItem;
use Livewire\Component;
use Livewire\WithPagination;

class ReportSummaryTable extends Component
{
    use WithPagination;

    public string $dateFrom = '';
    public string $dateTo = '';

    protected $queryString = [
        'dateFrom' => ['as' => 'date_from'],
        'dateTo' => ['as' => 'date_to'],
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

    public function render()
    {
        $query = DailySummary::query()
            ->whereHas('dispatchDay', function ($q) {
                $q->whereDate('service_date', '>=', $this->dateFrom)
                  ->whereDate('service_date', '<=', $this->dateTo);
            })
            ->with(['dispatchDay', 'items']);

        // Get summary IDs for aggregate query
        $summaryIds = (clone $query)->pluck('id');

        // Totals via normalized items table
        $totalsRaw = DailySummaryItem::whereIn('daily_summary_id', $summaryIds)
            ->selectRaw('category, SUM(trip_count) as total')
            ->groupBy('category')
            ->pluck('total', 'category');

        $totalTrips = DailySummary::whereIn('id', $summaryIds)->sum('total_trips');
        $daysCount = $summaryIds->count();

        $categories = ['sb', 'nb', 'naga', 'legazpi', 'sorsogon', 'virac', 'masbate', 'tabaco', 'visayas', 'cargo'];
        $totals = ['total_trips' => (int) $totalTrips];
        foreach ($categories as $cat) {
            $totals[$cat . '_trips'] = (int) ($totalsRaw[$cat] ?? 0);
        }

        // Paginated results ordered by dispatch day service_date
        $summaries = $query
            ->join('dispatch_days', 'daily_summaries.dispatch_day_id', '=', 'dispatch_days.id')
            ->orderBy('dispatch_days.service_date', 'desc')
            ->select('daily_summaries.*')
            ->paginate(15);

        return view('livewire.report.report-summary-table', compact('summaries', 'totals', 'daysCount'));
    }
}
