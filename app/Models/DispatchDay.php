<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class DispatchDay extends Model
{
    use HasFactory;
    use SoftDeletes;
    use \App\Traits\Auditable;

    protected $fillable = [
        'service_date',
        'created_by',
        'notes',
    ];

    protected $casts = [
        'service_date' => 'date',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function entries(): HasMany
    {
        return $this->hasMany(DispatchEntry::class);
    }

    public function dailySummaries(): HasMany
    {
        return $this->hasMany(DailySummary::class);
    }

    public function summary(): HasOne
    {
        return $this->hasOne(DailySummary::class);
    }
}