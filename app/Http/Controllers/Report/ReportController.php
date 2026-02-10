<?php

namespace App\Http\Controllers\Report;

use App\Exports\DispatchExport;
use App\Http\Controllers\Controller;
use App\Models\DailySummary;
use App\Models\DispatchDay;
use App\Models\DispatchEntry;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->toDateString());
        $dateTo = $request->input('date_to', now()->toDateString());

        $summaries = DailySummary::whereHas('dispatchDay', function ($q) use ($dateFrom, $dateTo) {
            $q->whereDate('service_date', '>=', $dateFrom)
              ->whereDate('service_date', '<=', $dateTo);
        })
            ->with('dispatchDay')
            ->get()
            ->sortBy('dispatchDay.service_date');

        $totals = [
            'total_trips' => $summaries->sum('total_trips'),
            'sb_trips' => $summaries->sum('sb_trips'),
            'nb_trips' => $summaries->sum('nb_trips'),
            'naga_trips' => $summaries->sum('naga_trips'),
            'legazpi_trips' => $summaries->sum('legazpi_trips'),
            'sorsogon_trips' => $summaries->sum('sorsogon_trips'),
            'virac_trips' => $summaries->sum('virac_trips'),
            'masbate_trips' => $summaries->sum('masbate_trips'),
            'tabaco_trips' => $summaries->sum('tabaco_trips'),
            'visayas_trips' => $summaries->sum('visayas_trips'),
            'cargo_trips' => $summaries->sum('cargo_trips'),
        ];

        return view('reports.index', [
            'summaries' => $summaries->values(),
            'totals' => $totals,
            'filters' => [
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ],
        ]);
    }

    public function daily(string $date): View
    {
        $dispatchDay = DispatchDay::with([
            'entries.vehicle',
            'entries.tripCode',
            'entries.driver',
            'entries.driver2',
            'summary',
        ])
            ->whereDate('service_date', $date)
            ->firstOrFail();

        return view('reports.daily', [
            'dispatchDay' => $dispatchDay,
        ]);
    }

    public function exportExcel(string $date)
    {
        $dispatchDay = DispatchDay::whereDate('service_date', $date)->firstOrFail();
        return Excel::download(new DispatchExport($dispatchDay), "dispatch-{$date}.xlsx");
    }

    public function exportPdf(string $date)
    {
        $dispatchDay = DispatchDay::with(['entries.tripCode', 'entries.driver', 'summary'])
            ->whereDate('service_date', $date)
            ->firstOrFail();

        $pdf = Pdf::loadView('exports.dispatch-pdf', compact('dispatchDay'));
        return $pdf->download("dispatch-{$date}.pdf");
    }
}
