<?php

namespace App\Exports\Concerns;

use App\Models\DispatchEntry;
use Carbon\Carbon;

/**
 * Canonical column layout for all dispatch-based Excel exports.
 * Matches the reference TRIP SCHEDULE.xlsx columns + KMR / dispatcher additions.
 */
trait DispatchRowMapper
{
    public function dispatchHeadings(): array
    {
        return [
            'Date',
            'Trip Code',
            'Service Class',
            'Route',
            'Direction',
            'Bus No.',
            'Brand',
            'Seating',
            'Driver 1',
            'Driver 2',
            'Dispatcher',
            'Scheduled',
            'Departed',
            'Arrived',
            'KMR Out',
            'KMR In',
            'KM Run',
            'Status',
            'Remarks',
        ];
    }

    public function dispatchRow(DispatchEntry $entry): array
    {
        $kmRun = ($entry->kmr_at_dispatch && $entry->kmr_at_arrival)
            ? max(0, $entry->kmr_at_arrival - $entry->kmr_at_dispatch)
            : null;

        return [
            optional($entry->dispatchDay)->service_date?->format('Y-m-d'),
            $entry->tripCode?->code,
            $entry->bus_type,
            $entry->route,
            $entry->direction instanceof \BackedEnum ? $entry->direction->value : $entry->direction,
            $entry->bus_number,
            $entry->brand,
            $entry->seating_capacity,
            $entry->driver?->name,
            $entry->driver2?->name,
            $entry->dispatcher?->name,
            $this->fmtTime($entry->scheduled_departure),
            $this->fmtTime($entry->actual_departure),
            $this->fmtTime($entry->actual_arrival),
            $entry->kmr_at_dispatch,
            $entry->kmr_at_arrival,
            $kmRun,
            $entry->status instanceof \BackedEnum ? $entry->status->value : $entry->status,
            $entry->remarks,
        ];
    }

    protected function fmtTime($value): ?string
    {
        if (!$value) return null;
        try {
            return Carbon::parse($value)->format('H:i');
        } catch (\Throwable $e) {
            return (string) $value;
        }
    }
}
