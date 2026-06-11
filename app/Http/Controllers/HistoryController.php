<?php

namespace App\Http\Controllers;

use App\Exports\DispatchScheduleExport;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class HistoryController extends Controller
{
    public function index(): View
    {
        return view('history.index');
    }

    public function exportPeriod(string $period)
    {
        $this->suppressSpreadsheetDeprecations();

        $range = $this->resolveDateRange($period);

        return Excel::download(
            new DispatchScheduleExport($range['date_from'], $range['date_to'], title: "History {$range['label']}"),
            "dispatch-history-{$range['label']}.xlsx"
        );
    }

    public function exportCustom(Request $request)
    {
        $this->suppressSpreadsheetDeprecations();

        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'search' => 'nullable|string|max:255',
            'direction' => 'nullable|in:SB,NB',
            'status' => 'nullable|string|max:50',
        ]);

        $from = $request->query('date_from');
        $to = $request->query('date_to');
        $label = $from && $to ? "{$from}-to-{$to}" : 'filtered';

        return Excel::download(
            new DispatchScheduleExport(
                $from,
                $to,
                $request->only(['search', 'direction', 'status']),
                "History {$label}"
            ),
            "dispatch-history-{$label}.xlsx"
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
