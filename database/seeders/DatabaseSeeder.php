<?php

namespace Database\Seeders;

use App\Enums\BusType;
use App\Enums\Direction;
use App\Enums\DispatchStatus;
use App\Enums\PmsUnit;
use App\Enums\UserRole;
use App\Enums\VehicleStatus;
use App\Models\DailySummary;
use App\Models\DailySummaryItem;
use App\Models\DispatchDay;
use App\Models\DispatchEntry;
use App\Models\Driver;
use App\Models\PmsSetting;
use App\Models\TripCode;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Users
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@bitsi.com',
            'password' => bcrypt('password'),
            'role' => UserRole::Admin,
            'phone' => '09171234567',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Operations Manager',
            'email' => 'opsmanager@bitsi.com',
            'password' => bcrypt('password'),
            'role' => UserRole::OperationsManager,
            'phone' => '09172345678',
            'is_active' => true,
        ]);

        $dispatcher = User::create([
            'name' => 'Dispatcher',
            'email' => 'dispatcher@bitsi.com',
            'password' => bcrypt('password'),
            'role' => UserRole::Dispatcher,
            'phone' => '09173456789',
            'is_active' => true,
        ]);

        // Drivers
        $drivers = collect([
            ['name' => 'Juan Dela Cruz', 'phone' => '09181111111', 'license_number' => 'N01-12-345678'],
            ['name' => 'Pedro Santos', 'phone' => '09182222222', 'license_number' => 'N02-13-456789'],
            ['name' => 'Mario Reyes', 'phone' => '09183333333', 'license_number' => 'N03-14-567890'],
            ['name' => 'Carlos Garcia', 'phone' => '09184444444', 'license_number' => 'N04-15-678901'],
            ['name' => 'Roberto Cruz', 'phone' => '09185555555', 'license_number' => 'N05-16-789012'],
        ])->map(fn($d) => Driver::create($d));

        // Vehicles
        $vehicles = collect([
            ['bus_number' => '1001', 'brand' => 'Hino', 'bus_type' => BusType::Regular, 'plate_number' => 'ABC 1234', 'status' => VehicleStatus::OK, 'pms_threshold' => 15000, 'current_pms_value' => 5000],
            ['bus_number' => '1002', 'brand' => 'Hino', 'bus_type' => BusType::Deluxe, 'plate_number' => 'DEF 5678', 'status' => VehicleStatus::OK, 'pms_threshold' => 15000, 'current_pms_value' => 12000],
            ['bus_number' => '1003', 'brand' => 'Yutong', 'bus_type' => BusType::SuperDeluxe, 'plate_number' => 'GHI 9012', 'status' => VehicleStatus::OK, 'pms_threshold' => 20000, 'current_pms_value' => 3000],
            ['bus_number' => '1004', 'brand' => 'Yutong', 'bus_type' => BusType::Elite, 'plate_number' => 'JKL 3456', 'status' => VehicleStatus::UR, 'pms_threshold' => 20000, 'current_pms_value' => 18000],
            ['bus_number' => '1005', 'brand' => 'Hino', 'bus_type' => BusType::Sleeper, 'plate_number' => 'MNO 7890', 'status' => VehicleStatus::OK, 'pms_threshold' => 15000, 'current_pms_value' => 7500],
            ['bus_number' => '1006', 'brand' => 'Daewoo', 'bus_type' => BusType::SkyBus, 'plate_number' => 'PQR 1234', 'status' => VehicleStatus::PMS, 'pms_threshold' => 15000, 'current_pms_value' => 15000],
            ['bus_number' => '1007', 'brand' => 'Hino', 'bus_type' => BusType::Regular, 'plate_number' => 'STU 5678', 'status' => VehicleStatus::InTransit, 'pms_threshold' => 15000, 'current_pms_value' => 9000],
            ['bus_number' => '1008', 'brand' => 'Yutong', 'bus_type' => BusType::Deluxe, 'plate_number' => 'VWX 9012', 'status' => VehicleStatus::Lutaw, 'idle_days' => 5],
            ['bus_number' => '1009', 'brand' => 'Daewoo', 'bus_type' => BusType::SingleSeater, 'plate_number' => 'YZA 3456', 'status' => VehicleStatus::OK, 'pms_threshold' => 12000, 'current_pms_value' => 2000],
            ['bus_number' => '1010', 'brand' => 'Hino', 'bus_type' => BusType::Regular, 'plate_number' => 'BCD 7890', 'status' => VehicleStatus::OK, 'pms_threshold' => 15000, 'current_pms_value' => 10500],
        ])->map(fn($v) => Vehicle::create($v));

        // Trip Codes — Southbound (Cubao → Bicol destinations)
        $tripCodes = collect([
            ['code' => 'SB-001', 'operator' => 'BITSI', 'origin_terminal' => 'Cubao', 'destination_terminal' => 'Naga', 'bus_type' => BusType::Regular, 'scheduled_departure_time' => '06:00', 'direction' => Direction::SB],
            ['code' => 'SB-002', 'operator' => 'BITSI', 'origin_terminal' => 'Cubao', 'destination_terminal' => 'Naga', 'bus_type' => BusType::Deluxe, 'scheduled_departure_time' => '08:00', 'direction' => Direction::SB],
            ['code' => 'SB-003', 'operator' => 'BITSI', 'origin_terminal' => 'Cubao', 'destination_terminal' => 'Legazpi', 'bus_type' => BusType::Regular, 'scheduled_departure_time' => '07:00', 'direction' => Direction::SB],
            ['code' => 'SB-004', 'operator' => 'BITSI', 'origin_terminal' => 'Cubao', 'destination_terminal' => 'Legazpi', 'bus_type' => BusType::SuperDeluxe, 'scheduled_departure_time' => '20:00', 'direction' => Direction::SB],
            ['code' => 'SB-005', 'operator' => 'BITSI', 'origin_terminal' => 'Cubao', 'destination_terminal' => 'Sorsogon', 'bus_type' => BusType::Regular, 'scheduled_departure_time' => '18:00', 'direction' => Direction::SB],
            ['code' => 'SB-006', 'operator' => 'BITSI', 'origin_terminal' => 'Cubao', 'destination_terminal' => 'Virac', 'bus_type' => BusType::Deluxe, 'scheduled_departure_time' => '19:00', 'direction' => Direction::SB],
            ['code' => 'SB-007', 'operator' => 'BITSI', 'origin_terminal' => 'Cubao', 'destination_terminal' => 'Tabaco', 'bus_type' => BusType::Regular, 'scheduled_departure_time' => '21:00', 'direction' => Direction::SB],
            ['code' => 'SB-008', 'operator' => 'BITSI', 'origin_terminal' => 'Naga', 'destination_terminal' => 'Masbate', 'bus_type' => BusType::Regular, 'scheduled_departure_time' => '05:00', 'direction' => Direction::SB],
            ['code' => 'SB-009', 'operator' => 'BITSI', 'origin_terminal' => 'Cubao', 'destination_terminal' => 'Tacloban', 'bus_type' => BusType::Elite, 'scheduled_departure_time' => '17:00', 'direction' => Direction::SB],
            ['code' => 'SB-010', 'operator' => 'BITSI', 'origin_terminal' => 'Cubao', 'destination_terminal' => 'Naga', 'bus_type' => BusType::Sleeper, 'scheduled_departure_time' => '22:00', 'direction' => Direction::SB],
            // Northbound (Bicol → Cubao)
            ['code' => 'NB-001', 'operator' => 'BITSI', 'origin_terminal' => 'Naga', 'destination_terminal' => 'Cubao', 'bus_type' => BusType::Regular, 'scheduled_departure_time' => '06:00', 'direction' => Direction::NB],
            ['code' => 'NB-002', 'operator' => 'BITSI', 'origin_terminal' => 'Naga', 'destination_terminal' => 'Cubao', 'bus_type' => BusType::Deluxe, 'scheduled_departure_time' => '08:00', 'direction' => Direction::NB],
            ['code' => 'NB-003', 'operator' => 'BITSI', 'origin_terminal' => 'Legazpi', 'destination_terminal' => 'Cubao', 'bus_type' => BusType::Regular, 'scheduled_departure_time' => '07:00', 'direction' => Direction::NB],
            ['code' => 'NB-004', 'operator' => 'BITSI', 'origin_terminal' => 'Legazpi', 'destination_terminal' => 'Cubao', 'bus_type' => BusType::SuperDeluxe, 'scheduled_departure_time' => '20:00', 'direction' => Direction::NB],
            ['code' => 'NB-005', 'operator' => 'BITSI', 'origin_terminal' => 'Sorsogon', 'destination_terminal' => 'Cubao', 'bus_type' => BusType::Regular, 'scheduled_departure_time' => '18:00', 'direction' => Direction::NB],
            ['code' => 'NB-006', 'operator' => 'BITSI', 'origin_terminal' => 'Virac', 'destination_terminal' => 'Cubao', 'bus_type' => BusType::Deluxe, 'scheduled_departure_time' => '19:00', 'direction' => Direction::NB],
            ['code' => 'NB-007', 'operator' => 'BITSI', 'origin_terminal' => 'Tabaco', 'destination_terminal' => 'Cubao', 'bus_type' => BusType::Regular, 'scheduled_departure_time' => '21:00', 'direction' => Direction::NB],
            ['code' => 'NB-008', 'operator' => 'BITSI', 'origin_terminal' => 'Masbate', 'destination_terminal' => 'Naga', 'bus_type' => BusType::Regular, 'scheduled_departure_time' => '05:00', 'direction' => Direction::NB],
            ['code' => 'NB-009', 'operator' => 'BITSI', 'origin_terminal' => 'Tacloban', 'destination_terminal' => 'Cubao', 'bus_type' => BusType::Elite, 'scheduled_departure_time' => '17:00', 'direction' => Direction::NB],
            ['code' => 'NB-010', 'operator' => 'BITSI', 'origin_terminal' => 'Naga', 'destination_terminal' => 'Cubao', 'bus_type' => BusType::Sleeper, 'scheduled_departure_time' => '22:00', 'direction' => Direction::NB],
        ])->map(fn($tc) => TripCode::create($tc));

        // PMS Settings
        PmsSetting::create(['name' => 'Standard PMS (15,000 km)', 'unit' => PmsUnit::Kilometers, 'threshold' => 15000, 'description' => 'Standard preventive maintenance every 15,000 km', 'is_default' => true]);
        PmsSetting::create(['name' => 'Heavy Duty PMS (20,000 km)', 'unit' => PmsUnit::Kilometers, 'threshold' => 20000, 'description' => 'For heavy-duty vehicles']);
        PmsSetting::create(['name' => 'Trip-based PMS (500 trips)', 'unit' => PmsUnit::Trips, 'threshold' => 500, 'description' => 'Maintenance based on trip count']);

        // Sample Dispatch Day for today
        $dispatchDay = DispatchDay::create([
            'service_date' => today(),
            'created_by' => $dispatcher->id,
            'notes' => 'Sample dispatch day',
        ]);

        // Sample dispatch entries
        $sampleEntries = [
            ['vehicle' => $vehicles[0], 'trip_code' => $tripCodes[0], 'driver' => $drivers[0], 'status' => DispatchStatus::Departed, 'actual_departure' => '06:15'],
            ['vehicle' => $vehicles[1], 'trip_code' => $tripCodes[1], 'driver' => $drivers[1], 'status' => DispatchStatus::OnRoute, 'actual_departure' => '08:05'],
            ['vehicle' => $vehicles[2], 'trip_code' => $tripCodes[2], 'driver' => $drivers[2], 'status' => DispatchStatus::Scheduled, 'actual_departure' => null],
            ['vehicle' => $vehicles[4], 'trip_code' => $tripCodes[3], 'driver' => $drivers[3], 'status' => DispatchStatus::Scheduled, 'actual_departure' => null],
            ['vehicle' => $vehicles[8], 'trip_code' => $tripCodes[4], 'driver' => $drivers[4], 'status' => DispatchStatus::Scheduled, 'actual_departure' => null],
            ['vehicle' => $vehicles[9], 'trip_code' => $tripCodes[10], 'driver' => $drivers[0], 'status' => DispatchStatus::Arrived, 'actual_departure' => '06:10'],
            ['vehicle' => $vehicles[0], 'trip_code' => $tripCodes[8], 'driver' => $drivers[1], 'status' => DispatchStatus::Scheduled, 'actual_departure' => null],
            ['vehicle' => $vehicles[1], 'trip_code' => $tripCodes[11], 'driver' => $drivers[2], 'status' => DispatchStatus::Delayed, 'actual_departure' => null, 'remarks' => 'Mechanical issue, delayed 30 minutes'],
        ];

        foreach ($sampleEntries as $index => $entry) {
            $tc = $entry['trip_code'];
            DispatchEntry::create([
                'dispatch_day_id' => $dispatchDay->id,
                'vehicle_id' => $entry['vehicle']->id,
                'trip_code_id' => $tc->id,
                'driver_id' => $entry['driver']->id,
                'brand' => $entry['vehicle']->brand,
                'bus_number' => $entry['vehicle']->bus_number,
                'route' => "{$tc->origin_terminal} → {$tc->destination_terminal}",
                'bus_type' => $tc->bus_type->value,
                'departure_terminal' => $tc->origin_terminal,
                'arrival_terminal' => $tc->destination_terminal,
                'scheduled_departure' => $tc->scheduled_departure_time,
                'actual_departure' => $entry['actual_departure'],
                'direction' => $tc->direction->value,
                'status' => $entry['status'],
                'remarks' => $entry['remarks'] ?? null,
                'sort_order' => $index,
            ]);
        }

        // Daily summary (use updateOrCreate since observer auto-generates on entry creation)
        $summary = DailySummary::updateOrCreate(
            ['dispatch_day_id' => $dispatchDay->id],
            ['total_trips' => 8]
        );

        $summaryItems = [
            'sb' => 5, 'nb' => 3, 'naga' => 3, 'legazpi' => 2,
            'sorsogon' => 1, 'virac' => 0, 'masbate' => 0,
            'tabaco' => 0, 'visayas' => 1, 'cargo' => 0,
        ];

        foreach ($summaryItems as $category => $count) {
            DailySummaryItem::updateOrCreate(
                ['daily_summary_id' => $summary->id, 'category' => $category],
                ['trip_count' => $count]
            );
        }
    }
}
