<?php

namespace App\Actions\Dispatch;

use App\Enums\DispatchStatus;
use App\Enums\VehicleStatus;
use App\Models\DispatchEntry;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class TransitionStatus
{
    /**
     * Allowed transitions from a given status.
     */
    protected const ALLOWED = [
        'scheduled'  => ['departed', 'delayed', 'cancelled'],
        'departed'   => ['on_route', 'arrived', 'delayed', 'cancelled'],
        'on_route'   => ['arrived', 'delayed'],
        'delayed'    => ['departed', 'on_route', 'arrived', 'cancelled'],
        'arrived'    => [],
        'cancelled'  => ['scheduled'],
    ];

    public function execute(DispatchEntry $entry, DispatchStatus $to, ?User $user = null, ?int $kmrReading = null): DispatchEntry
    {
        $from = $entry->status?->value ?? 'scheduled';
        if (!in_array($to->value, self::ALLOWED[$from] ?? [], true) && $from !== $to->value) {
            throw new InvalidArgumentException("Cannot transition from {$from} to {$to->value}.");
        }

        return DB::transaction(function () use ($entry, $to, $user, $kmrReading, $from) {
            $entry->status = $to;
            if ($user && !$entry->dispatcher_user_id) {
                $entry->dispatcher_user_id = $user->id;
            }

            $vehicle = $entry->vehicle;

            if ($to === DispatchStatus::Departed) {
                $entry->actual_departure = now();
                if ($vehicle) {
                    $entry->kmr_at_dispatch = $kmrReading ?? $vehicle->current_kmr;
                    $vehicle->status = VehicleStatus::InTransit;
                    $vehicle->last_used_at = now();
                    $vehicle->save();
                }
            }

            if ($to === DispatchStatus::Arrived) {
                $entry->actual_arrival = now();
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

            $entry->save();
            return $entry;
        });
    }
}
