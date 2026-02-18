<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailySummaryItem extends Model
{
    protected $fillable = [
        'daily_summary_id',
        'category',
        'trip_count',
    ];

    protected $casts = [
        'trip_count' => 'integer',
    ];

    public function dailySummary(): BelongsTo
    {
        return $this->belongsTo(DailySummary::class);
    }
}
