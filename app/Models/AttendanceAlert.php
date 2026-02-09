<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class AttendanceAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'dispatch_entry_id',
        'alert_date',
        'alert_type',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'alert_date' => 'date',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    /**
     * The driver associated with this alert.
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    /**
     * The dispatch entry associated with this alert.
     */
    public function dispatchEntry(): BelongsTo
    {
        return $this->belongsTo(DispatchEntry::class);
    }

    /**
     * Scope to get unread alerts.
     */
    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope to get alerts for a specific date.
     */
    public function scopeForDate(Builder $query, string $date): Builder
    {
        return $query->where('alert_date', $date);
    }

    /**
     * Scope to get upcoming alerts.
     */
    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('alert_type', 'upcoming');
    }

    /**
     * Scope to get late alerts.
     */
    public function scopeLate(Builder $query): Builder
    {
        return $query->where('alert_type', 'late');
    }

    /**
     * Scope to get absent alerts.
     */
    public function scopeAbsent(Builder $query): Builder
    {
        return $query->where('alert_type', 'absent');
    }

    /**
     * Mark the alert as read.
     */
    public function markAsRead(): bool
    {
        return $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Mark the alert as unread.
     */
    public function markAsUnread(): bool
    {
        return $this->update([
            'is_read' => false,
            'read_at' => null,
        ]);
    }

    /**
     * Create a new alert.
     */
    public static function createAlert(int $driverId, int $dispatchEntryId, string $alertType, string $alertDate): self
    {
        return static::updateOrCreate(
            [
                'driver_id' => $driverId,
                'dispatch_entry_id' => $dispatchEntryId,
                'alert_type' => $alertType,
                'alert_date' => $alertDate,
            ],
            [
                'is_read' => false,
                'read_at' => null,
            ]
        );
    }
}
