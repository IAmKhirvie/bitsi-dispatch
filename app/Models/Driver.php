<?php

namespace App\Models;

use App\Enums\DriverStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Driver extends Model
{
    use HasFactory;
    use \App\Traits\Auditable;

    protected $fillable = [
        "name",
        "phone",
        "license_number",
        "is_active",
        "status",
    ];

    protected $casts = [
        "is_active" => "boolean",
        "status" => DriverStatus::class,
    ];

    public function dispatchEntries(): HasMany
    {
        return $this->hasMany(DispatchEntry::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where("is_active", true);
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where("is_active", true)->where("status", DriverStatus::Available);
    }
}
