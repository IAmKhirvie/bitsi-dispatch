<?php

namespace App\Actions\Dispatch;

use App\Enums\DispatchStatus;
use App\Enums\VehicleStatus;
use App\Models\DispatchEntry;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class TransitionStatus
{
    /**
     * Allowed transitions from a given status.
     */
    protected const ALLOWED = [
        'scheduled'  => ['departed', 'delayed', 'cancelled', 'breakdown'],
        'departed'   => ['on_route', 'arrived', 'delayed', 'cancelled', 'breakdown'],
        'on_route'   => ['arrived', 'delayed', 'breakdown'],
        'delayed'    => ['departed', 'on_route', 'arrived', 'cancelled', 'breakdown'],
        'breakdown'  => ['scheduled', 'departed', 'delayed', 'cancelled'],
        'arrived'    => [],
        'cancelled'  => ['scheduled'],
    ];

    public function execute(
        DispatchEntry $entry,
        DispatchStatus $to,
        ?User $user = null,
        ?int $kmrReading = null,
        ?CarbonInterface $occurredAt = null,
        ?string $reason = null,
        ?string $notes = null,
    ): DispatchEntry
    {
        $from = $entry->status instanceof \BackedEnum ? $entry->status->value : ($entry->status ?? 'scheduled');
        if (!in_array($to->value, self::ALLOWED[$from] ?? [], true) && $from !== $to->value) {
            throw new InvalidArgumentException("Cannot transition from {$from} to {$to->value}.");
        }

        return DB::transaction(function () use ($entry, $to, $user, $kmrReading, $from, $occurredAt, $reason, $notes) {
            $occurredAt ??= now();
            $entry->status = $to;
            if ($user && !$entry->dispatcher_user_id) {
                $entry->dispatcher_user_id = $user->id;
            }

            $vehicle = $entry->vehicle;

            if ($to === DispatchStatus::Departed) {
                $entry->actual_departure = $occurredAt;
                if ($vehicle) {
                    $entry->kmr_at_dispatch = $kmrReading ?? $vehicle->current_kmr;
                    $vehicle->status = VehicleStatus::InTransit;
                    $vehicle->last_used_at = $occurredAt;
                    $vehicle->save();
                }
            }

            if ($to === DispatchStatus::Delayed) {
                $entry->delayed_at = $occurredAt;
                $entry->delay_reason = $reason;
            }

            if ($to === DispatchStatus::Cancelled) {
                $entry->cancelled_at = $occurredAt;
                $entry->cancel_reason = $reason;
                if ($vehicle) {
                    $vehicle->status = VehicleStatus::OK;
                    $vehicle->save();
                }
            }

            if ($to === DispatchStatus::Arrived) {
                $entry->actual_arrival = $occurredAt;
                if ($vehicle) {
                    $kmrIn = $kmrReading ?? $vehicle->current_kmr;
                    $entry->kmr_at_arrival = $kmrIn;
                    if ($kmrIn > $vehicle->current_kmr) {
                        $vehicle->current_kmr = $kmrIn;
                    }
                    $vehicle->status = VehicleStatus::OK;
                    $vehicle->save();
                }
            }

            if ($to === DispatchStatus::Breakdown && $vehicle) {
                $entry->breakdown_at = $occurredAt;
                $entry->breakdown_reason = $reason;
                $vehicle->status = VehicleStatus::UR;
                $vehicle->save();
            }

            if ($notes) {
                $entry->operations_notes = trim(($entry->operations_notes ? $entry->operations_notes . "\n" : '') . $notes);
            }

            $entry->save();
            $entry->events()->create([
                'event_type' => $to->value,
                'occurred_at' => $occurredAt,
                'actor_user_id' => $user?->id,
                'driver_id' => $entry->driver_id,
                'vehicle_id' => $entry->vehicle_id,
                'old_value' => $from,
                'new_value' => $to->value,
                'reason' => $reason,
                'notes' => $notes,
            ]);

            return $entry;
        });
    }
}
