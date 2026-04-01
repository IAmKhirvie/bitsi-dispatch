<?php

namespace App\Models;

use App\Enums\BusType;
use App\Enums\PmsUnit;
use App\Enums\VehicleStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
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
        "current_location",
        "pms_unit",
        "pms_threshold",
        "current_pms_value",
        "total_kilometers",
        "last_pms_date",
        "pms_interval_months",
        "next_pms_date",
        "idle_days",
        "last_used_at",
    ];

    protected $casts = [
        "bus_type" => BusType::class,
        "status" => VehicleStatus::class,
        "pms_unit" => PmsUnit::class,
        "pms_threshold" => "integer",
        "current_pms_value" => "integer",
        "total_kilometers" => "integer",
        "last_pms_date" => "datetime",
        "pms_interval_months" => "integer",
        "next_pms_date" => "date",
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

    public function getIsPmsScheduleDueAttribute(): bool
    {
        return $this->next_pms_date && $this->next_pms_date->lte(today());
    }

    public function getIsPmsScheduleApproachingAttribute(): bool
    {
        return $this->next_pms_date
            && $this->next_pms_date->gt(today())
            && $this->next_pms_date->lte(today()->addWeeks(2));
    }

    public function recalculateNextPmsDate(): void
    {
        if ($this->last_pms_date && $this->pms_interval_months) {
            $this->next_pms_date = $this->last_pms_date->copy()->addMonths($this->pms_interval_months);
        }
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }
}
