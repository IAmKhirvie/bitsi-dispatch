<?php

namespace App\Console\Commands;

use App\Models\AttendanceAlert;
use App\Models\AttendanceSetting;
use App\Models\DispatchDay;
use App\Models\DispatchEntry;
use App\Models\DriverAttendance;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckAttendanceAlerts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:check-alerts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for attendance issues and create alerts';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $date = today()->toDateString();
        $currentTime = now()->format('H:i');

        // Initialize settings if not exists
        AttendanceSetting::initializeDefaults();

        // Get settings
        $preDepartureMinutes = (int) AttendanceSetting::get('pre_departure_alert_minutes', 15);
        $lateThresholdMinutes = (int) AttendanceSetting::get('late_threshold_minutes', 15);
        $autoAbsentTimeoutMinutes = (int) AttendanceSetting::get('auto_absent_timeout_minutes', 30);

        $this->info("Checking attendance alerts for {$date} at {$currentTime}");

        // Get today's dispatch day
        $dispatchDay = DispatchDay::whereDate('service_date', $date)->first();

        if (!$dispatchDay) {
            $this->info("No dispatch day found for {$date}");
            return self::SUCCESS;
        }

        $upcomingCount = 0;
        $lateCount = 0;
        $absentCount = 0;

        // Check for upcoming trips (no check-in yet, departure within pre-departure window)
        $this->checkUpcomingTrips($dispatchDay, $currentTime, $preDepartureMinutes, $upcomingCount);

        // Check for late drivers (past departure time, no check-in)
        $this->checkLateDrivers($dispatchDay, $currentTime, $lateThresholdMinutes, $lateCount);

        // Check for absent drivers (way past departure time)
        $this->checkAbsentDrivers($dispatchDay, $currentTime, $autoAbsentTimeoutMinutes, $absentCount);

        $this->info("Created {$upcomingCount} upcoming alerts, {$lateCount} late alerts, {$absentCount} absent alerts");

        return self::SUCCESS;
    }

    /**
     * Check for upcoming trips that need check-in reminder.
     */
    protected function checkUpcomingTrips(
        DispatchDay $dispatchDay,
        string $currentTime,
        int $preDepartureMinutes,
        int &$count
    ): void {
        // Calculate the time threshold for upcoming alerts
        $timeThreshold = now()->addMinutes($preDepartureMinutes)->format('H:i');

        $entries = DispatchEntry::where('dispatch_day_id', $dispatchDay->id)
            ->where('status', 'scheduled')
            ->where('scheduled_departure', '<=', $timeThreshold)
            ->where('scheduled_departure', '>=', $currentTime)
            ->whereNotNull('driver_id')
            ->whereDoesntHave('attendances', function ($query) use ($dispatchDay) {
                $query->where('attendance_date', $dispatchDay->service_date)
                    ->whereNotNull('check_in_time');
            })
            ->get();

        foreach ($entries as $entry) {
            $alertTime = now()->subMinutes(5)->format('H:i'); // Only alert every 5 minutes

            // Check if alert was already created recently
            $existingAlert = AttendanceAlert::where('dispatch_entry_id', $entry->id)
                ->where('driver_id', $entry->driver_id)
                ->where('alert_type', 'upcoming')
                ->where('alert_date', $dispatchDay->service_date)
                ->where('created_at', '>=', now()->subMinutes(5))
                ->first();

            if (!$existingAlert) {
                AttendanceAlert::createAlert(
                    $entry->driver_id,
                    $entry->id,
                    'upcoming',
                    $dispatchDay->service_date
                );
                $count++;
                $this->line("Upcoming alert created for driver {$entry->driver->name} - trip at {$entry->scheduled_departure}");
            }

            // Check for driver 2
            if ($entry->driver2_id) {
                $existingAlert2 = AttendanceAlert::where('dispatch_entry_id', $entry->id)
                    ->where('driver_id', $entry->driver2_id)
                    ->where('alert_type', 'upcoming')
                    ->where('alert_date', $dispatchDay->service_date)
                    ->where('created_at', '>=', now()->subMinutes(5))
                    ->first();

                if (!$existingAlert2) {
                    AttendanceAlert::createAlert(
                        $entry->driver2_id,
                        $entry->id,
                        'upcoming',
                        $dispatchDay->service_date
                    );
                    $count++;
                }
            }
        }
    }

    /**
     * Check for late drivers.
     */
    protected function checkLateDrivers(
        DispatchDay $dispatchDay,
        string $currentTime,
        int $lateThresholdMinutes,
        int &$count
    ): void {
        $entries = DispatchEntry::where('dispatch_day_id', $dispatchDay->id)
            ->where('status', 'scheduled')
            ->whereNotNull('driver_id')
            ->whereDoesntHave('attendances', function ($query) use ($dispatchDay) {
                $query->where('attendance_date', $dispatchDay->service_date)
                    ->whereNotNull('check_in_time');
            })
            ->get();

        foreach ($entries as $entry) {
            if (!$entry->scheduled_departure) {
                continue;
            }

            // Calculate how many minutes past scheduled time
            $scheduled = \Carbon\Carbon::parse($entry->scheduled_departure);
            $current = \Carbon\Carbon::parse($currentTime);
            $minutesPast = $scheduled->diffInMinutes($current, false);

            // Only mark as late if past the threshold
            if ($minutesPast >= $lateThresholdMinutes && $minutesPast < $lateThresholdMinutes + 60) {
                // Check if alert already exists
                $existingAlert = AttendanceAlert::where('dispatch_entry_id', $entry->id)
                    ->where('driver_id', $entry->driver_id)
                    ->where('alert_type', 'late')
                    ->where('alert_date', $dispatchDay->service_date)
                    ->first();

                if (!$existingAlert) {
                    AttendanceAlert::createAlert(
                        $entry->driver_id,
                        $entry->id,
                        'late',
                        $dispatchDay->service_date
                    );
                    $count++;
                    $this->line("Late alert created for driver {$entry->driver->name}");
                }
            }

            // Check for driver 2
            if ($entry->driver2_id) {
                $existingAlert2 = AttendanceAlert::where('dispatch_entry_id', $entry->id)
                    ->where('driver_id', $entry->driver2_id)
                    ->where('alert_type', 'late')
                    ->where('alert_date', $dispatchDay->service_date)
                    ->first();

                if (!$existingAlert2) {
                    AttendanceAlert::createAlert(
                        $entry->driver2_id,
                        $entry->id,
                        'late',
                        $dispatchDay->service_date
                    );
                    $count++;
                }
            }
        }
    }

    /**
     * Check for absent drivers.
     */
    protected function checkAbsentDrivers(
        DispatchDay $dispatchDay,
        string $currentTime,
        int $autoAbsentTimeoutMinutes,
        int &$count
    ): void {
        $entries = DispatchEntry::where('dispatch_day_id', $dispatchDay->id)
            ->where('status', 'scheduled')
            ->whereNotNull('driver_id')
            ->whereDoesntHave('attendances', function ($query) use ($dispatchDay) {
                $query->where('attendance_date', $dispatchDay->service_date)
                    ->whereNotNull('check_in_time');
            })
            ->get();

        foreach ($entries as $entry) {
            if (!$entry->scheduled_departure) {
                continue;
            }

            $scheduled = \Carbon\Carbon::parse($entry->scheduled_departure);
            $current = \Carbon\Carbon::parse($currentTime);
            $minutesPast = $scheduled->diffInMinutes($current, false);

            // Mark as absent if way past scheduled time
            if ($minutesPast >= $autoAbsentTimeoutMinutes) {
                $existingAlert = AttendanceAlert::where('dispatch_entry_id', $entry->id)
                    ->where('driver_id', $entry->driver_id)
                    ->where('alert_type', 'absent')
                    ->where('alert_date', $dispatchDay->service_date)
                    ->first();

                if (!$existingAlert) {
                    AttendanceAlert::createAlert(
                        $entry->driver_id,
                        $entry->id,
                        'absent',
                        $dispatchDay->service_date
                    );
                    $count++;

                    // Also create the attendance record as absent
                    DriverAttendance::updateOrCreate(
                        [
                            'driver_id' => $entry->driver_id,
                            'dispatch_entry_id' => $entry->id,
                            'attendance_date' => $dispatchDay->service_date,
                        ],
                        [
                            'status' => 'absent',
                            'minutes_late' => abs($minutesPast),
                        ]
                    );
                }
            }

            // Check for driver 2
            if ($entry->driver2_id) {
                $existingAlert2 = AttendanceAlert::where('dispatch_entry_id', $entry->id)
                    ->where('driver_id', $entry->driver2_id)
                    ->where('alert_type', 'absent')
                    ->where('alert_date', $dispatchDay->service_date)
                    ->first();

                if (!$existingAlert2) {
                    AttendanceAlert::createAlert(
                        $entry->driver2_id,
                        $entry->id,
                        'absent',
                        $dispatchDay->service_date
                    );
                    $count++;

                    DriverAttendance::updateOrCreate(
                        [
                            'driver_id' => $entry->driver2_id,
                            'dispatch_entry_id' => $entry->id,
                            'attendance_date' => $dispatchDay->service_date,
                        ],
                        [
                            'status' => 'absent',
                            'minutes_late' => abs($minutesPast),
                        ]
                    );
                }
            }
        }
    }
}
