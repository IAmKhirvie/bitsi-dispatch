<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class DriverAttendance extends Model
{
    use HasFactory;
    use \App\Traits\Auditable;

    protected $fillable = [
        'driver_id',
        'dispatch_entry_id',
        'attendance_date',
        'check_in_time',
        'check_out_time',
        'status',
        'minutes_late',
        'notes',
        'marked_by',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'check_in_time' => 'datetime:H:i',
        'check_out_time' => 'datetime:H:i',
        'minutes_late' => 'integer',
    ];

    /**
     * The driver associated with this attendance.
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    /**
     * The dispatch entry associated with this attendance.
     */
    public function dispatchEntry(): BelongsTo
    {
        return $this->belongsTo(DispatchEntry::class);
    }

    /**
     * The user who marked this attendance.
     */
    public function markedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'marked_by');
    }

    /**
     * Scope to filter by date.
     */
    public function scopeForDate(Builder $query, string $date): Builder
    {
        return $query->where('attendance_date', $date);
    }

    /**
     * Scope to filter by driver.
     */
    public function scopeForDriver(Builder $query, int $driverId): Builder
    {
        return $query->where('driver_id', $driverId);
    }

    /**
     * Scope to get pending attendances.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get absent attendances.
     */
    public function scopeAbsent(Builder $query): Builder
    {
        return $query->where('status', 'absent');
    }

    /**
     * Scope to get late attendances.
     */
    public function scopeLate(Builder $query): Builder
    {
        return $query->where('status', 'late');
    }

    /**
     * Mark the attendance as late.
     */
    public function markAsLate(int $minutes, ?int $markedBy = null): bool
    {
        return $this->update([
            'status' => 'late',
            'minutes_late' => $minutes,
            'marked_by' => $markedBy ?? auth()->id(),
        ]);
    }

    /**
     * Mark the attendance as absent.
     */
    public function markAsAbsent(?int $markedBy = null): bool
    {
        return $this->update([
            'status' => 'absent',
            'marked_by' => $markedBy ?? auth()->id(),
        ]);
    }

    /**
     * Mark the attendance as on time.
     */
    public function markAsOnTime(?int $markedBy = null): bool
    {
        return $this->update([
            'status' => 'on_time',
            'marked_by' => $markedBy ?? auth()->id(),
        ]);
    }

    /**
     * Mark the attendance as excused.
     */
    public function markAsExcused(string $notes = '', ?int $markedBy = null): bool
    {
        return $this->update([
            'status' => 'excused',
            'notes' => $notes,
            'marked_by' => $markedBy ?? auth()->id(),
        ]);
    }

    /**
     * Check in the driver.
     */
    public function checkIn(string $time): bool
    {
        $entry = $this->dispatchEntry;
        $scheduledTime = $entry->scheduled_departure ?? '00:00';

        // Calculate if late
        $status = 'on_time';
        $minutesLate = 0;

        if ($scheduledTime && $time > $scheduledTime) {
            $scheduled = \Carbon\Carbon::parse($scheduledTime);
            $actual = \Carbon\Carbon::parse($time);
            $minutesLate = $scheduled->diffInMinutes($actual);
            $threshold = (int) AttendanceSetting::get('late_threshold_minutes', 15);

            if ($minutesLate > $threshold) {
                $status = 'late';
            }
        }

        return $this->update([
            'check_in_time' => $time,
            'status' => $status,
            'minutes_late' => $minutesLate,
        ]);
    }

    /**
     * Check out the driver.
     */
    public function checkOut(string $time): bool
    {
        return $this->update([
            'check_out_time' => $time,
        ]);
    }
}
