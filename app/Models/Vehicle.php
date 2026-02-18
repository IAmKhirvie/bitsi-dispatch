<?php

namespace App\Models;

use App\Enums\BusType;
use App\Enums\PmsUnit;
use App\Enums\VehicleStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use HasFactory;
    use SoftDeletes;
    use \App\Traits\Auditable;

    protected $fillable = [
        "bus_number",
        "brand",
        "bus_type",
        "plate_number",
        "status",
        "pms_unit",
        "pms_threshold",
        "current_pms_value",
        "last_pms_date",
        "idle_days",
        "last_used_at",
    ];

    protected $casts = [
        "bus_type" => BusType::class,
        "status" => VehicleStatus::class,
        "pms_unit" => PmsUnit::class,
        "pms_threshold" => "integer",
        "current_pms_value" => "integer",
        "last_pms_date" => "datetime",
        "idle_days" => "integer",
        "last_used_at" => "datetime",
    ];

    public function dispatchEntries(): HasMany
    {
        return $this->hasMany(DispatchEntry::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where("status", VehicleStatus::OK);
    }

    public function getIsPmsWarningAttribute(): bool
    {
        return $this->current_pms_value >= $this->pms_threshold;
    }

    public function getIsPmsApproachingAttribute(): bool
    {
        $pct = $this->pms_percentage;
        return $pct >= 80 && $pct < 100;
    }

    public function getPmsPercentageAttribute(): float
    {
        if ($this->pms_threshold === 0) {
            return 0;
        }

        return round(($this->current_pms_value / $this->pms_threshold) * 100, 1);
    }

    public function scopePmsAlert(Builder $query): Builder
    {
        return $query->where('pms_threshold', '>', 0)
            ->whereRaw('current_pms_value >= pms_threshold * 0.8');
    }
}
