<?php

namespace App\Models;

use App\Enums\BusType;
use App\Enums\Direction;
use App\Enums\DispatchStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DispatchEntry extends Model
{
    use HasFactory;
    use SoftDeletes;
    use \App\Traits\Auditable;

    protected $fillable = [
        "dispatch_day_id",
        "vehicle_id",
        "trip_code_id",
        "manual_trip_code",
        "driver_id",
        "driver2_id",
        "dispatcher_user_id",
        "brand",
        "bus_number",
        "seating_capacity",
        "route",
        "bus_type",
        "departure_terminal",
        "arrival_terminal",
        "scheduled_departure",
        "actual_departure",
        "actual_arrival",
        "delayed_at",
        "cancelled_at",
        "breakdown_at",
        "driver1_arrived_at",
        "driver2_arrived_at",
        "driver1_cutoff_at",
        "driver2_cutoff_at",
        "replacement_driver1_id",
        "replacement_driver2_id",
        "kmr_at_dispatch",
        "kmr_at_arrival",
        "delay_reason",
        "cancel_reason",
        "breakdown_reason",
        "operations_notes",
        "direction",
        "status",
        "remarks",
        "sort_order",
    ];

    protected $casts = [
        "status" => DispatchStatus::class,
        "actual_departure" => "datetime",
        "actual_arrival" => "datetime",
        "delayed_at" => "datetime",
        "cancelled_at" => "datetime",
        "breakdown_at" => "datetime",
        "driver1_arrived_at" => "datetime",
        "driver2_arrived_at" => "datetime",
        "driver1_cutoff_at" => "datetime",
        "driver2_cutoff_at" => "datetime",
        "seating_capacity" => "integer",
        "kmr_at_dispatch" => "integer",
        "kmr_at_arrival" => "integer",
    ];

    public function dispatcher(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'dispatcher_user_id');
    }

    public function getKmRunAttribute(): ?int
    {
        if ($this->kmr_at_dispatch !== null && $this->kmr_at_arrival !== null) {
            return max(0, $this->kmr_at_arrival - $this->kmr_at_dispatch);
        }
        return null;
    }

    public function dispatchDay(): BelongsTo
    {
        return $this->belongsTo(DispatchDay::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function tripCode(): BelongsTo
    {
        return $this->belongsTo(TripCode::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function driver2(): BelongsTo
    {
        return $this->belongsTo(Driver::class, 'driver2_id');
    }

    public function replacementDriver1(): BelongsTo
    {
        return $this->belongsTo(Driver::class, 'replacement_driver1_id');
    }

    public function replacementDriver2(): BelongsTo
    {
        return $this->belongsTo(Driver::class, 'replacement_driver2_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(DispatchEvent::class)->latest('occurred_at');
    }

    public function smsLogs(): HasMany
    {
        return $this->hasMany(SmsLog::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(DriverAttendance::class);
    }

    public function fillFromTripCode(TripCode $tripCode): self
    {
        $this->trip_code_id = $tripCode->id;
        $this->route = $tripCode->route_display;
        $this->bus_type = $tripCode->bus_type->value;
        $this->departure_terminal = $tripCode->origin_terminal;
        $this->arrival_terminal = $tripCode->destination_terminal;
        $this->scheduled_departure = $tripCode->scheduled_departure_time;
        $this->direction = $tripCode->direction->value;

        $vehicle = $tripCode->defaultVehicle;
        if ($vehicle) {
            $this->vehicle_id = $vehicle->id;
            $this->bus_number = $vehicle->bus_number;
            $this->brand = $vehicle->brand;
            $this->seating_capacity = $vehicle->seating_capacity;
        } else {
            $this->brand = $tripCode->default_brand ?: $this->brand;
            $this->seating_capacity = $tripCode->default_seating_capacity ?: $this->seating_capacity;
        }

        return $this;
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }
}
