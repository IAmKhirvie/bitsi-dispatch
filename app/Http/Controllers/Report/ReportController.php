<?php

namespace App\Http\Controllers\Report;

use App\Exports\DispatchExport;
use App\Exports\DispatchScheduleExport;
use App\Exports\MonthlyReportExport;
use App\Exports\WeeklyReportExport;
use App\Http\Controllers\Controller;
use App\Models\DispatchDay;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
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
            'summary.items',
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
        $this->suppressSpreadsheetDeprecations();

        $dispatchDay = DispatchDay::whereDate('service_date', $date)->firstOrFail();
        return Excel::download(new DispatchExport($dispatchDay), "dispatch-{$date}.xlsx");
    }

    public function exportPdf(string $date)
    {
        $dispatchDay = DispatchDay::with(['entries.tripCode', 'entries.driver', 'entries.driver2', 'entries.dispatcher', 'summary.items'])
            ->whereDate('service_date', $date)
            ->firstOrFail();

        $pdf = Pdf::loadView('exports.dispatch-pdf', compact('dispatchDay'));
        return $pdf->download("dispatch-{$date}.pdf");
    }

    public function exportWeeklyExcel(Request $request)
    {
        $this->suppressSpreadsheetDeprecations();

        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
        ]);

        $from = $request->query('date_from');
        $to = $request->query('date_to');

        return Excel::download(
            new WeeklyReportExport($from, $to),
            "weekly-report-{$from}-to-{$to}.xlsx"
        );
    }

    public function exportMonthlyExcel(Request $request)
    {
        $this->suppressSpreadsheetDeprecations();

        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
        ]);

        $from = $request->query('date_from');
        $to = $request->query('date_to');

        return Excel::download(
            new MonthlyReportExport($from, $to),
            "monthly-report-{$from}-to-{$to}.xlsx"
        );
    }

    public function exportSchedulePeriod(string $period)
    {
        $this->suppressSpreadsheetDeprecations();

        $range = $this->resolveDateRange($period);

        return Excel::download(
            new DispatchScheduleExport($range['date_from'], $range['date_to'], title: "Schedule {$range['label']}"),
            "trip-schedule-{$range['label']}.xlsx"
        );
    }

    public function exportScheduleCustom(Request $request)
    {
        $this->suppressSpreadsheetDeprecations();

        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
        ]);

        $from = $request->query('date_from');
        $to = $request->query('date_to');
        $label = "{$from}-to-{$to}";

        return Excel::download(
            new DispatchScheduleExport($from, $to, title: "Schedule {$label}"),
            "trip-schedule-{$label}.xlsx"
        );
    }

    private function resolveDateRange(string $period): array
    {
        $now = CarbonImmutable::now();

        return match ($period) {
            'daily' => [
                'date_from' => $now->toDateString(),
                'date_to' => $now->toDateString(),
                'label' => $now->format('Y-m-d'),
            ],
            'weekly' => [
                'date_from' => $now->startOfWeek(Carbon::MONDAY)->toDateString(),
                'date_to' => $now->endOfWeek(Carbon::SUNDAY)->toDateString(),
                'label' => $now->startOfWeek(Carbon::MONDAY)->format('Y-m-d') . '-to-' . $now->endOfWeek(Carbon::SUNDAY)->format('Y-m-d'),
            ],
            'monthly' => [
                'date_from' => $now->startOfMonth()->toDateString(),
                'date_to' => $now->endOfMonth()->toDateString(),
                'label' => $now->format('Y-m'),
            ],
            default => [
                'date_from' => null,
                'date_to' => null,
                'label' => 'all',
            ],
        };
    }

    private function suppressSpreadsheetDeprecations(): void
    {
        error_reporting(error_reporting() & ~E_DEPRECATED);
    }
}
