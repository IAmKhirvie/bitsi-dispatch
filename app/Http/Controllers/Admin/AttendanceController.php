<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceAlert;
use App\Models\AttendanceSetting;
use App\Models\DispatchDay;
use App\Models\DispatchEntry;
use App\Models\Driver;
use App\Models\DriverAttendance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    /**
     * Display attendance records list.
     */
    public function index(): View
    {
        return view('admin.attendances.index');
    }

    /**
     * Display attendance settings page.
     */
    public function settings(Request $request): View
    {
        AttendanceSetting::initializeDefaults();

        $settings = AttendanceSetting::getAll();

        return view('admin.settings.attendance-settings', [
            'settings' => [
                'late_threshold_minutes' => $settings['late_threshold_minutes'] ?? '15',
                'pre_departure_alert_minutes' => $settings['pre_departure_alert_minutes'] ?? '15',
                'auto_absent_timeout_minutes' => $settings['auto_absent_timeout_minutes'] ?? '30',
                'require_check_in' => $settings['require_check_in'] ?? 'true',
            ],
        ]);
    }

    /**
     * Update attendance settings.
     */
    public function updateSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'late_threshold_minutes' => 'required|integer|min:1|max:120',
            'pre_departure_alert_minutes' => 'required|integer|min:1|max:120',
            'auto_absent_timeout_minutes' => 'required|integer|min:1|max:240',
            'require_check_in' => 'boolean',
        ]);

        AttendanceSetting::set('late_threshold_minutes', $validated['late_threshold_minutes']);
        AttendanceSetting::set('pre_departure_alert_minutes', $validated['pre_departure_alert_minutes']);
        AttendanceSetting::set('auto_absent_timeout_minutes', $validated['auto_absent_timeout_minutes']);
        AttendanceSetting::set('require_check_in', $validated['require_check_in'] ? 'true' : 'false');

        return back()->with('success', 'Settings updated successfully.');
    }

    /**
     * Mark a driver as late for a trip.
     */
    public function markLate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'driver_id' => 'required|exists:drivers,id',
            'dispatch_entry_id' => 'required|exists:dispatch_entries,id',
            'minutes_late' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500',
        ]);

        $date = today()->toDateString();

        $attendance = DriverAttendance::updateOrCreate(
            [
                'driver_id' => $validated['driver_id'],
                'dispatch_entry_id' => $validated['dispatch_entry_id'],
                'attendance_date' => $date,
            ],
            [
                'status' => 'late',
                'minutes_late' => $validated['minutes_late'],
                'notes' => $validated['notes'] ?? null,
                'marked_by' => auth()->id(),
            ]
        );

        AttendanceAlert::createAlert(
            $validated['driver_id'],
            $validated['dispatch_entry_id'],
            'late',
            $date
        );

        return back()->with('success', 'Driver marked as late.');
    }

    /**
     * Mark a driver as absent for a trip.
     */
    public function markAbsent(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'driver_id' => 'required|exists:drivers,id',
            'dispatch_entry_id' => 'required|exists:dispatch_entries,id',
            'notes' => 'nullable|string|max:500',
        ]);

        $date = today()->toDateString();

        $attendance = DriverAttendance::updateOrCreate(
            [
                'driver_id' => $validated['driver_id'],
                'dispatch_entry_id' => $validated['dispatch_entry_id'],
                'attendance_date' => $date,
            ],
            [
                'status' => 'absent',
                'notes' => $validated['notes'] ?? null,
                'marked_by' => auth()->id(),
            ]
        );

        AttendanceAlert::createAlert(
            $validated['driver_id'],
            $validated['dispatch_entry_id'],
            'absent',
            $date
        );

        return back()->with('success', 'Driver marked as absent.');
    }

    /**
     * Override attendance record.
     */
    public function override(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'attendance_id' => 'required|exists:driver_attendances,id',
            'status' => 'required|in:on_time,late,absent,excused',
            'minutes_late' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $attendance = DriverAttendance::find($validated['attendance_id']);
        $attendance->update([
            'status' => $validated['status'],
            'minutes_late' => $validated['minutes_late'] ?? 0,
            'notes' => $validated['notes'] ?? $attendance->notes,
            'marked_by' => auth()->id(),
        ]);

        return back()->with('success', 'Attendance record updated.');
    }

    /**
     * Get unread alerts for dashboard.
     */
    public function getAlerts(Request $request): JsonResponse
    {
        $alerts = AttendanceAlert::with(['driver', 'dispatchEntry', 'dispatchEntry.tripCode'])
            ->unread()
            ->forDate(today()->toDateString())
            ->latest()
            ->limit(10)
            ->get();

        $unreadCount = AttendanceAlert::unread()->forDate(today()->toDateString())->count();

        return response()->json([
            'alerts' => $alerts,
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Mark alerts as read.
     */
    public function markAlertsRead(Request $request): RedirectResponse
    {
        $alertIds = $request->input('alert_ids', []);

        AttendanceAlert::whereIn('id', $alertIds)->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return back()->with('success', 'Alerts marked as read.');
    }

    /**
     * Get pending attendance for today (drivers with no check-in).
     */
    public function getPendingAttendance(Request $request): JsonResponse
    {
        $date = today()->toDateString();
        $dispatchDay = DispatchDay::whereDate('service_date', $date)->first();

        if (!$dispatchDay) {
            return response()->json([
                'pending' => [],
            ]);
        }

        // Get entries with scheduled departure in the next 2 hours
        $twoHoursFromNow = now()->addHours(2)->format('H:i');

        $entries = DispatchEntry::where('dispatch_day_id', $dispatchDay->id)
            ->where('status', 'scheduled')
            ->where('scheduled_departure', '<=', $twoHoursFromNow)
            ->where(function ($query) {
                $query->whereNotNull('driver_id')
                    ->orWhereNotNull('driver2_id');
            })
            ->with(['driver', 'driver2', 'tripCode'])
            ->orderBy('scheduled_departure')
            ->get();

        $pending = [];

        foreach ($entries as $entry) {
            if ($entry->driver_id && !$this->hasCheckedIn($entry->driver_id, $entry->id, $date)) {
                $pending[] = [
                    'entry' => $entry,
                    'driver' => $entry->driver,
                    'type' => 'driver',
                ];
            }

            if ($entry->driver2_id && !$this->hasCheckedIn($entry->driver2_id, $entry->id, $date)) {
                $pending[] = [
                    'entry' => $entry,
                    'driver' => $entry->driver2,
                    'type' => 'driver2',
                ];
            }
        }

        return response()->json([
            'pending' => $pending,
        ]);
    }

    /**
     * Check if driver has checked in for a dispatch entry.
     */
    protected function hasCheckedIn(int $driverId, int $entryId, string $date): bool
    {
        return DriverAttendance::where('driver_id', $driverId)
            ->where('dispatch_entry_id', $entryId)
            ->where('attendance_date', $date)
            ->whereNotNull('check_in_time')
            ->exists();
    }

    /**
     * Initialize attendance records for today's dispatch entries.
     */
    public function initializeToday(): RedirectResponse
    {
        $date = today()->toDateString();
        $dispatchDay = DispatchDay::whereDate('service_date', $date)->first();

        if (!$dispatchDay) {
            return back()->with('error', 'No dispatch day found for today.');
        }

        $count = 0;

        foreach ($dispatchDay->entries as $entry) {
            if ($entry->driver_id) {
                DriverAttendance::firstOrCreate(
                    [
                        'driver_id' => $entry->driver_id,
                        'dispatch_entry_id' => $entry->id,
                        'attendance_date' => $date,
                    ],
                    ['status' => 'pending']
                );
                $count++;
            }

            if ($entry->driver2_id) {
                DriverAttendance::firstOrCreate(
                    [
                        'driver_id' => $entry->driver2_id,
                        'dispatch_entry_id' => $entry->id,
                        'attendance_date' => $date,
                    ],
                    ['status' => 'pending']
                );
                $count++;
            }
        }

        return back()->with('success', "Initialized {$count} attendance records for today.");
    }
}
