<?php

namespace Database\Seeders;

use App\Enums\BusType;
use App\Enums\Direction;
use App\Models\TripCode;
use Illuminate\Database\Seeder;

class TripScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $trips = $this->getTripSchedule();

        foreach ($trips as $trip) {
            TripCode::updateOrCreate(
                [
                    'code' => $trip['code'],
                    'direction' => $trip['direction'],
                ],
                $trip
            );
        }
    }

    /**
     * Hardcoded trip schedule data from BITSI Trip Schedule.
     * Sheet 320 & 321 — Trip codes with service class, origin, destination, and direction.
     * Departure times are assigned in sequential order for dispatch planning.
     */
    private function getTripSchedule(): array
    {
        $sb = [];
        $nb = [];

        // =========================================================================
        // SOUTH BOUND — Sheet 320
        // =========================================================================

        // --- Visayas Routes (Tacloban, Leyte) ---
        $sb[] = ['code' => 'P3',     'operator' => 'BITSI', 'origin_terminal' => 'EDSA Cubao',       'destination_terminal' => 'Abuyog',               'bus_type' => BusType::Executive, 'scheduled_departure_time' => '05:00', 'direction' => Direction::SB, 'is_active' => false];
        $sb[] = ['code' => 'P4',     'operator' => 'BITSI', 'origin_terminal' => 'PITX',              'destination_terminal' => 'Burauen',              'bus_type' => BusType::Executive, 'scheduled_departure_time' => '05:30', 'direction' => Direction::SB, 'is_active' => true];
        $sb[] = ['code' => 'P1',     'operator' => 'BITSI', 'origin_terminal' => 'EDSA Cubao',       'destination_terminal' => 'Tacloban',             'bus_type' => BusType::Executive, 'scheduled_departure_time' => '06:00', 'direction' => Direction::SB, 'is_active' => true];

        // --- Legazpi Routes (Royal) ---
        $sb[] = ['code' => 'Q',      'operator' => 'BITSI', 'origin_terminal' => 'EDSA Cubao',       'destination_terminal' => 'Legazpi',              'bus_type' => BusType::Royal,     'scheduled_departure_time' => '06:30', 'direction' => Direction::SB, 'is_active' => true];
        $sb[] = ['code' => 'D',      'operator' => 'BITSI', 'origin_terminal' => 'PITX',              'destination_terminal' => 'Legazpi',              'bus_type' => BusType::Royal,     'scheduled_departure_time' => '07:00', 'direction' => Direction::SB, 'is_active' => true];
        $sb[] = ['code' => 'B',      'operator' => 'BITSI', 'origin_terminal' => 'Arcovia Pasig',    'destination_terminal' => 'Legazpi',              'bus_type' => BusType::Royal,     'scheduled_departure_time' => '07:30', 'direction' => Direction::SB, 'is_active' => true];
        $sb[] = ['code' => 'S',      'operator' => 'BITSI', 'origin_terminal' => 'PITX',              'destination_terminal' => 'Legazpi',              'bus_type' => BusType::Royal,     'scheduled_departure_time' => '08:00', 'direction' => Direction::SB, 'is_active' => true];
        $sb[] = ['code' => 'B001',   'operator' => 'BITSI', 'origin_terminal' => 'Arcovia Pasig',    'destination_terminal' => 'Legazpi',              'bus_type' => BusType::Royal,     'scheduled_departure_time' => '08:30', 'direction' => Direction::SB, 'is_active' => false];
        $sb[] = ['code' => 'EXELEG', 'operator' => 'BITSI', 'origin_terminal' => 'PITX',              'destination_terminal' => 'Legazpi',              'bus_type' => BusType::Executive, 'scheduled_departure_time' => '09:00', 'direction' => Direction::SB, 'is_active' => true];

        // --- Naga CBD Routes (Royal) ---
        $sb[] = ['code' => 'K',      'operator' => 'BITSI', 'origin_terminal' => 'EDSA Cubao',       'destination_terminal' => 'Naga CBD',             'bus_type' => BusType::Royal,     'scheduled_departure_time' => '09:30', 'direction' => Direction::SB, 'is_active' => true];
        $sb[] = ['code' => 'K1',     'operator' => 'BITSI', 'origin_terminal' => 'PITX',              'destination_terminal' => 'Naga CBD',             'bus_type' => BusType::Royal,     'scheduled_departure_time' => '10:00', 'direction' => Direction::SB, 'is_active' => true];
        $sb[] = ['code' => 'F',      'operator' => 'BITSI', 'origin_terminal' => 'Arcovia Pasig',    'destination_terminal' => 'Naga CBD',             'bus_type' => BusType::Royal,     'scheduled_departure_time' => '10:30', 'direction' => Direction::SB, 'is_active' => true];
        $sb[] = ['code' => 'F001',   'operator' => 'BITSI', 'origin_terminal' => 'Arcovia Pasig',    'destination_terminal' => 'Naga CBD',             'bus_type' => BusType::Royal,     'scheduled_departure_time' => '11:00', 'direction' => Direction::SB, 'is_active' => false];
        $sb[] = ['code' => 'SN',     'operator' => 'BITSI', 'origin_terminal' => 'Arcovia Pasig',    'destination_terminal' => 'Naga CBD',             'bus_type' => BusType::Royal,     'scheduled_departure_time' => '11:30', 'direction' => Direction::SB, 'is_active' => false];
        $sb[] = ['code' => 'T',      'operator' => 'BITSI', 'origin_terminal' => 'EDSA Cubao',       'destination_terminal' => 'Naga CBD',             'bus_type' => BusType::Royal,     'scheduled_departure_time' => '12:00', 'direction' => Direction::SB, 'is_active' => true];
        $sb[] = ['code' => 'L001',   'operator' => 'BITSI', 'origin_terminal' => 'Arcovia Pasig',    'destination_terminal' => 'Naga CBD',             'bus_type' => BusType::Royal,     'scheduled_departure_time' => '12:30', 'direction' => Direction::SB, 'is_active' => false];
        $sb[] = ['code' => 'L002',   'operator' => 'BITSI', 'origin_terminal' => 'EDSA Cubao',       'destination_terminal' => 'Naga CBD',             'bus_type' => BusType::Royal,     'scheduled_departure_time' => '13:00', 'direction' => Direction::SB, 'is_active' => true];

        // --- Masbate / Bicol Masbate Routes ---
        $sb[] = ['code' => 'M',      'operator' => 'BITSI', 'origin_terminal' => 'EDSA Cubao',       'destination_terminal' => 'Masbate City',         'bus_type' => BusType::Royal,     'scheduled_departure_time' => '13:30', 'direction' => Direction::SB, 'is_active' => true];
        $sb[] = ['code' => '20',     'operator' => 'BITSI', 'origin_terminal' => 'EDSA Cubao',       'destination_terminal' => 'Aroroy',               'bus_type' => BusType::Executive, 'scheduled_departure_time' => '14:00', 'direction' => Direction::SB, 'is_active' => true];
        $sb[] = ['code' => '21',     'operator' => 'BITSI', 'origin_terminal' => 'EDSA Cubao',       'destination_terminal' => 'Cataingan',            'bus_type' => BusType::Executive, 'scheduled_departure_time' => '14:30', 'direction' => Direction::SB, 'is_active' => true];
        $sb[] = ['code' => '22',     'operator' => 'BITSI', 'origin_terminal' => 'EDSA Cubao',       'destination_terminal' => 'Mandaon',              'bus_type' => BusType::Executive, 'scheduled_departure_time' => '15:00', 'direction' => Direction::SB, 'is_active' => true];
        $sb[] = ['code' => '23A',    'operator' => 'BITSI', 'origin_terminal' => 'EDSA Cubao',       'destination_terminal' => 'Placer',               'bus_type' => BusType::Executive, 'scheduled_departure_time' => '15:30', 'direction' => Direction::SB, 'is_active' => true];
        $sb[] = ['code' => '26E',    'operator' => 'BITSI', 'origin_terminal' => 'EDSA Cubao',       'destination_terminal' => 'Placer',               'bus_type' => BusType::Economy,   'scheduled_departure_time' => '16:00', 'direction' => Direction::SB, 'is_active' => true];
        $sb[] = ['code' => '27',     'operator' => 'BITSI', 'origin_terminal' => 'EDSA Cubao',       'destination_terminal' => 'Cawayan',              'bus_type' => BusType::Executive, 'scheduled_departure_time' => '16:30', 'direction' => Direction::SB, 'is_active' => false];
        $sb[] = ['code' => '28',     'operator' => 'BITSI', 'origin_terminal' => 'EDSA Cubao',       'destination_terminal' => 'Aroroy',               'bus_type' => BusType::Economy,   'scheduled_departure_time' => '17:00', 'direction' => Direction::SB, 'is_active' => false];
        $sb[] = ['code' => '29',     'operator' => 'BITSI', 'origin_terminal' => 'EDSA Cubao',       'destination_terminal' => 'Balud',                'bus_type' => BusType::Executive, 'scheduled_departure_time' => '17:30', 'direction' => Direction::SB, 'is_active' => false];
        $sb[] = ['code' => '31',     'operator' => 'BITSI', 'origin_terminal' => 'EDSA Cubao',       'destination_terminal' => 'Esperanza',            'bus_type' => BusType::Executive, 'scheduled_departure_time' => '18:00', 'direction' => Direction::SB, 'is_active' => false];
        $sb[] = ['code' => '27C',    'operator' => 'BITSI', 'origin_terminal' => 'PITX',              'destination_terminal' => 'Cawayan',              'bus_type' => BusType::Executive, 'scheduled_departure_time' => '18:30', 'direction' => Direction::SB, 'is_active' => false];
        $sb[] = ['code' => '29B',    'operator' => 'BITSI', 'origin_terminal' => 'PITX',              'destination_terminal' => 'Balud',                'bus_type' => BusType::Executive, 'scheduled_departure_time' => '19:00', 'direction' => Direction::SB, 'is_active' => false];

        // --- Gubat / Sorsogon Routes ---
        $sb[] = ['code' => 'Z5',     'operator' => 'BITSI', 'origin_terminal' => 'EDSA Cubao',       'destination_terminal' => 'Gubat',                'bus_type' => BusType::RoyalExe,  'scheduled_departure_time' => '19:30', 'direction' => Direction::SB, 'is_active' => true];
        $sb[] = ['code' => 'N',      'operator' => 'BITSI', 'origin_terminal' => 'EDSA Cubao',       'destination_terminal' => 'Gubat',                'bus_type' => BusType::Royal,     'scheduled_departure_time' => '20:00', 'direction' => Direction::SB, 'is_active' => true];
        $sb[] = ['code' => '10',     'operator' => 'BITSI', 'origin_terminal' => 'EDSA Cubao',       'destination_terminal' => 'Gubat',                'bus_type' => BusType::Executive, 'scheduled_departure_time' => '20:30', 'direction' => Direction::SB, 'is_active' => true];
        $sb[] = ['code' => 'Z8',     'operator' => 'BITSI', 'origin_terminal' => 'PITX',              'destination_terminal' => 'Gubat',                'bus_type' => BusType::RoyalExe,  'scheduled_departure_time' => '21:00', 'direction' => Direction::SB, 'is_active' => true];

        // --- Naga CBD Routes (RoyalExe, Premier, etc.) ---
        $sb[] = ['code' => 'Z3',     'operator' => 'BITSI', 'origin_terminal' => 'EDSA Cubao',       'destination_terminal' => 'Naga CBD',             'bus_type' => BusType::RoyalExe,  'scheduled_departure_time' => '21:30', 'direction' => Direction::SB, 'is_active' => true];
        $sb[] = ['code' => 'A',      'operator' => 'BITSI', 'origin_terminal' => 'EDSA Cubao',       'destination_terminal' => 'Naga CBD',             'bus_type' => BusType::Royal,     'scheduled_departure_time' => '22:00', 'direction' => Direction::SB, 'is_active' => false];
        $sb[] = ['code' => 'C',      'operator' => 'BITSI', 'origin_terminal' => 'PITX',              'destination_terminal' => 'Naga CBD',             'bus_type' => BusType::Royal,     'scheduled_departure_time' => '22:30', 'direction' => Direction::SB, 'is_active' => true];
        $sb[] = ['code' => 'E',      'operator' => 'BITSI', 'origin_terminal' => 'Ermita',           'destination_terminal' => 'Naga CBD',             'bus_type' => BusType::Royal,     'scheduled_departure_time' => '23:00', 'direction' => Direction::SB, 'is_active' => true];
        $sb[] = ['code' => 'X1',     'operator' => 'BITSI', 'origin_terminal' => 'EDSA Cubao',       'destination_terminal' => 'Naga CBD',             'bus_type' => BusType::Premier,   'scheduled_departure_time' => '23:30', 'direction' => Direction::SB, 'is_active' => true];
        $sb[] = ['code' => 'R',      'operator' => 'BITSI', 'origin_terminal' => 'PITX',              'destination_terminal' => 'Naga CBD',             'bus_type' => BusType::Royal,     'scheduled_departure_time' => '00:00', 'direction' => Direction::SB, 'is_active' => true];
        $sb[] = ['code' => 'U',      'operator' => 'BITSI', 'origin_terminal' => 'EDSA Cubao',       'destination_terminal' => 'Naga CBD',             'bus_type' => BusType::Royal,     'scheduled_departure_time' => '00:30', 'direction' => Direction::SB, 'is_active' => true];

        // --- Calabanga Route ---
        $sb[] = ['code' => 'Z',      'operator' => 'BITSI', 'origin_terminal' => 'EDSA Cubao',       'destination_terminal' => 'Calabanga',            'bus_type' => BusType::RoyalExe,  'scheduled_departure_time' => '01:00', 'direction' => Direction::SB, 'is_active' => true];

        // --- More Naga CBD Routes ---
        $sb[] = ['code' => 'X',      'operator' => 'BITSI', 'origin_terminal' => 'PITX',              'destination_terminal' => 'Naga CBD',             'bus_type' => BusType::Premier,   'scheduled_departure_time' => '01:30', 'direction' => Direction::SB, 'is_active' => true];
        $sb[] = ['code' => 'R1',     'operator' => 'BITSI', 'origin_terminal' => 'PITX',              'destination_terminal' => 'Naga CBD',             'bus_type' => BusType::Royal,     'scheduled_departure_time' => '02:00', 'direction' => Direction::SB, 'is_active' => true];

        // --- Lagonoy Route ---
        $sb[] = ['code' => 'Z9',     'operator' => 'BITSI', 'origin_terminal' => 'EDSA Cubao',       'destination_terminal' => 'Lagonoy',              'bus_type' => BusType::RoyalExe,  'scheduled_departure_time' => '02:30', 'direction' => Direction::SB, 'is_active' => true];
        $sb[] = ['code' => 'P',      'operator' => 'BITSI', 'origin_terminal' => 'PITX',              'destination_terminal' => 'Lagonoy',              'bus_type' => BusType::Royal,     'scheduled_departure_time' => '03:00', 'direction' => Direction::SB, 'is_active' => true];

        // --- Tabaco / Iriga / Daraga / Legazpi Routes ---
        $sb[] = ['code' => 'Z6',     'operator' => 'BITSI', 'origin_terminal' => 'Ermita',           'destination_terminal' => 'Tabaco',               'bus_type' => BusType::RoyalExe,  'scheduled_departure_time' => '03:30', 'direction' => Direction::SB, 'is_active' => true];
        $sb[] = ['code' => 'Z7',     'operator' => 'BITSI', 'origin_terminal' => 'EDSA Cubao',       'destination_terminal' => 'Iriga',                'bus_type' => BusType::RoyalExe,  'scheduled_departure_time' => '04:00', 'direction' => Direction::SB, 'is_active' => true];
        $sb[] = ['code' => 'H',      'operator' => 'BITSI', 'origin_terminal' => 'EDSA Cubao',       'destination_terminal' => 'Daraga',               'bus_type' => BusType::Royal,     'scheduled_departure_time' => '04:30', 'direction' => Direction::SB, 'is_active' => true];
        $sb[] = ['code' => 'I',      'operator' => 'BITSI', 'origin_terminal' => 'EDSA Cubao',       'destination_terminal' => 'Legazpi Calle Siping', 'bus_type' => BusType::Royal,     'scheduled_departure_time' => '05:00', 'direction' => Direction::SB, 'is_active' => true];
        $sb[] = ['code' => 'Z2',     'operator' => 'BITSI', 'origin_terminal' => 'EDSA Cubao',       'destination_terminal' => 'Legazpi',              'bus_type' => BusType::RoyalExe,  'scheduled_departure_time' => '05:30', 'direction' => Direction::SB, 'is_active' => true];
        $sb[] = ['code' => 'Z14',    'operator' => 'BITSI', 'origin_terminal' => 'PITX',              'destination_terminal' => 'Daraga',               'bus_type' => BusType::RoyalExe,  'scheduled_departure_time' => '06:00', 'direction' => Direction::SB, 'is_active' => false];
        $sb[] = ['code' => 'Z1',     'operator' => 'BITSI', 'origin_terminal' => 'PITX',              'destination_terminal' => 'Legazpi',              'bus_type' => BusType::RoyalExe,  'scheduled_departure_time' => '06:30', 'direction' => Direction::SB, 'is_active' => true];
        $sb[] = ['code' => 'J',      'operator' => 'BITSI', 'origin_terminal' => 'PITX',              'destination_terminal' => 'Legazpi',              'bus_type' => BusType::Royal,     'scheduled_departure_time' => '07:00', 'direction' => Direction::SB, 'is_active' => true];
        $sb[] = ['code' => '15',     'operator' => 'BITSI', 'origin_terminal' => 'PITX',              'destination_terminal' => 'Legazpi',              'bus_type' => BusType::Executive, 'scheduled_departure_time' => '07:30', 'direction' => Direction::SB, 'is_active' => true];

        // --- Virac Routes ---
        $sb[] = ['code' => 'Z10',    'operator' => 'BITSI', 'origin_terminal' => 'PITX',              'destination_terminal' => 'Virac',                'bus_type' => BusType::RoyalExe,  'scheduled_departure_time' => '08:00', 'direction' => Direction::SB, 'is_active' => true];
        $sb[] = ['code' => 'Z15',    'operator' => 'BITSI', 'origin_terminal' => 'EDSA Cubao',       'destination_terminal' => 'Virac',                'bus_type' => BusType::RoyalExe,  'scheduled_departure_time' => '08:30', 'direction' => Direction::SB, 'is_active' => true];
        $sb[] = ['code' => 'V1',     'operator' => 'BITSI', 'origin_terminal' => 'EDSA Cubao',       'destination_terminal' => 'Virac',                'bus_type' => BusType::Executive, 'scheduled_departure_time' => '09:00', 'direction' => Direction::SB, 'is_active' => true];
        $sb[] = ['code' => 'V2',     'operator' => 'BITSI', 'origin_terminal' => 'EDSA Cubao',       'destination_terminal' => 'Virac',                'bus_type' => BusType::Executive, 'scheduled_departure_time' => '09:30', 'direction' => Direction::SB, 'is_active' => true];
        $sb[] = ['code' => 'EXECAT-B','operator' => 'BITSI', 'origin_terminal' => 'EDSA Cubao',      'destination_terminal' => 'Virac',                'bus_type' => BusType::Executive, 'scheduled_departure_time' => '10:00', 'direction' => Direction::SB, 'is_active' => true];
        $sb[] = ['code' => 'EXECAT-C','operator' => 'BITSI', 'origin_terminal' => 'EDSA Cubao',      'destination_terminal' => 'Virac',                'bus_type' => BusType::Executive, 'scheduled_departure_time' => '10:30', 'direction' => Direction::SB, 'is_active' => true];
        $sb[] = ['code' => 'EXECAT-D','operator' => 'BITSI', 'origin_terminal' => 'EDSA Cubao',      'destination_terminal' => 'Virac',                'bus_type' => BusType::Executive, 'scheduled_departure_time' => '11:00', 'direction' => Direction::SB, 'is_active' => true];
        $sb[] = ['code' => 'ECOCAT-C','operator' => 'BITSI', 'origin_terminal' => 'EDSA Cubao',      'destination_terminal' => 'Virac',                'bus_type' => BusType::Economy,   'scheduled_departure_time' => '11:30', 'direction' => Direction::SB, 'is_active' => true];
        $sb[] = ['code' => 'ECOCAT-A','operator' => 'BITSI', 'origin_terminal' => 'EDSA Cubao',      'destination_terminal' => 'Virac',                'bus_type' => BusType::Economy,   'scheduled_departure_time' => '12:00', 'direction' => Direction::SB, 'is_active' => true];
        $sb[] = ['code' => 'EXECAT-A','operator' => 'BITSI', 'origin_terminal' => 'PITX',             'destination_terminal' => 'Virac',                'bus_type' => BusType::Executive, 'scheduled_departure_time' => '12:30', 'direction' => Direction::SB, 'is_active' => true];

        // --- Tabaco / Daraga / Bulan Routes ---
        $sb[] = ['code' => 'EXETAB-A','operator' => 'BITSI', 'origin_terminal' => 'PITX',             'destination_terminal' => 'Tabaco',               'bus_type' => BusType::Executive, 'scheduled_departure_time' => '13:00', 'direction' => Direction::SB, 'is_active' => true];
        $sb[] = ['code' => 'EXEDAR', 'operator' => 'BITSI', 'origin_terminal' => 'EDSA Cubao',       'destination_terminal' => 'Daraga',               'bus_type' => BusType::Executive, 'scheduled_departure_time' => '13:30', 'direction' => Direction::SB, 'is_active' => true];
        $sb[] = ['code' => 'B16',    'operator' => 'BITSI', 'origin_terminal' => 'EDSA Cubao',       'destination_terminal' => 'Bulan',                'bus_type' => BusType::Executive, 'scheduled_departure_time' => '14:00', 'direction' => Direction::SB, 'is_active' => true];
        $sb[] = ['code' => 'B17',    'operator' => 'BITSI', 'origin_terminal' => 'PITX',              'destination_terminal' => 'Bulan',                'bus_type' => BusType::Executive, 'scheduled_departure_time' => '14:30', 'direction' => Direction::SB, 'is_active' => true];

        // =========================================================================
        // NORTH BOUND — Sheet 320
        // =========================================================================

        // --- Visayas Routes ---
        $nb[] = ['code' => 'P1',     'operator' => 'BITSI', 'origin_terminal' => 'Tacloban',          'destination_terminal' => 'EDSA Cubao',           'bus_type' => BusType::Executive, 'scheduled_departure_time' => '05:00', 'direction' => Direction::NB, 'is_active' => true];

        // --- Legazpi to Manila ---
        $nb[] = ['code' => 'Q',      'operator' => 'BITSI', 'origin_terminal' => 'Legazpi',           'destination_terminal' => 'EDSA Cubao',           'bus_type' => BusType::Royal,     'scheduled_departure_time' => '05:30', 'direction' => Direction::NB, 'is_active' => true];
        $nb[] = ['code' => 'D',      'operator' => 'BITSI', 'origin_terminal' => 'Legazpi',           'destination_terminal' => 'PITX',                 'bus_type' => BusType::Royal,     'scheduled_departure_time' => '06:00', 'direction' => Direction::NB, 'is_active' => true];
        $nb[] = ['code' => 'B',      'operator' => 'BITSI', 'origin_terminal' => 'Legazpi',           'destination_terminal' => 'Arcovia Pasig',        'bus_type' => BusType::Royal,     'scheduled_departure_time' => '06:30', 'direction' => Direction::NB, 'is_active' => true];
        $nb[] = ['code' => 'S',      'operator' => 'BITSI', 'origin_terminal' => 'Legazpi',           'destination_terminal' => 'PITX',                 'bus_type' => BusType::Royal,     'scheduled_departure_time' => '07:00', 'direction' => Direction::NB, 'is_active' => true];

        // --- Naga CBD to Manila ---
        $nb[] = ['code' => 'K',      'operator' => 'BITSI', 'origin_terminal' => 'Naga CBD',          'destination_terminal' => 'EDSA Cubao',           'bus_type' => BusType::Royal,     'scheduled_departure_time' => '07:30', 'direction' => Direction::NB, 'is_active' => true];
        $nb[] = ['code' => 'K1',     'operator' => 'BITSI', 'origin_terminal' => 'Naga CBD',          'destination_terminal' => 'PITX',                 'bus_type' => BusType::Royal,     'scheduled_departure_time' => '08:00', 'direction' => Direction::NB, 'is_active' => true];

        // --- Daraga to Manila ---
        $nb[] = ['code' => 'SD',     'operator' => 'BITSI', 'origin_terminal' => 'Daraga',            'destination_terminal' => 'Arcovia Pasig',        'bus_type' => BusType::Royal,     'scheduled_departure_time' => '08:30', 'direction' => Direction::NB, 'is_active' => true];

        // --- Legazpi Executive ---
        $nb[] = ['code' => 'EXELEG', 'operator' => 'BITSI', 'origin_terminal' => 'Legazpi',           'destination_terminal' => 'PITX',                 'bus_type' => BusType::Executive, 'scheduled_departure_time' => '09:00', 'direction' => Direction::NB, 'is_active' => true];

        // --- Naga CBD Routes (continued) ---
        $nb[] = ['code' => 'F',      'operator' => 'BITSI', 'origin_terminal' => 'Naga CBD',          'destination_terminal' => 'EDSA Cubao',           'bus_type' => BusType::Royal,     'scheduled_departure_time' => '09:30', 'direction' => Direction::NB, 'is_active' => true];
        $nb[] = ['code' => 'T',      'operator' => 'BITSI', 'origin_terminal' => 'Naga CBD',          'destination_terminal' => 'EDSA Cubao',           'bus_type' => BusType::Royal,     'scheduled_departure_time' => '10:00', 'direction' => Direction::NB, 'is_active' => true];

        // --- Masbate Routes ---
        $nb[] = ['code' => 'M',      'operator' => 'BITSI', 'origin_terminal' => 'Masbate',           'destination_terminal' => 'EDSA Cubao',           'bus_type' => BusType::Royal,     'scheduled_departure_time' => '10:30', 'direction' => Direction::NB, 'is_active' => true];
        $nb[] = ['code' => '20',     'operator' => 'BITSI', 'origin_terminal' => 'Aroroy',            'destination_terminal' => 'EDSA Cubao',           'bus_type' => BusType::Executive, 'scheduled_departure_time' => '11:00', 'direction' => Direction::NB, 'is_active' => true];
        $nb[] = ['code' => '21',     'operator' => 'BITSI', 'origin_terminal' => 'Cataingan',         'destination_terminal' => 'EDSA Cubao',           'bus_type' => BusType::Executive, 'scheduled_departure_time' => '11:30', 'direction' => Direction::NB, 'is_active' => false];
        $nb[] = ['code' => '22',     'operator' => 'BITSI', 'origin_terminal' => 'Mandaon',           'destination_terminal' => 'EDSA Cubao',           'bus_type' => BusType::Executive, 'scheduled_departure_time' => '12:00', 'direction' => Direction::NB, 'is_active' => true];
        $nb[] = ['code' => '23A',    'operator' => 'BITSI', 'origin_terminal' => 'Placer',            'destination_terminal' => 'EDSA Cubao',           'bus_type' => BusType::Executive, 'scheduled_departure_time' => '12:30', 'direction' => Direction::NB, 'is_active' => true];
        $nb[] = ['code' => '26E',    'operator' => 'BITSI', 'origin_terminal' => 'Placer',            'destination_terminal' => 'EDSA Cubao',           'bus_type' => BusType::Economy,   'scheduled_departure_time' => '13:00', 'direction' => Direction::NB, 'is_active' => true];
        $nb[] = ['code' => '27',     'operator' => 'BITSI', 'origin_terminal' => 'Cawayan',           'destination_terminal' => 'EDSA Cubao',           'bus_type' => BusType::Executive, 'scheduled_departure_time' => '13:30', 'direction' => Direction::NB, 'is_active' => true];
        $nb[] = ['code' => '28',     'operator' => 'BITSI', 'origin_terminal' => 'Aroroy',            'destination_terminal' => 'EDSA Cubao',           'bus_type' => BusType::Economy,   'scheduled_departure_time' => '14:00', 'direction' => Direction::NB, 'is_active' => false];
        $nb[] = ['code' => '29',     'operator' => 'BITSI', 'origin_terminal' => 'Balud',             'destination_terminal' => 'EDSA Cubao',           'bus_type' => BusType::Executive, 'scheduled_departure_time' => '14:30', 'direction' => Direction::NB, 'is_active' => true];
        $nb[] = ['code' => '31',     'operator' => 'BITSI', 'origin_terminal' => 'Esperanza',         'destination_terminal' => 'EDSA Cubao',           'bus_type' => BusType::Executive, 'scheduled_departure_time' => '15:00', 'direction' => Direction::NB, 'is_active' => false];

        // --- Naga CBD Routes (L, etc.) ---
        $nb[] = ['code' => 'L',      'operator' => 'BITSI', 'origin_terminal' => 'Naga CBD',          'destination_terminal' => 'EDSA Cubao',           'bus_type' => BusType::Royal,     'scheduled_departure_time' => '15:30', 'direction' => Direction::NB, 'is_active' => true];

        // --- Gubat Routes ---
        $nb[] = ['code' => 'Z5',     'operator' => 'BITSI', 'origin_terminal' => 'Gubat',             'destination_terminal' => 'EDSA Cubao',           'bus_type' => BusType::RoyalExe,  'scheduled_departure_time' => '16:00', 'direction' => Direction::NB, 'is_active' => true];
        $nb[] = ['code' => 'N',      'operator' => 'BITSI', 'origin_terminal' => 'Gubat',             'destination_terminal' => 'EDSA Cubao',           'bus_type' => BusType::Royal,     'scheduled_departure_time' => '16:30', 'direction' => Direction::NB, 'is_active' => true];
        $nb[] = ['code' => '10',     'operator' => 'BITSI', 'origin_terminal' => 'Gubat',             'destination_terminal' => 'EDSA Cubao',           'bus_type' => BusType::Executive, 'scheduled_departure_time' => '17:00', 'direction' => Direction::NB, 'is_active' => true];
        $nb[] = ['code' => 'Z8',     'operator' => 'BITSI', 'origin_terminal' => 'SITEX',             'destination_terminal' => 'PITX',                 'bus_type' => BusType::RoyalExe,  'scheduled_departure_time' => '17:30', 'direction' => Direction::NB, 'is_active' => true];

        // --- Naga CBD Routes (RoyalExe, Premier, etc.) ---
        $nb[] = ['code' => 'Z3',     'operator' => 'BITSI', 'origin_terminal' => 'Naga CBD',          'destination_terminal' => 'EDSA Cubao',           'bus_type' => BusType::RoyalExe,  'scheduled_departure_time' => '18:00', 'direction' => Direction::NB, 'is_active' => true];
        $nb[] = ['code' => 'A',      'operator' => 'BITSI', 'origin_terminal' => 'Naga CBD',          'destination_terminal' => 'EDSA Cubao',           'bus_type' => BusType::Royal,     'scheduled_departure_time' => '18:30', 'direction' => Direction::NB, 'is_active' => true];
        $nb[] = ['code' => 'C',      'operator' => 'BITSI', 'origin_terminal' => 'Naga CBD',          'destination_terminal' => 'EDSA Cubao',           'bus_type' => BusType::Royal,     'scheduled_departure_time' => '19:00', 'direction' => Direction::NB, 'is_active' => true];
        $nb[] = ['code' => 'E',      'operator' => 'BITSI', 'origin_terminal' => 'Naga CBD',          'destination_terminal' => 'Ermita',               'bus_type' => BusType::Royal,     'scheduled_departure_time' => '19:30', 'direction' => Direction::NB, 'is_active' => true];
        $nb[] = ['code' => 'R1',     'operator' => 'BITSI', 'origin_terminal' => 'Naga CBD',          'destination_terminal' => 'PITX',                 'bus_type' => BusType::Royal,     'scheduled_departure_time' => '20:00', 'direction' => Direction::NB, 'is_active' => true];
        $nb[] = ['code' => 'R',      'operator' => 'BITSI', 'origin_terminal' => 'Naga CBD',          'destination_terminal' => 'PITX',                 'bus_type' => BusType::Royal,     'scheduled_departure_time' => '20:30', 'direction' => Direction::NB, 'is_active' => true];
        $nb[] = ['code' => 'U',      'operator' => 'BITSI', 'origin_terminal' => 'Naga CBD',          'destination_terminal' => 'EDSA Cubao',           'bus_type' => BusType::Royal,     'scheduled_departure_time' => '21:00', 'direction' => Direction::NB, 'is_active' => true];

        // --- Calabanga Route ---
        $nb[] = ['code' => 'Z',      'operator' => 'BITSI', 'origin_terminal' => 'Calabanga',         'destination_terminal' => 'EDSA Cubao',           'bus_type' => BusType::RoyalExe,  'scheduled_departure_time' => '21:30', 'direction' => Direction::NB, 'is_active' => true];

        // --- Premier Routes ---
        $nb[] = ['code' => 'X',      'operator' => 'BITSI', 'origin_terminal' => 'Naga CBD',          'destination_terminal' => 'PITX',                 'bus_type' => BusType::Premier,   'scheduled_departure_time' => '22:00', 'direction' => Direction::NB, 'is_active' => true];
        $nb[] = ['code' => 'X1',     'operator' => 'BITSI', 'origin_terminal' => 'Naga CBD',          'destination_terminal' => 'EDSA Cubao',           'bus_type' => BusType::Premier,   'scheduled_departure_time' => '22:30', 'direction' => Direction::NB, 'is_active' => true];

        // --- Tabaco / Iriga / Daraga Routes ---
        $nb[] = ['code' => 'Z6',     'operator' => 'BITSI', 'origin_terminal' => 'Tabaco',            'destination_terminal' => 'Ermita',               'bus_type' => BusType::RoyalExe,  'scheduled_departure_time' => '23:00', 'direction' => Direction::NB, 'is_active' => true];
        $nb[] = ['code' => 'Z7',     'operator' => 'BITSI', 'origin_terminal' => 'Iriga',             'destination_terminal' => 'EDSA Cubao',           'bus_type' => BusType::RoyalExe,  'scheduled_departure_time' => '23:30', 'direction' => Direction::NB, 'is_active' => true];
        $nb[] = ['code' => 'H',      'operator' => 'BITSI', 'origin_terminal' => 'Daraga',            'destination_terminal' => 'EDSA Cubao',           'bus_type' => BusType::Royal,     'scheduled_departure_time' => '00:00', 'direction' => Direction::NB, 'is_active' => true];
        $nb[] = ['code' => 'Z14',    'operator' => 'BITSI', 'origin_terminal' => 'Daraga',            'destination_terminal' => 'PITX',                 'bus_type' => BusType::RoyalExe,  'scheduled_departure_time' => '00:30', 'direction' => Direction::NB, 'is_active' => false];
        $nb[] = ['code' => 'Z1',     'operator' => 'BITSI', 'origin_terminal' => 'Legazpi',           'destination_terminal' => 'PITX',                 'bus_type' => BusType::RoyalExe,  'scheduled_departure_time' => '01:00', 'direction' => Direction::NB, 'is_active' => true];
        $nb[] = ['code' => 'J',      'operator' => 'BITSI', 'origin_terminal' => 'Legazpi',           'destination_terminal' => 'PITX',                 'bus_type' => BusType::Royal,     'scheduled_departure_time' => '01:30', 'direction' => Direction::NB, 'is_active' => true];

        // --- Lagonoy Route ---
        $nb[] = ['code' => 'Z9',     'operator' => 'BITSI', 'origin_terminal' => 'Lagonoy',           'destination_terminal' => 'EDSA Cubao',           'bus_type' => BusType::RoyalExe,  'scheduled_departure_time' => '02:00', 'direction' => Direction::NB, 'is_active' => true];
        $nb[] = ['code' => 'P',      'operator' => 'BITSI', 'origin_terminal' => 'Lagonoy',           'destination_terminal' => 'PITX',                 'bus_type' => BusType::Royal,     'scheduled_departure_time' => '02:30', 'direction' => Direction::NB, 'is_active' => true];

        // --- Legazpi Executive ---
        $nb[] = ['code' => '15',     'operator' => 'BITSI', 'origin_terminal' => 'Legazpi',           'destination_terminal' => 'PITX',                 'bus_type' => BusType::Executive, 'scheduled_departure_time' => '03:00', 'direction' => Direction::NB, 'is_active' => true];

        // --- Naga CBD to PITX ---
        $nb[] = ['code' => 'R2',     'operator' => 'BITSI', 'origin_terminal' => 'Naga CBD',          'destination_terminal' => 'PITX',                 'bus_type' => BusType::Royal,     'scheduled_departure_time' => '03:30', 'direction' => Direction::NB, 'is_active' => true];

        // --- Virac Routes ---
        $nb[] = ['code' => 'Z10',    'operator' => 'BITSI', 'origin_terminal' => 'Virac',             'destination_terminal' => 'PITX',                 'bus_type' => BusType::RoyalExe,  'scheduled_departure_time' => '04:00', 'direction' => Direction::NB, 'is_active' => true];
        $nb[] = ['code' => 'Z15',    'operator' => 'BITSI', 'origin_terminal' => 'Virac',             'destination_terminal' => 'EDSA Cubao',           'bus_type' => BusType::RoyalExe,  'scheduled_departure_time' => '04:30', 'direction' => Direction::NB, 'is_active' => true];
        $nb[] = ['code' => 'V1',     'operator' => 'BITSI', 'origin_terminal' => 'Virac',             'destination_terminal' => 'Arcovia Pasig',        'bus_type' => BusType::Executive, 'scheduled_departure_time' => '05:00', 'direction' => Direction::NB, 'is_active' => true];
        $nb[] = ['code' => 'V2',     'operator' => 'BITSI', 'origin_terminal' => 'Virac',             'destination_terminal' => 'EDSA Cubao',           'bus_type' => BusType::Executive, 'scheduled_departure_time' => '05:30', 'direction' => Direction::NB, 'is_active' => true];
        $nb[] = ['code' => 'EXECAT-A','operator' => 'BITSI', 'origin_terminal' => 'Virac',            'destination_terminal' => 'PITX',                 'bus_type' => BusType::Executive, 'scheduled_departure_time' => '06:00', 'direction' => Direction::NB, 'is_active' => true];
        $nb[] = ['code' => 'EXECAT-B','operator' => 'BITSI', 'origin_terminal' => 'Virac',            'destination_terminal' => 'EDSA Cubao',           'bus_type' => BusType::Executive, 'scheduled_departure_time' => '06:30', 'direction' => Direction::NB, 'is_active' => false];
        $nb[] = ['code' => 'EXECAT-C','operator' => 'BITSI', 'origin_terminal' => 'Virac',            'destination_terminal' => 'EDSA Cubao',           'bus_type' => BusType::Executive, 'scheduled_departure_time' => '07:00', 'direction' => Direction::NB, 'is_active' => false];
        $nb[] = ['code' => 'ECOCAT-A','operator' => 'BITSI', 'origin_terminal' => 'Virac',            'destination_terminal' => 'EDSA Cubao',           'bus_type' => BusType::Economy,   'scheduled_departure_time' => '07:30', 'direction' => Direction::NB, 'is_active' => true];
        $nb[] = ['code' => 'ECOCAT-C','operator' => 'BITSI', 'origin_terminal' => 'Virac',            'destination_terminal' => 'Alabang',              'bus_type' => BusType::Economy,   'scheduled_departure_time' => '08:00', 'direction' => Direction::NB, 'is_active' => false];

        // --- Bulan Routes ---
        $nb[] = ['code' => 'B16',    'operator' => 'BITSI', 'origin_terminal' => 'Bulan',             'destination_terminal' => 'EDSA Cubao',           'bus_type' => BusType::Executive, 'scheduled_departure_time' => '08:30', 'direction' => Direction::NB, 'is_active' => true];
        $nb[] = ['code' => 'B17',    'operator' => 'BITSI', 'origin_terminal' => 'Bulan',             'destination_terminal' => 'PITX',                 'bus_type' => BusType::Executive, 'scheduled_departure_time' => '09:00', 'direction' => Direction::NB, 'is_active' => true];

        // --- Tabaco / Daraga Routes ---
        $nb[] = ['code' => 'EXETAB-A','operator' => 'BITSI', 'origin_terminal' => 'Tabaco',           'destination_terminal' => 'PITX',                 'bus_type' => BusType::Executive, 'scheduled_departure_time' => '09:30', 'direction' => Direction::NB, 'is_active' => true];
        $nb[] = ['code' => 'EXEDAR', 'operator' => 'BITSI', 'origin_terminal' => 'Daraga',            'destination_terminal' => 'EDSA Cubao',           'bus_type' => BusType::Executive, 'scheduled_departure_time' => '10:00', 'direction' => Direction::NB, 'is_active' => true];

        // =========================================================================
        // Sheet 321 — Additional Trip Codes
        // =========================================================================

        // Northbound additions from Sheet 321
        $nb[] = ['code' => 'Z4',      'operator' => 'BITSI', 'origin_terminal' => 'Daraga',            'destination_terminal' => 'EDSA Cubao',           'bus_type' => BusType::RoyalExe,  'scheduled_departure_time' => '10:15', 'direction' => Direction::NB, 'is_active' => true];
        $nb[] = ['code' => 'H001',    'operator' => 'BITSI', 'origin_terminal' => 'Daraga',            'destination_terminal' => 'Aurora Cubao',         'bus_type' => BusType::Royal,     'scheduled_departure_time' => '10:30', 'direction' => Direction::NB, 'is_active' => true];
        $nb[] = ['code' => 'EXEDAR-B','operator' => 'BITSI', 'origin_terminal' => 'Daraga',            'destination_terminal' => 'PITX',                 'bus_type' => BusType::Executive, 'scheduled_departure_time' => '11:00', 'direction' => Direction::NB, 'is_active' => true];

        // Sheet 321 also shows F001, SN, L001 as active in NB direction
        $nb[] = ['code' => 'F001',    'operator' => 'BITSI', 'origin_terminal' => 'Naga CBD',          'destination_terminal' => 'Arcovia Pasig',        'bus_type' => BusType::Royal,     'scheduled_departure_time' => '11:30', 'direction' => Direction::NB, 'is_active' => true];
        $nb[] = ['code' => 'SN',      'operator' => 'BITSI', 'origin_terminal' => 'Naga CBD',          'destination_terminal' => 'EDSA Cubao',           'bus_type' => BusType::Royal,     'scheduled_departure_time' => '12:00', 'direction' => Direction::NB, 'is_active' => false];
        $nb[] = ['code' => 'L001',    'operator' => 'BITSI', 'origin_terminal' => 'Naga CBD',          'destination_terminal' => 'Arcovia Pasig',        'bus_type' => BusType::Royal,     'scheduled_departure_time' => '12:30', 'direction' => Direction::NB, 'is_active' => true];

        // B001 in NB from Sheet 321
        $nb[] = ['code' => 'B001',    'operator' => 'BITSI', 'origin_terminal' => 'Legazpi',           'destination_terminal' => 'Arcovia Pasig',        'bus_type' => BusType::Royal,     'scheduled_departure_time' => '13:00', 'direction' => Direction::NB, 'is_active' => true];

        return array_merge($sb, $nb);
    }
}