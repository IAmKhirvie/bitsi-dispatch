<?php

namespace Database\Seeders;

use App\Enums\PmsUnit;
use App\Models\PmsSetting;
use Illuminate\Database\Seeder;

class PmsSettingSeeder extends Seeder
{
    public function run(): void
    {
        PmsSetting::updateOrCreate(
            ['name' => 'Standard PMS (10,000 km)'],
            [
                'unit' => PmsUnit::Kilometers,
                'threshold' => 10000,
                'warning_ratio' => 0.80,
                'description' => 'Default — good < 8,000 km, warning 8,000–9,999 km, overdue ≥ 10,000 km',
                'is_default' => true,
            ]
        );

        PmsSetting::updateOrCreate(
            ['name' => 'Extended PMS (15,000 km)'],
            [
                'unit' => PmsUnit::Kilometers,
                'threshold' => 15000,
                'warning_ratio' => 0.80,
                'description' => 'Extended interval',
                'is_default' => false,
            ]
        );

        PmsSetting::updateOrCreate(
            ['name' => 'Heavy Duty PMS (20,000 km)'],
            [
                'unit' => PmsUnit::Kilometers,
                'threshold' => 20000,
                'warning_ratio' => 0.80,
                'description' => 'For heavy-duty vehicles',
                'is_default' => false,
            ]
        );

        PmsSetting::updateOrCreate(
            ['name' => 'Trip-based PMS (500 trips)'],
            [
                'unit' => PmsUnit::Trips,
                'threshold' => 500,
                'warning_ratio' => 0.80,
                'description' => 'Maintenance based on trip count',
                'is_default' => false,
            ]
        );
    }
}
