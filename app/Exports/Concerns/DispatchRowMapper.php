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
            'Seats Available',
            'Origin',
            'Destination',
            'Departure Time',
            'Status',
            'Action',
            'Seating Capacity',
            'Bus No.',
            'Brand',
            'Driver 1',
            'Driver 2',
            'Dispatcher',
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
            'G' => 15,
            'H' => 14,
            'I' => 10,
            'J' => 16,
            'K' => 12,
            'L' => 14,
            'M' => 22,
            'N' => 22,
            'O' => 22,
            'P' => 30,
        ];
    }

    public function dispatchRow(DispatchEntry $entry): array
    {
        return [
            optional($entry->dispatchDay)->service_date?->format('Y-m-d'),
            $this->enumLabel($entry->bus_type),
            $entry->tripCode?->code,
            $entry->seating_capacity ?? $entry->tripCode?->default_seating_capacity,
            $entry->departure_terminal,
            $entry->arrival_terminal,
            $this->fmtTime($entry->scheduled_departure),
            $this->enumLabel($entry->status),
            '',
            $entry->seating_capacity,
            $entry->bus_number,
            $entry->brand,
            $entry->driver?->name,
            $entry->driver2?->name,
            $entry->dispatcher?->name,
            $entry->remarks,
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
            return Carbon::parse($value)->format('H:i');
        } catch (\Throwable $e) {
            return (string) $value;
        }
    }
}
