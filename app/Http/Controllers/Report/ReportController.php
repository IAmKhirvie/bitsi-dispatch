<?php

namespace App\Http\Controllers\Report;

use App\Exports\DispatchExport;
use App\Exports\DispatchScheduleExport;
use App\Exports\MonthlyReportExport;
use App\Exports\WeeklyReportExport;
use App\Http\Controllers\Controller;
use App\Models\DispatchDay;
use App\Models\ReportTemplate;
use App\Services\ReportTemplateExporter;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(): View
    {
        return view('reports.index', [
            'dispatchTemplate' => ReportTemplate::active('dispatch'),
        ]);
    }

    public function daily(string $date): View|RedirectResponse
    {
        try {
            $date = Carbon::parse($date)->toDateString();

            $dispatchDay = DispatchDay::with([
                'entries.vehicle',
                'entries.tripCode',
                'entries.driver',
                'entries.driver2',
                'entries.dispatcher',
                'summary.items',
            ])
                ->whereDate('service_date', $date)
                ->firstOrFail();

            return view('reports.daily', [
                'dispatchDay' => $dispatchDay,
                'summary' => $dispatchDay->summary,
                'entries' => $dispatchDay->entries,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Daily report view failed.', [
                'date' => $date,
                'message' => $e->getMessage(),
            ]);

            return redirect()
                ->route('reports.index')
                ->with('error', 'Daily report could not be opened. Please check that the report date has dispatch data.');
        }
    }

    public function exportExcel(string $date, ReportTemplateExporter $templateExporter)
    {
        $this->suppressSpreadsheetDeprecations();

        $date = Carbon::parse($date)->toDateString();
        $dispatchDay = DispatchDay::whereDate('service_date', $date)->firstOrFail();
        $template = ReportTemplate::active('dispatch');

        if ($template) {
            $entries = $dispatchDay->entries()
                ->with(['vehicle', 'tripCode', 'driver', 'driver2', 'dispatcher', 'dispatchDay'])
                ->orderBy('sort_order')
                ->get();

            try {
                return $templateExporter->downloadDispatch($entries, $template, "dispatch-{$date}.xlsx");
            } catch (\Throwable $e) {
                Log::warning('Dispatch template export failed.', [
                    'template_id' => $template->id,
                    'date' => $date,
                    'message' => $e->getMessage(),
                ]);

                return redirect()
                    ->route('reports.index')
                    ->with('error', 'The active XLSX template could not be used for this dispatch export. Upload a dispatch report template or remove the current template.');
            }
        }

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
        $export = new DispatchScheduleExport($range['date_from'], $range['date_to'], title: "Schedule {$range['label']}");

        if ($template = ReportTemplate::active('dispatch')) {
            try {
                return app(ReportTemplateExporter::class)->downloadDispatch(
                    $export->collection(),
                    $template,
                    "trip-schedule-{$range['label']}.xlsx"
                );
            } catch (\Throwable $e) {
                Log::warning('Schedule template export failed.', [
                    'template_id' => $template->id,
                    'period' => $period,
                    'message' => $e->getMessage(),
                ]);

                return redirect()
                    ->route('reports.index')
                    ->with('error', 'The active XLSX template could not be used for this schedule export. Upload a dispatch report template or remove the current template.');
            }
        }

        return Excel::download($export, "trip-schedule-{$range['label']}.xlsx");
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
        $export = new DispatchScheduleExport($from, $to, title: "Schedule {$label}");

        if ($template = ReportTemplate::active('dispatch')) {
            try {
                return app(ReportTemplateExporter::class)->downloadDispatch(
                    $export->collection(),
                    $template,
                    "trip-schedule-{$label}.xlsx"
                );
            } catch (\Throwable $e) {
                Log::warning('Custom schedule template export failed.', [
                    'template_id' => $template->id,
                    'label' => $label,
                    'message' => $e->getMessage(),
                ]);

                return redirect()
                    ->route('reports.index')
                    ->with('error', 'The active XLSX template could not be used for this schedule export. Upload a dispatch report template or remove the current template.');
            }
        }

        return Excel::download($export, "trip-schedule-{$label}.xlsx");
    }

    public function storeTemplate(Request $request, ReportTemplateExporter $templateExporter): RedirectResponse
    {
        $validated = $request->validate([
            'report_type' => ['required', 'string', 'in:dispatch'],
            'template' => ['required', 'file', 'mimes:xlsx', 'max:10240'],
        ]);

        $file = $request->file('template');

        try {
            $templateExporter->assertDispatchTemplate($file->getRealPath());
        } catch (\Throwable $e) {
            Log::warning('Report template import rejected.', [
                'file_name' => $file->getClientOriginalName(),
                'message' => $e->getMessage(),
            ]);

            return redirect()
                ->route('reports.index')
                ->with('error', 'That XLSX file does not look like a dispatch report template. Include headings like Trip Code and Scheduled.');
        }

        $path = $file->store('report-templates', 'local');

        ReportTemplate::where('report_type', $validated['report_type'])->get()->each->delete();
        ReportTemplate::create([
            'report_type' => $validated['report_type'],
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'uploaded_by' => $request->user()?->id,
        ]);

        return redirect()
            ->route('reports.index')
            ->with('status', 'Report template imported. Future dispatch Excel exports will use this XLSX layout.');
    }

    public function destroyTemplate(ReportTemplate $template): RedirectResponse
    {
        $template->delete();

        return redirect()
            ->route('reports.index')
            ->with('status', 'Report template removed. Exports will use the default layout.');
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
