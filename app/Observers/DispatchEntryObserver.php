<?php

namespace App\Observers;

use App\Jobs\SendSmsJob;
use App\Models\DispatchEntry;
use App\Services\SummaryService;

class DispatchEntryObserver
{
    public function __construct(private SummaryService $summaryService) {}

    public function created(DispatchEntry $entry): void
    {
        $this->regenerateSummary($entry);

        if ($entry->driver_id && $entry->driver?->phone) {
            $message = "BITSI Dispatch: You have been assigned to Trip {$entry->tripCode?->code} ({$entry->route}). Scheduled departure: {$entry->scheduled_departure}. Bus: {$entry->brand} {$entry->bus_number}.";
            SendSmsJob::dispatch($entry->driver->phone, $message, $entry->id);
        }
    }

    public function updated(DispatchEntry $entry): void
    {
        $this->regenerateSummary($entry);

        if ($entry->wasChanged('status') && $entry->driver_id && $entry->driver?->phone) {
            $statusLabel = is_string($entry->status) ? $entry->status : $entry->status->value;
            $message = "BITSI Dispatch: Trip {$entry->tripCode?->code} ({$entry->route}) status updated to {$statusLabel}.";
            SendSmsJob::dispatch($entry->driver->phone, $message, $entry->id);
        }
    }

    public function deleted(DispatchEntry $entry): void
    {
        $this->regenerateSummary($entry);
    }

    private function regenerateSummary(DispatchEntry $entry): void
    {
        if ($entry->dispatchDay) {
            $this->summaryService->generateForDay($entry->dispatchDay);
        }
    }
}
