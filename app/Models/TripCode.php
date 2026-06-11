<?php

namespace App\Models;

use App\Enums\BusType;
use App\Enums\Direction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class TripCode extends Model
{
    use HasFactory;
    use SoftDeletes;
    use \App\Traits\Auditable;

    protected $fillable = [
        "code",
        "operator",
        "default_vehicle_id",
        "default_brand",
        "default_seating_capacity",
        "origin_terminal",
        "destination_terminal",
        "bus_type",
        "scheduled_departure_time",
        "direction",
        "is_active",
    ];

    protected $casts = [
        "bus_type" => BusType::class,
        "direction" => Direction::class,
        "is_active" => "boolean",
        "default_seating_capacity" => "integer",
    ];

    public function dispatchEntries(): HasMany
    {
        return $this->hasMany(DispatchEntry::class);
    }

    public function defaultVehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'default_vehicle_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where("is_active", true);
    }

    public function getRouteDisplayAttribute(): string
    {
        return "{$this->origin_terminal} → {$this->destination_terminal}";
    }
}
