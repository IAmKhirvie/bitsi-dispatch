<?php

namespace App\Observers;

use App\Enums\DriverStatus;
use App\Enums\VehicleStatus;
use App\Jobs\SendSmsJob;
use App\Models\DispatchEntry;
use App\Services\SummaryService;

class DispatchEntryObserver
{
    public function __construct(private SummaryService $summaryService) {}

    public function created(DispatchEntry $entry): void
    {
        $this->regenerateSummary($entry);
        $this->notifyDriversAssigned($entry);
        $this->syncDriverVehicleStatusOnCreate($entry);
    }

    public function updated(DispatchEntry $entry): void
    {
        $this->regenerateSummary($entry);

        if ($entry->wasChanged('status')) {
            $status = $entry->status?->value ?? $entry->status;

            // Auto-set actual departure when status changes to departed
            if ($status === 'departed' && !$entry->actual_departure) {
                $entry->updateQuietly(['actual_departure' => now()->format('H:i')]);
            }

            $this->syncDriverVehicleStatus($entry);

            // SMS notification
            $statusLabel = is_string($entry->status) ? $entry->status : $entry->status->value;
            $message = "BITSI Dispatch: Trip {$entry->tripCode?->code} ({$entry->route}) status updated to {$statusLabel}.";

            if ($entry->driver_id && $entry->driver?->phone) {
                SendSmsJob::dispatch($entry->driver->phone, $message, $entry->id);
            }
            if ($entry->driver2_id && $entry->driver2?->phone) {
                SendSmsJob::dispatch($entry->driver2->phone, $message, $entry->id);
            }
        }

        // Handle driver/vehicle reassignment
        if ($entry->wasChanged('driver_id') || $entry->wasChanged('vehicle_id')) {
            $this->syncDriverVehicleStatusOnCreate($entry);
        }
    }

    public function deleted(DispatchEntry $entry): void
    {
        $this->regenerateSummary($entry);
        $this->releaseDriverVehicleOnDelete($entry);
    }

    private function syncDriverVehicleStatusOnCreate(DispatchEntry $entry): void
    {
        if ($entry->driver_id && $entry->driver) {
            $entry->driver->updateQuietly(['status' => DriverStatus::Dispatched]);
        }
        if ($entry->vehicle_id && $entry->vehicle) {
            $entry->vehicle->updateQuietly(['status' => VehicleStatus::InTransit]);
        }
    }

    private function syncDriverVehicleStatus(DispatchEntry $entry): void
    {
        $status = $entry->status?->value ?? $entry->status;

        match ($status) {
            'departed' => $this->setStatuses($entry, DriverStatus::Dispatched, VehicleStatus::InTransit),
            'on_route' => $this->setStatuses($entry, DriverStatus::OnRoute, VehicleStatus::InTransit),
            'arrived' => $this->setStatuses($entry, DriverStatus::Available, VehicleStatus::OK, updateLocation: true),
            'cancelled' => $this->setStatuses($entry, DriverStatus::Available, VehicleStatus::OK),
            default => null,
        };
    }

    private function setStatuses(
        DispatchEntry $entry,
        DriverStatus $driverStatus,
        VehicleStatus $vehicleStatus,
        bool $updateLocation = false,
    ): void {
        if ($entry->driver_id && $entry->driver) {
            $entry->driver->updateQuietly(['status' => $driverStatus]);
        }

        if ($entry->vehicle_id && $entry->vehicle) {
            $vehicleData = ['status' => $vehicleStatus];
            if ($updateLocation && $entry->arrival_terminal) {
                $vehicleData['current_location'] = $entry->arrival_terminal;
            }
            $entry->vehicle->updateQuietly($vehicleData);
        }
    }

    private function releaseDriverVehicleOnDelete(DispatchEntry $entry): void
    {
        // Only release if driver/vehicle has no other active (non-arrived, non-cancelled) entries
        if ($entry->driver_id && $entry->driver) {
            $hasOtherActive = DispatchEntry::where('driver_id', $entry->driver_id)
                ->where('id', '!=', $entry->id)
                ->whereNotIn('status', ['arrived', 'cancelled'])
                ->exists();

            if (!$hasOtherActive) {
                $entry->driver->updateQuietly(['status' => DriverStatus::Available]);
            }
        }

        if ($entry->vehicle_id && $entry->vehicle) {
            $hasOtherActive = DispatchEntry::where('vehicle_id', $entry->vehicle_id)
                ->where('id', '!=', $entry->id)
                ->whereNotIn('status', ['arrived', 'cancelled'])
                ->exists();

            if (!$hasOtherActive) {
                $entry->vehicle->updateQuietly(['status' => VehicleStatus::OK]);
            }
        }
    }

    private function notifyDriversAssigned(DispatchEntry $entry): void
    {
        $message = "BITSI Dispatch: You have been assigned to Trip {$entry->tripCode?->code} ({$entry->route}). Scheduled departure: {$entry->scheduled_departure}. Bus: {$entry->brand} {$entry->bus_number}.";

        if ($entry->driver_id && $entry->driver?->phone) {
            SendSmsJob::dispatch($entry->driver->phone, $message, $entry->id);
        }
        if ($entry->driver2_id && $entry->driver2?->phone) {
            SendSmsJob::dispatch($entry->driver2->phone, $message, $entry->id);
        }
    }

    private function regenerateSummary(DispatchEntry $entry): void
    {
        if ($entry->dispatchDay) {
            $this->summaryService->generateForDay($entry->dispatchDay);
        }
    }
}
