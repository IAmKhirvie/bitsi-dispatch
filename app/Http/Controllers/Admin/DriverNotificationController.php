<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DispatchDay;
use App\Models\DispatchEntry;
use App\Models\Driver;
use App\Jobs\SendSmsJob;
use App\Services\SemaphoreService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DriverNotificationController extends Controller
{
    public function __construct(
        private SemaphoreService $smsService
    ) {}

    /**
     * Send driver their schedule for today via SMS
     */
    public function sendScheduleSms(Request $request, Driver $driver): RedirectResponse
    {
        if (!$driver->phone) {
            return back()->with('error', "Driver {$driver->name} has no phone number.");
        }

        $today = today()->toDateString();
        $dispatchDay = DispatchDay::whereDate('service_date', $today)->first();

        if (!$dispatchDay) {
            return back()->with('error', "No dispatch entries found for today ({$today}).");
        }

        // Get all dispatch entries for this driver today (as driver1 or driver2)
        $entries = DispatchEntry::where('dispatch_day_id', $dispatchDay->id)
            ->where(function ($query) use ($driver) {
                $query->where('driver_id', $driver->id)
                    ->orWhere('driver2_id', $driver->id);
            })
            ->with(['tripCode', 'vehicle'])
            ->orderBy('scheduled_departure')
            ->get();

        if ($entries->isEmpty()) {
            return back()->with('error', "Driver {$driver->name} has no scheduled trips for today.");
        }

        // Build SMS message with schedule
        $message = $this->buildScheduleMessage($driver, $entries, $today);

        // Send SMS via queue
        SendSmsJob::dispatch($driver->phone, $message, null)->onQueue('high');

        return back()->with('success', "Schedule SMS sent to {$driver->name}.");
    }

    /**
     * Send custom message to driver via SMS
     */
    public function sendCustomSms(Request $request, Driver $driver): RedirectResponse
    {
        $request->validate([
            'message' => 'required|string|max:160',
        ]);

        if (!$driver->phone) {
            return back()->with('error', "Driver {$driver->name} has no phone number.");
        }

        $message = $request->input('message');

        // Send SMS via queue
        SendSmsJob::dispatch($driver->phone, $message, null)->onQueue('high');

        return back()->with('success', "SMS sent to {$driver->name}.");
    }

    /**
     * Get driver's schedule for preview in modal
     */
    public function getSchedulePreview(Request $request, Driver $driver): JsonResponse
    {
        $today = today()->toDateString();
        $dispatchDay = DispatchDay::whereDate('service_date', $today)->first();

        if (!$dispatchDay) {
            return response()->json([
                'success' => false,
                'message' => 'No dispatch entries found for today.',
            ]);
        }

        $entries = DispatchEntry::where('dispatch_day_id', $dispatchDay->id)
            ->where(function ($query) use ($driver) {
                $query->where('driver_id', $driver->id)
                    ->orWhere('driver2_id', $driver->id);
            })
            ->with(['tripCode', 'vehicle'])
            ->orderBy('scheduled_departure')
            ->get();

        if ($entries->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Driver has no scheduled trips for today.',
            ]);
        }

        return response()->json([
            'success' => true,
            'driver' => $driver,
            'entries' => $entries,
            'message_preview' => $this->buildScheduleMessage($driver, $entries, $today),
        ]);
    }

    /**
     * Build SMS message with driver's schedule
     */
    private function buildScheduleMessage(Driver $driver, $entries, string $date): string
    {
        $lines = [];
        $lines[] = "BITSI Dispatch - Schedule for {$date}:";
        $lines[] = "";

        foreach ($entries as $index => $entry) {
            $tripNum = $index + 1;
            $time = $entry->scheduled_departure ?? 'TBD';
            $route = $entry->route ?? ($entry->tripCode?->route_display ?? 'TBD');
            $bus = $entry->vehicle?->bus_number ?? 'TBD';
            $status = strtoupper($entry->status ?? 'SCHEDULED');

            $lines[] = "{$tripNum}. {$time} - {$route}";
            $lines[] = "   Bus: {$bus} | Status: {$status}";
        }

        $lines[] = "";
        $lines[] = "Please report on time. Thank you!";

        return implode("\n", $lines);
    }
}
