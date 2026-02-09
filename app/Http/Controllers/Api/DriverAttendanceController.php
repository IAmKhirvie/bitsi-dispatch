<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\DriverAttendance;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class DriverAttendanceController extends Controller
{
    /**
     * Check in the driver.
     */
    public function checkIn(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'dispatch_entry_id' => 'required|exists:dispatch_entries,id',
            'time' => 'nullable|date_format:H:i',
        ]);

        // For now, we'll use phone number to identify the driver
        // In production, this should use proper authentication
        $driver = $this->getDriverFromRequest($request);

        if (!$driver) {
            throw ValidationException::withMessages([
                'phone' => ['Driver not found or unauthorized.'],
            ]);
        }

        $date = today()->toDateString();
        $time = $validated['time'] ?? now()->format('H:i');

        // Find or create attendance record
        $attendance = DriverAttendance::firstOrCreate(
            [
                'driver_id' => $driver->id,
                'dispatch_entry_id' => $validated['dispatch_entry_id'],
                'attendance_date' => $date,
            ],
            ['status' => 'pending']
        );

        // Check if already checked in
        if ($attendance->check_in_time) {
            return response()->json([
                'success' => false,
                'message' => 'Already checked in at ' . $attendance->check_in_time,
            ]);
        }

        // Check in
        $attendance->checkIn($time);

        return response()->json([
            'success' => true,
            'message' => 'Checked in successfully at ' . $time,
            'attendance' => $attendance->load('dispatchEntry'),
        ]);
    }

    /**
     * Check out the driver.
     */
    public function checkOut(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'dispatch_entry_id' => 'required|exists:dispatch_entries,id',
            'time' => 'nullable|date_format:H:i',
        ]);

        $driver = $this->getDriverFromRequest($request);

        if (!$driver) {
            throw ValidationException::withMessages([
                'phone' => ['Driver not found or unauthorized.'],
            ]);
        }

        $date = today()->toDateString();
        $time = $validated['time'] ?? now()->format('H:i');

        $attendance = DriverAttendance::where('driver_id', $driver->id)
            ->where('dispatch_entry_id', $validated['dispatch_entry_id'])
            ->where('attendance_date', $date)
            ->first();

        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => 'No attendance record found. Please check in first.',
            ]);
        }

        if ($attendance->check_out_time) {
            return response()->json([
                'success' => false,
                'message' => 'Already checked out at ' . $attendance->check_out_time,
            ]);
        }

        $attendance->checkOut($time);

        return response()->json([
            'success' => true,
            'message' => 'Checked out successfully at ' . $time,
            'attendance' => $attendance->load('dispatchEntry'),
        ]);
    }

    /**
     * Get driver's upcoming trips.
     */
    public function mySchedule(Request $request): JsonResponse
    {
        $driver = $this->getDriverFromRequest($request);

        if (!$driver) {
            return response()->json([
                'success' => false,
                'message' => 'Driver not found or unauthorized.',
            ], 401);
        }

        $today = today()->toDateString();
        $dispatchDay = \App\Models\DispatchDay::whereDate('service_date', $today)->first();

        if (!$dispatchDay) {
            return response()->json([
                'success' => true,
                'trips' => [],
            ]);
        }

        $trips = \App\Models\DispatchEntry::where('dispatch_day_id', $dispatchDay->id)
            ->where(function ($query) use ($driver) {
                $query->where('driver_id', $driver->id)
                    ->orWhere('driver2_id', $driver->id);
            })
            ->with(['tripCode', 'vehicle'])
            ->orderBy('scheduled_departure')
            ->get();

        return response()->json([
            'success' => true,
            'trips' => $trips,
        ]);
    }

    /**
     * Get driver's attendance history.
     */
    public function myAttendance(Request $request): JsonResponse
    {
        $driver = $this->getDriverFromRequest($request);

        if (!$driver) {
            return response()->json([
                'success' => false,
                'message' => 'Driver not found or unauthorized.',
            ], 401);
        }

        $fromDate = $request->input('from_date', now()->subDays(7)->toDateString());
        $toDate = $request->input('to_date', today()->toDateString());

        $attendances = DriverAttendance::where('driver_id', $driver->id)
            ->whereBetween('attendance_date', [$fromDate, $toDate])
            ->with(['dispatchEntry', 'dispatchEntry.tripCode'])
            ->orderBy('attendance_date', 'desc')
            ->orderBy('check_in_time')
            ->get();

        return response()->json([
            'success' => true,
            'attendances' => $attendances,
        ]);
    }

    /**
     * Get driver from request using phone number (for mobile app).
     * In production, this should use proper API tokens/JWT.
     */
    protected function getDriverFromRequest(Request $request): ?Driver
    {
        $phone = $request->input('phone');

        if (!$phone) {
            return null;
        }

        return Driver::where('phone', $phone)
            ->where('is_active', true)
            ->first();
    }
}
