<?php

namespace App\Exports\Concerns;

use App\Models\DispatchEntry;
use Carbon\Carbon;

/**
 * Canonical column layout for dispatch-based Excel exports.
 * Follows the reference TRIP SCHEDULE.xlsx schedule columns, with dispatch-only fields appended.
 */
trait DispatchRowMapper
{
    public function dispatchHeadings(): array
    {
        return [
            'Date',
            'Service Class',
            'Trip Code',
            'PAX',
            'Origin',
            'Destination',
            'Scheduled',
            'Actual Time',
            'Late Dispatch',
            'Status',
            'Action',
            'Seating Capacity',
            'Bus No.',
            'Brand',
            'Driver 1',
            'Driver 2',
            'Dispatcher',
            'KMR Out',
            'KMR In',
            'KMR',
            'Remarks',
        ];
    }

    public function dispatchColumnWidths(): array
    {
        return [
            'A' => 12,
            'B' => 18,
            'C' => 14,
            'D' => 16,
            'E' => 18,
            'F' => 18,
            'G' => 14,
            'H' => 14,
            'I' => 16,
            'J' => 14,
            'K' => 10,
            'L' => 16,
            'M' => 12,
            'N' => 14,
            'O' => 22,
            'P' => 22,
            'Q' => 22,
            'R' => 14,
            'S' => 14,
            'T' => 12,
            'U' => 30,
        ];
    }

    public function dispatchRow(DispatchEntry $entry): array
    {
        $values = $this->dispatchTemplateValues($entry);

        return [
            $values['date'],
            $values['service_class'],
            $values['trip_code'],
            $values['pax'],
            $values['origin'],
            $values['destination'],
            $values['scheduled'],
            $values['actual'],
            $values['late_dispatch'],
            $values['status'],
            $values['action'],
            $values['seating_capacity'],
            $values['bus_number'],
            $values['brand'],
            $values['driver_1'],
            $values['driver_2'],
            $values['dispatcher'],
            $values['kmr_out'],
            $values['kmr_in'],
            $values['kmr_run'],
            $values['remarks'],
        ];
    }

    public function dispatchTemplateValues(DispatchEntry $entry): array
    {
        return [
            'date' => optional($entry->dispatchDay)->service_date?->format('Y-m-d'),
            'service_class' => $this->enumLabel($entry->bus_type),
            'trip_code' => $entry->manual_trip_code ?: $entry->tripCode?->code,
            'pax' => $entry->seating_capacity ?? $entry->tripCode?->default_seating_capacity,
            'origin' => $entry->departure_terminal,
            'destination' => $entry->arrival_terminal,
            'scheduled' => $this->fmtTime($entry->scheduled_departure),
            'actual' => $this->fmtTime($entry->actual_departure),
            'late_dispatch' => $this->lateDispatch($entry),
            'status' => $this->enumLabel($entry->status),
            'action' => '',
            'seating_capacity' => $entry->seating_capacity,
            'bus_number' => $entry->bus_number,
            'brand' => $entry->brand,
            'driver_1' => $entry->driver?->name,
            'driver_2' => $entry->driver2?->name,
            'dispatcher' => $entry->dispatcher?->name,
            'kmr_out' => $entry->kmr_at_dispatch,
            'kmr_in' => $entry->kmr_at_arrival,
            'kmr_run' => $entry->km_run,
            'remarks' => $entry->remarks,
        ];
    }

    protected function enumLabel($value): ?string
    {
        if ($value instanceof \UnitEnum && method_exists($value, 'label')) {
            return $value->label();
        }

        if ($value instanceof \BackedEnum) {
            return ucwords(str_replace('_', ' ', $value->value));
        }

        return $value ? ucwords(str_replace('_', ' ', (string) $value)) : null;
    }

    protected function fmtTime($value): ?string
    {
        if (!$value) return null;
        try {
            return Carbon::parse($value)->format('H:i:s');
        } catch (\Throwable $e) {
            return (string) $value;
        }
    }

    protected function lateDispatch(DispatchEntry $entry): ?string
    {
        if (! $entry->scheduled_departure || ! $entry->actual_departure) {
            return null;
        }

        try {
            $scheduled = Carbon::parse($entry->scheduled_departure);
            $actual = Carbon::parse($entry->actual_departure);
            $minutes = $scheduled->diffInMinutes($actual, false);

            return $minutes > 0 ? "{$minutes} min" : null;
        } catch (\Throwable $e) {
            return null;
        }
    }
}
