<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehiclePosition extends Model
{
    protected $fillable = [
        'vehicle_id',
        'latitude',
        'longitude',
        'speed_kph',
        'heading',
        'kmr',
        'recorded_at',
        'source',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'speed_kph' => 'integer',
        'heading' => 'integer',
        'kmr' => 'integer',
        'recorded_at' => 'datetime',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}
