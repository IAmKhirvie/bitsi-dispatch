<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailySummary extends Model
{
    protected $fillable = [
        "dispatch_day_id",
        "total_trips",
        "sb_trips",
        "nb_trips",
        "naga_trips",
        "legazpi_trips",
        "sorsogon_trips",
        "virac_trips",
        "masbate_trips",
        "tabaco_trips",
        "visayas_trips",
        "cargo_trips",
    ];

    protected $casts = [
        "total_trips" => "integer",
        "sb_trips" => "integer",
        "nb_trips" => "integer",
        "naga_trips" => "integer",
        "legazpi_trips" => "integer",
        "sorsogon_trips" => "integer",
        "virac_trips" => "integer",
        "masbate_trips" => "integer",
        "tabaco_trips" => "integer",
        "visayas_trips" => "integer",
        "cargo_trips" => "integer",
    ];

    public function dispatchDay(): BelongsTo
    {
        return $this->belongsTo(DispatchDay::class);
    }
}
