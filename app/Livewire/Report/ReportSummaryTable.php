<?php

namespace App\Livewire\Report;

use App\Models\DailySummary;
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
            ->with('dispatchDay');

        // Get totals from a separate aggregate query (no pagination impact)
        $totalsRaw = (clone $query)->selectRaw("
            SUM(total_trips) as total_trips,
            SUM(sb_trips) as sb_trips,
            SUM(nb_trips) as nb_trips,
            SUM(naga_trips) as naga_trips,
            SUM(legazpi_trips) as legazpi_trips,
            SUM(sorsogon_trips) as sorsogon_trips,
            SUM(virac_trips) as virac_trips,
            SUM(masbate_trips) as masbate_trips,
            SUM(tabaco_trips) as tabaco_trips,
            SUM(visayas_trips) as visayas_trips,
            SUM(cargo_trips) as cargo_trips,
            COUNT(*) as days_count
        ")->first();

        $totals = [
            'total_trips' => (int) ($totalsRaw->total_trips ?? 0),
            'sb_trips' => (int) ($totalsRaw->sb_trips ?? 0),
            'nb_trips' => (int) ($totalsRaw->nb_trips ?? 0),
            'naga_trips' => (int) ($totalsRaw->naga_trips ?? 0),
            'legazpi_trips' => (int) ($totalsRaw->legazpi_trips ?? 0),
            'sorsogon_trips' => (int) ($totalsRaw->sorsogon_trips ?? 0),
            'virac_trips' => (int) ($totalsRaw->virac_trips ?? 0),
            'masbate_trips' => (int) ($totalsRaw->masbate_trips ?? 0),
            'tabaco_trips' => (int) ($totalsRaw->tabaco_trips ?? 0),
            'visayas_trips' => (int) ($totalsRaw->visayas_trips ?? 0),
            'cargo_trips' => (int) ($totalsRaw->cargo_trips ?? 0),
        ];

        $daysCount = (int) ($totalsRaw->days_count ?? 0);

        // Paginated results ordered by dispatch day service_date
        $summaries = $query
            ->join('dispatch_days', 'daily_summaries.dispatch_day_id', '=', 'dispatch_days.id')
            ->orderBy('dispatch_days.service_date', 'desc')
            ->select('daily_summaries.*')
            ->paginate(15);

        return view('livewire.report.report-summary-table', compact('summaries', 'totals', 'daysCount'));
    }
}
