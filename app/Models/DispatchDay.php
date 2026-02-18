<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class DispatchDay extends Model
{
    use HasFactory;
    use SoftDeletes;
    use \App\Traits\Auditable;

    protected $fillable = [
        "service_date",
        "created_by",
        "notes",
    ];

    protected $casts = [
        "service_date" => "date",
    ];

    public function entries(): HasMany
    {
        return $this->hasMany(DispatchEntry::class)->orderBy("sort_order");
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, "created_by");
    }

    public function summary(): HasOne
    {
        return $this->hasOne(DailySummary::class);
    }

    public function scopeForDate(Builder $query, string $date): Builder
    {
        return $query->whereDate("service_date", $date);
    }
}
