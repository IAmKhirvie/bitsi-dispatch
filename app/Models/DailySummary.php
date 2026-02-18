<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DailySummary extends Model
{
    protected $fillable = [
        'dispatch_day_id',
        'total_trips',
    ];

    protected $casts = [
        'total_trips' => 'integer',
    ];

    public function dispatchDay(): BelongsTo
    {
        return $this->belongsTo(DispatchDay::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(DailySummaryItem::class);
    }

    /**
     * Get the trip count for a specific category.
     * Expects items to be eager-loaded for performance.
     */
    public function tripCount(string $category): int
    {
        return $this->items->firstWhere('category', $category)?->trip_count ?? 0;
    }
}
