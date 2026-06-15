<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DispatchEvent extends Model
{
    protected $fillable = [
        'dispatch_entry_id',
        'event_type',
        'occurred_at',
        'actor_user_id',
        'driver_id',
        'vehicle_id',
        'old_value',
        'new_value',
        'reason',
        'notes',
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
    ];

    public function dispatchEntry(): BelongsTo
    {
        return $this->belongsTo(DispatchEntry::class);
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}
