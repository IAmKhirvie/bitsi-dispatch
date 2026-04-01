<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>BITSI Daily Dispatch Report - {{ $dispatchDay->service_date->format('F j, Y') }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; }
        h1 { font-size: 16px; text-align: center; margin-bottom: 2px; }
        h2 { font-size: 12px; text-align: center; color: #666; margin-top: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #ddd; padding: 4px 6px; text-align: left; }
        th { background-color: #f5f5f5; font-weight: bold; font-size: 10px; }
        td { font-size: 10px; }
        .summary { margin-top: 20px; }
        .summary td { font-weight: bold; }
        .footer { margin-top: 20px; font-size: 9px; color: #999; text-align: center; }
    </style>
</head>
<body>
    <h1>BITSI DAILY BUS STATUS REPORT</h1>
    <h2>Service Date: {{ $dispatchDay->service_date->format('F j, Y') }}</h2>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Brand</th>
                <th>Bus No.</th>
                <th>Trip Code</th>
                <th>Route</th>
                <th>Bus Type</th>
                <th>Dep.</th>
                <th>Arr.</th>
                <th>Sched.</th>
                <th>Actual</th>
                <th>Dir.</th>
                <th>Driver</th>
                <th>Status</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dispatchDay->entries as $index => $entry)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $entry->brand }}</td>
                <td>{{ $entry->bus_number }}</td>
                <td>{{ $entry->tripCode?->code }}</td>
                <td>{{ $entry->route }}</td>
                <td>{{ $entry->bus_type?->value ?? $entry->bus_type }}</td>
                <td>{{ $entry->departure_terminal }}</td>
                <td>{{ $entry->arrival_terminal }}</td>
                <td>{{ $entry->scheduled_departure }}</td>
                <td>{{ $entry->actual_departure }}</td>
                <td>{{ $entry->direction?->value ?? $entry->direction }}</td>
                <td>{{ $entry->driver?->name }}</td>
                <td>{{ $entry->status?->value ?? $entry->status }}</td>
                <td>{{ $entry->remarks }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if($dispatchDay->summary)
    <table class="summary">
        <tr>
            <td>Total: {{ $dispatchDay->summary->total_trips }}</td>
            <td>SB: {{ $dispatchDay->summary->tripCount('sb') }}</td>
            <td>NB: {{ $dispatchDay->summary->tripCount('nb') }}</td>
            <td>Naga: {{ $dispatchDay->summary->tripCount('naga') }}</td>
            <td>Legazpi: {{ $dispatchDay->summary->tripCount('legazpi') }}</td>
            <td>Sorsogon: {{ $dispatchDay->summary->tripCount('sorsogon') }}</td>
        </tr>
        <tr>
            <td>Virac: {{ $dispatchDay->summary->tripCount('virac') }}</td>
            <td>Masbate: {{ $dispatchDay->summary->tripCount('masbate') }}</td>
            <td>Tabaco: {{ $dispatchDay->summary->tripCount('tabaco') }}</td>
            <td>Visayas: {{ $dispatchDay->summary->tripCount('visayas') }}</td>
            <td>Cargo: {{ $dispatchDay->summary->tripCount('cargo') }}</td>
            <td></td>
        </tr>
    </table>
    @endif

    <div class="footer">
        Generated on {{ now()->format('F j, Y g:i A') }} — BITSI Dispatch System
    </div>
</body>
</html>
