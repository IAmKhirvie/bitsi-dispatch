<?php

namespace App\Http\Controllers\Report;

use App\Exports\DispatchExport;
use App\Http\Controllers\Controller;
use App\Models\DispatchDay;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(): View
    {
        return view('reports.index');
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
            'summary' => $dispatchDay->summary,
            'entries' => $dispatchDay->entries,
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
