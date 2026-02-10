<?php

namespace App\Livewire\Admin;

use App\Models\AttendanceAlert;
use App\Models\DispatchDay;
use App\Models\DispatchEntry;
use App\Models\DriverAttendance;
use Livewire\Component;
use Livewire\WithPagination;

class AttendanceTable extends Component
{
    use WithPagination;

    public string $date = '';

    public function mount(): void
    {
        $this->date = $this->date ?: now()->toDateString();
    }

    public function updatingDate(): void
    {
        $this->resetPage();
    }

    public function initializeToday(): void
    {
        $dispatchDay = DispatchDay::where('service_date', $this->date)->first();

        if (!$dispatchDay) {
            session()->flash('error', 'No dispatch day found for this date.');
            return;
        }

        $entries = $dispatchDay->entries()->with('driver')->get();

        foreach ($entries as $entry) {
            if (!$entry->driver_id) continue;

            DriverAttendance::firstOrCreate(
                [
                    'driver_id' => $entry->driver_id,
                    'dispatch_entry_id' => $entry->id,
                    'attendance_date' => $this->date,
                ],
                [
                    'status' => 'pending',
                    'marked_by' => auth()->id(),
                ]
            );
        }

        session()->flash('status', 'Attendance records initialized.');
    }

    public function markLate(int $attendanceId, int $minutesLate = 15): void
    {
        $attendance = DriverAttendance::findOrFail($attendanceId);
        $attendance->markAsLate($minutesLate);

        AttendanceAlert::createAlert(
            $attendance->driver_id,
            $attendance->dispatch_entry_id,
            'late'
        );

        session()->flash('status', 'Driver marked as late.');
    }

    public function markAbsent(int $attendanceId): void
    {
        $attendance = DriverAttendance::findOrFail($attendanceId);
        $attendance->markAsAbsent();

        AttendanceAlert::createAlert(
            $attendance->driver_id,
            $attendance->dispatch_entry_id,
            'absent'
        );

        session()->flash('status', 'Driver marked as absent.');
    }

    public function overrideStatus(int $attendanceId, string $status): void
    {
        $attendance = DriverAttendance::findOrFail($attendanceId);

        match ($status) {
            'on_time' => $attendance->markAsOnTime(),
            'late' => $attendance->markAsLate(0),
            'absent' => $attendance->markAsAbsent(),
            'excused' => $attendance->markAsExcused(),
            default => null,
        };

        session()->flash('status', 'Attendance status overridden.');
    }

    public function render()
    {
        $attendances = DriverAttendance::with(['driver', 'dispatchEntry.tripCode'])
            ->forDate($this->date)
            ->latest()
            ->paginate(15);

        $stats = [
            'total' => DriverAttendance::forDate($this->date)->count(),
            'pending' => DriverAttendance::forDate($this->date)->pending()->count(),
            'on_time' => DriverAttendance::forDate($this->date)->where('status', 'on_time')->count(),
            'late' => DriverAttendance::forDate($this->date)->late()->count(),
            'absent' => DriverAttendance::forDate($this->date)->absent()->count(),
            'excused' => DriverAttendance::forDate($this->date)->where('status', 'excused')->count(),
        ];

        return view('livewire.admin.attendance-table', compact('attendances', 'stats'));
    }
}
