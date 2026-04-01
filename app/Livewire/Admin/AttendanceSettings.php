<?php

namespace App\Livewire\Admin;

use App\Models\AttendanceSetting;
use Livewire\Component;

class AttendanceSettings extends Component
{
    public int $lateThreshold = 15;
    public int $preDepartureAlert = 15;
    public int $autoAbsentTimeout = 30;
    public bool $requireCheckIn = true;

    public function mount(): void
    {
        AttendanceSetting::initializeDefaults();

        $settings = AttendanceSetting::getAll();
        $this->lateThreshold = (int) ($settings['late_threshold_minutes'] ?? 15);
        $this->preDepartureAlert = (int) ($settings['pre_departure_alert_minutes'] ?? 15);
        $this->autoAbsentTimeout = (int) ($settings['auto_absent_timeout_minutes'] ?? 30);
        $this->requireCheckIn = (bool) ($settings['require_check_in'] ?? true);
    }

    public function updated($property): void
    {
        $map = [
            'lateThreshold' => 'late_threshold_minutes',
            'preDepartureAlert' => 'pre_departure_alert_minutes',
            'autoAbsentTimeout' => 'auto_absent_timeout_minutes',
            'requireCheckIn' => 'require_check_in',
        ];

        if (isset($map[$property])) {
            AttendanceSetting::set($map[$property], $this->{$property});
            session()->flash('status', 'Settings saved.');
        }
    }

    public function render()
    {
        return view('livewire.admin.attendance-settings');
    }
}
