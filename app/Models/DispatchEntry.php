<?php

namespace App\Models;

use App\Enums\BusType;
use App\Enums\Direction;
use App\Enums\DispatchStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DispatchEntry extends Model
{
    use HasFactory;
    use \App\Traits\Auditable;

    protected $fillable = [
        "dispatch_day_id",
        "vehicle_id",
        "trip_code_id",
        "driver_id",
        "driver2_id",
        "brand",
        "bus_number",
        "route",
        "bus_type",
        "departure_terminal",
        "arrival_terminal",
        "scheduled_departure",
        "actual_departure",
        "direction",
        "status",
        "remarks",
        "sort_order",
    ];

    protected $casts = [
        "status" => DispatchStatus::class,
    ];

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

    public function smsLogs(): HasMany
    {
        return $this->hasMany(SmsLog::class);
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

        return $this;
    }
}
