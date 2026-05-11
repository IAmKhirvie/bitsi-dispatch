<?php

namespace Database\Seeders;

use App\Enums\BusType;
use App\Enums\Direction;
use App\Enums\VehicleStatus;
use App\Models\TripCode;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Reads database/seeders/data/trip-schedule.xlsx and seeds:
 *   - Vehicles from the "BUS NO" sheet
 *   - TripCodes from the "320" sheet (grouped by SOUTH BOUND / NORTH BOUND and operator)
 */
class XlsxTripScheduleSeeder extends Seeder
{
    private const XLSX_PATH = 'database/seeders/data/trip-schedule.xlsx';

    public function run(): void
    {
        $path = base_path(self::XLSX_PATH);
        if (!is_file($path)) {
            $this->command?->warn("trip-schedule.xlsx not found at {$path} — skipping.");
            return;
        }

        $spreadsheet = IOFactory::createReaderForFile($path)
            ->setReadDataOnly(true)
            ->load($path);

        $this->seedVehicles($spreadsheet->getSheetByName('BUS NO'));
        $this->seedTripCodes($spreadsheet->getSheetByName('320'));
    }

    private function seedVehicles($sheet): void
    {
        if (!$sheet) return;

        $count = 0;
        foreach ($sheet->toArray(null, false, false, true) as $rowIdx => $row) {
            // Skip header row and empty/section rows
            if ($rowIdx === 1) continue;
            $busNumber = trim((string) ($row['B'] ?? ''));
            $type = trim((string) ($row['C'] ?? ''));
            $seat = $row['D'] ?? null;
            $plate = trim((string) ($row['E'] ?? ''));
            $brand = trim((string) ($row['F'] ?? '')) ?: 'BITSI';

            if ($busNumber === '' || $type === '') continue;

            $busType = $this->mapBusTypeFromConfig($type);

            // Ensure plate uniqueness — fall back to generated placeholder when missing
            if ($plate === '') {
                $plate = "NO-PLATE-{$busNumber}";
            }

            Vehicle::updateOrCreate(
                ['bus_number' => $busNumber],
                [
                    'brand' => $brand,
                    'bus_type' => $busType,
                    'seating_capacity' => is_numeric($seat) ? (int) $seat : null,
                    'plate_number' => $plate,
                    'status' => VehicleStatus::OK,
                ],
            );
            $count++;
        }

        $this->command?->info("Seeded {$count} buses from XLSX.");
    }

    private function seedTripCodes($sheet): void
    {
        if (!$sheet) return;

        $direction = null;        // 'SB' or 'NB'
        $currentOperator = 'BITSI';
        $count = 0;

        foreach ($sheet->toArray(null, false, false, true) as $rowIdx => $row) {
            if ($rowIdx === 1) continue; // header

            $a = trim((string) ($row['A'] ?? ''));
            $b = trim((string) ($row['B'] ?? ''));

            // Direction section header
            if (stripos($a, 'SOUTH BOUND') !== false) { $direction = Direction::SB; continue; }
            if (stripos($a, 'NORTH BOUND') !== false) { $direction = Direction::NB; continue; }

            // Operator section header (single non-empty cell in column A, B empty)
            if ($a !== '' && $b === '' && $direction !== null) {
                $currentOperator = $this->normalizeOperator($a);
                continue;
            }

            // Trip row: needs both class (A) and code (B)
            if ($a === '' || $b === '' || $direction === null) continue;

            $serviceClass = $this->mapBusTypeFromServiceClass($a);
            $origin = trim((string) ($row['D'] ?? ''));
            $destination = trim((string) ($row['E'] ?? ''));
            if ($origin === '' || $destination === '') continue;

            $scheduledTime = $this->excelTimeToString($row['F'] ?? null);
            $seating = $row['I'] ?? null;
            $statusFlag = strtolower(trim((string) ($row['G'] ?? '')));

            TripCode::updateOrCreate(
                ['code' => $b, 'direction' => $direction],
                [
                    'operator' => $currentOperator,
                    'default_brand' => 'BITSI',
                    'default_seating_capacity' => is_numeric($seating) ? (int) $seating : null,
                    'origin_terminal' => $origin,
                    'destination_terminal' => $destination,
                    'bus_type' => $serviceClass,
                    'scheduled_departure_time' => $scheduledTime,
                    'is_active' => $statusFlag !== 'cancelled',
                ],
            );
            $count++;
        }

        $this->command?->info("Seeded {$count} trip codes from XLSX.");
    }

    private function mapBusTypeFromServiceClass(string $raw): BusType
    {
        return match (strtoupper($raw)) {
            'EXECUTIVE' => BusType::Executive,
            'ROYAL' => BusType::Royal,
            'ROYEXE', 'ROYAL EXE', 'ROYAL EXECUTIVE' => BusType::RoyalExe,
            'PREMIER' => BusType::Premier,
            'ECONOMY' => BusType::Economy,
            'SLEEPER' => BusType::Sleeper,
            default => BusType::Executive,
        };
    }

    private function mapBusTypeFromConfig(string $raw): BusType
    {
        $u = strtoupper(trim($raw));
        return match (true) {
            str_contains($u, 'SLEEPER') => BusType::Sleeper,
            str_contains($u, 'CARGO') => BusType::Economy,
            str_contains($u, 'DYESABEL') => BusType::Executive,
            str_contains($u, '2X3') => BusType::Economy,
            str_contains($u, '2X2') => BusType::Executive,
            str_contains($u, 'EXTREME 2X1'), str_contains($u, '2X1') => BusType::Royal,
            default => BusType::Executive,
        };
    }

    private function normalizeOperator(string $raw): string
    {
        $u = strtoupper($raw);
        return match (true) {
            str_contains($u, 'BICOL ISAROG') => 'BITSI',
            str_contains($u, 'PEÑAFRANCIA'), str_contains($u, 'PENAFRANCIA') => 'PEÑAFRANCIA',
            str_contains($u, 'LEGASPI ST'), str_contains($u, 'ST. JUDE') => 'LEGASPI ST. JUDE',
            default => trim($raw),
        };
    }

    /**
     * Excel stores time as a fraction of a day. Convert to "HH:MM".
     */
    private function excelTimeToString($value): ?string
    {
        if ($value === null || $value === '') return null;
        if (is_string($value) && preg_match('/^\d{1,2}:\d{2}/', $value)) {
            return substr($value, 0, 5);
        }
        if (!is_numeric($value)) return null;

        $totalMinutes = (int) round(((float) $value) * 24 * 60);
        $h = intdiv($totalMinutes, 60) % 24;
        $m = $totalMinutes % 60;
        return sprintf('%02d:%02d', $h, $m);
    }
}
