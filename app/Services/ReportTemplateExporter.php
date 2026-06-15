<?php

namespace App\Services;

use App\Exports\Concerns\DispatchRowMapper;
use App\Models\DispatchEntry;
use App\Models\ReportTemplate;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use RuntimeException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

class ReportTemplateExporter
{
    use DispatchRowMapper;

    private const MAX_TEMPLATE_COLUMNS = 52;
    private const MAX_HEADER_ROWS = 20;

    public function downloadDispatch(Collection $entries, ReportTemplate $template, string $fileName): BinaryFileResponse
    {
        $source = Storage::disk('local')->path($template->file_path);
        $columns = $this->readTemplateColumns($source);

        $this->assertDispatchColumns($columns);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        foreach ($columns as $index => $column) {
            $sheet->setCellValue([$index + 1, 1], $column['heading']);
        }

        $rowNumber = 2;

        foreach ($entries as $entry) {
            if (! $entry instanceof DispatchEntry) {
                continue;
            }

            $values = $this->dispatchTemplateValues($entry);
            foreach ($columns as $index => $column) {
                $sheet->setCellValue([$index + 1, $rowNumber], $values[$column['key']] ?? null);
            }
            $rowNumber++;
        }

        $tmpPath = storage_path('app/private/report-exports/' . uniqid('dispatch-', true) . '.xlsx');
        if (! is_dir(dirname($tmpPath))) {
            mkdir(dirname($tmpPath), 0755, true);
        }

        IOFactory::createWriter($spreadsheet, 'Xlsx')->save($tmpPath);

        return response()->download($tmpPath, $fileName)->deleteFileAfterSend(true);
    }

    public function assertDispatchTemplate(string $path): void
    {
        $this->assertDispatchColumns($this->readTemplateColumns($path));
    }

    private function assertDispatchColumns(array $columns): void
    {
        $keys = array_column($columns, 'key');
        if (count($columns) < 2 || ! in_array('trip_code', $keys, true) || ! in_array('scheduled', $keys, true)) {
            throw new RuntimeException('The uploaded XLSX does not contain recognizable dispatch report headings.');
        }
    }

    private function readTemplateColumns(string $path): array
    {
        $zip = new ZipArchive();
        if ($zip->open($path) !== true) {
            throw new RuntimeException('The uploaded XLSX template could not be opened.');
        }

        try {
            $sharedStrings = $this->sharedStrings($zip);
            $sheetStat = $zip->statName('xl/worksheets/sheet1.xml');
            if (! $sheetStat || ($sheetStat['size'] ?? 0) > 2_000_000) {
                throw new RuntimeException('The first worksheet is too large to use as a report template.');
            }

            $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
            if (! is_string($sheetXml)) {
                throw new RuntimeException('The first worksheet could not be read from the XLSX template.');
            }
        } finally {
            $zip->close();
        }

        $xml = simplexml_load_string($sheetXml);
        if (! $xml) {
            throw new RuntimeException('The first worksheet XML is not readable.');
        }

        $xml->registerXPathNamespace('x', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
        $bestColumns = [];

        foreach ($xml->xpath('//x:sheetData/x:row') ?: [] as $row) {
            $rowNumber = (int) ($row['r'] ?? 0);
            if ($rowNumber > self::MAX_HEADER_ROWS) {
                break;
            }

            $columns = [];
            $matches = 0;
            foreach ($row->children('http://schemas.openxmlformats.org/spreadsheetml/2006/main')->c as $cell) {
                $coordinate = (string) ($cell['r'] ?? '');
                $columnIndex = $this->cellColumnIndex($coordinate);
                if ($columnIndex < 1 || $columnIndex > self::MAX_TEMPLATE_COLUMNS) {
                    continue;
                }

                $heading = $this->cellValue($cell, $sharedStrings);
                $key = $this->templateKey($heading);
                if ($key) {
                    $columns[] = [
                        'heading' => trim((string) $heading),
                        'key' => $key,
                    ];
                    $matches++;
                }
            }

            if ($matches > count($bestColumns)) {
                $bestColumns = $columns;
            }
        }

        return count($bestColumns) >= 2 ? $bestColumns : [];
    }

    private function sharedStrings(ZipArchive $zip): array
    {
        $xml = $zip->getFromName('xl/sharedStrings.xml');
        if (! is_string($xml)) {
            return [];
        }

        $shared = simplexml_load_string($xml);
        if (! $shared) {
            return [];
        }

        $shared->registerXPathNamespace('x', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
        $strings = [];
        foreach ($shared->xpath('//x:si') ?: [] as $item) {
            $item->registerXPathNamespace('x', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
            $parts = [];
            foreach ($item->xpath('.//x:t') ?: [] as $text) {
                $parts[] = (string) $text;
            }
            $strings[] = implode('', $parts);
        }

        return $strings;
    }

    private function cellValue(\SimpleXMLElement $cell, array $sharedStrings): string
    {
        $type = (string) ($cell['t'] ?? '');
        $namespaces = 'http://schemas.openxmlformats.org/spreadsheetml/2006/main';

        if ($type === 's') {
            $index = (int) ($cell->children($namespaces)->v ?? -1);
            return $sharedStrings[$index] ?? '';
        }

        if ($type === 'inlineStr') {
            $cell->registerXPathNamespace('x', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
            $parts = [];
            foreach ($cell->xpath('.//x:t') ?: [] as $text) {
                $parts[] = (string) $text;
            }
            return implode('', $parts);
        }

        return (string) ($cell->children($namespaces)->v ?? '');
    }

    private function cellColumnIndex(string $coordinate): int
    {
        if (! preg_match('/^([A-Z]+)/i', $coordinate, $matches)) {
            return 0;
        }

        $index = 0;
        foreach (str_split(strtoupper($matches[1])) as $letter) {
            $index = ($index * 26) + (ord($letter) - 64);
        }

        return $index;
    }

    private function templateKey($heading): ?string
    {
        $normalized = strtolower(trim(preg_replace('/[^a-z0-9]+/i', ' ', (string) $heading)));

        return match ($normalized) {
            'date', 'service date' => 'date',
            'service class', 'bus type' => 'service_class',
            'trip code', 'trip' => 'trip_code',
            'pax', 'passengers', 'seats available', 'seat available' => 'pax',
            'origin', 'departure terminal' => 'origin',
            'destination', 'arrival terminal' => 'destination',
            'departure time', 'scheduled', 'scheduled time', 'scheduled departure' => 'scheduled',
            'actual', 'actual time', 'actual departure' => 'actual',
            'late dispatch', 'late', 'late minutes' => 'late_dispatch',
            'status' => 'status',
            'action' => 'action',
            'seating capacity' => 'seating_capacity',
            'bus no', 'bus number', 'bus' => 'bus_number',
            'brand' => 'brand',
            'driver 1', 'driver' => 'driver_1',
            'driver 2' => 'driver_2',
            'dispatcher' => 'dispatcher',
            'kmr', 'km run', 'kmr run' => 'kmr_run',
            'kmr out', 'kmr dispatch' => 'kmr_out',
            'kmr in', 'kmr arrival' => 'kmr_in',
            'remarks' => 'remarks',
            default => null,
        };
    }

    private function columnName(int $index): string
    {
        $name = '';
        while ($index > 0) {
            $index--;
            $name = chr(65 + ($index % 26)) . $name;
            $index = intdiv($index, 26);
        }

        return $name;
    }
}
