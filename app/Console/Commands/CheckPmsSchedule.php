<?php

namespace App\Console\Commands;

use App\Enums\VehicleStatus;
use App\Models\Vehicle;
use Illuminate\Console\Command;

class CheckPmsSchedule extends Command
{
    protected $signature = 'pms:check';

    protected $description = 'Check vehicles with upcoming or overdue PMS schedule dates and flag them';

    public function handle(): int
    {
        $overdue = Vehicle::whereNotNull('next_pms_date')
            ->whereDate('next_pms_date', '<=', today())
            ->where('status', '!=', VehicleStatus::PMS)
            ->get();

        $flagged = 0;
        foreach ($overdue as $vehicle) {
            $vehicle->update(['status' => VehicleStatus::PMS]);
            $flagged++;
        }

        $approaching = Vehicle::whereNotNull('next_pms_date')
            ->whereDate('next_pms_date', '>', today())
            ->whereDate('next_pms_date', '<=', today()->addWeeks(2))
            ->count();

        $this->info("PMS Check: {$flagged} vehicle(s) flagged for maintenance, {$approaching} approaching.");

        return self::SUCCESS;
    }
}
