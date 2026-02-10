@extends('layouts.app')

@section('title', 'Reports - BITSI Dispatch')

@section('content')
    <div class="flex h-full flex-1 flex-col gap-4 p-4">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold">Reports</h1>
                <p class="text-sm text-muted-foreground">Dispatch reports and trip analytics</p>
            </div>
        </div>

        {{-- Date Range Filter --}}
        <div class="rounded-xl border bg-card text-card-foreground shadow">
            <div class="p-6">
                <form method="GET" action="{{ route('reports.index') }}" class="flex flex-wrap items-end gap-4">
                    <div class="space-y-2">
                        <label for="date_from" class="text-sm font-medium leading-none">From Date</label>
                        <input type="date" id="date_from" name="date_from" value="{{ $dateFrom ?? '' }}" class="flex h-9 w-44 rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
                    </div>
                    <div class="space-y-2">
                        <label for="date_to" class="text-sm font-medium leading-none">To Date</label>
                        <input type="date" id="date_to" name="date_to" value="{{ $dateTo ?? '' }}" class="flex h-9 w-44 rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
                    </div>
                    <button type="submit" class="inline-flex items-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground shadow hover:bg-primary/90">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 h-4 w-4"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                        Apply
                    </button>
                </form>
            </div>
        </div>

        {{-- Summary Cards --}}
        @php
            $daysCount = count($summaries);
            $dailyAverage = $daysCount > 0 ? round($totals['total_trips'] / $daysCount, 1) : 0;
        @endphp
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-xl border bg-card text-card-foreground shadow">
                <div class="flex flex-row items-center justify-between space-y-0 p-6 pb-2">
                    <h3 class="text-sm font-medium">Total Trips</h3>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 text-muted-foreground"><line x1="18" x2="18" y1="20" y2="10"/><line x1="12" x2="12" y1="20" y2="4"/><line x1="6" x2="6" y1="20" y2="14"/></svg>
                </div>
                <div class="p-6 pt-0">
                    <div class="text-2xl font-bold">{{ $totals['total_trips'] }}</div>
                    <p class="text-xs text-muted-foreground">Across {{ $daysCount }} day{{ $daysCount !== 1 ? 's' : '' }}</p>
                </div>
            </div>

            <div class="rounded-xl border bg-card text-card-foreground shadow">
                <div class="flex flex-row items-center justify-between space-y-0 p-6 pb-2">
                    <h3 class="text-sm font-medium">SB / NB Split</h3>
                </div>
                <div class="p-6 pt-0">
                    <div class="text-2xl font-bold">{{ $totals['sb_trips'] }} / {{ $totals['nb_trips'] }}</div>
                    <p class="text-xs text-muted-foreground">Southbound / Northbound</p>
                </div>
            </div>

            <div class="rounded-xl border bg-card text-card-foreground shadow">
                <div class="flex flex-row items-center justify-between space-y-0 p-6 pb-2">
                    <h3 class="text-sm font-medium">Daily Average</h3>
                </div>
                <div class="p-6 pt-0">
                    <div class="text-2xl font-bold">{{ $dailyAverage }}</div>
                    <p class="text-xs text-muted-foreground">Trips per day</p>
                </div>
            </div>

            <div class="rounded-xl border bg-card text-card-foreground shadow">
                <div class="flex flex-row items-center justify-between space-y-0 p-6 pb-2">
                    <h3 class="text-sm font-medium">Top Destinations</h3>
                </div>
                <div class="p-6 pt-0">
                    <div class="text-2xl font-bold">{{ $totals['naga_trips'] }}</div>
                    <p class="text-xs text-muted-foreground">Naga trips (most served)</p>
                </div>
            </div>
        </div>

        {{-- Destination Breakdown --}}
        <div class="grid gap-4 sm:grid-cols-3 lg:grid-cols-6">
            @foreach([
                ['name' => 'Naga', 'count' => $totals['naga_trips']],
                ['name' => 'Legazpi', 'count' => $totals['legazpi_trips']],
                ['name' => 'Sorsogon', 'count' => $totals['sorsogon_trips']],
                ['name' => 'Virac', 'count' => $totals['virac_trips']],
                ['name' => 'Tabaco', 'count' => $totals['tabaco_trips']],
                ['name' => 'Visayas', 'count' => $totals['visayas_trips']],
            ] as $dest)
                <div class="rounded-xl border bg-card text-card-foreground shadow">
                    <div class="pb-3 pt-4 text-center">
                        <div class="text-xl font-bold">{{ $dest['count'] }}</div>
                        <div class="text-xs text-muted-foreground">{{ $dest['name'] }}</div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Daily Breakdown Table --}}
        <div class="rounded-xl border bg-card text-card-foreground shadow">
            <div class="p-6">
                <h3 class="font-semibold leading-none tracking-tight">Daily Breakdown</h3>
                <p class="text-sm text-muted-foreground">Trip counts per day within the selected range</p>
            </div>
            <div class="p-0">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b bg-muted/50">
                                <th class="px-4 py-3 text-left font-medium text-muted-foreground">Date</th>
                                <th class="px-4 py-3 text-right font-medium text-muted-foreground">Total</th>
                                <th class="px-4 py-3 text-right font-medium text-muted-foreground">SB</th>
                                <th class="px-4 py-3 text-right font-medium text-muted-foreground">NB</th>
                                <th class="px-4 py-3 text-right font-medium text-muted-foreground">Naga</th>
                                <th class="px-4 py-3 text-right font-medium text-muted-foreground">Legazpi</th>
                                <th class="px-4 py-3 text-right font-medium text-muted-foreground">Sorsogon</th>
                                <th class="px-4 py-3 text-right font-medium text-muted-foreground">Virac</th>
                                <th class="px-4 py-3 text-right font-medium text-muted-foreground">Visayas</th>
                                <th class="px-4 py-3 text-left font-medium text-muted-foreground">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($summaries as $row)
                                <tr class="border-b last:border-0 transition-colors hover:bg-muted/30">
                                    <td class="px-4 py-3 font-medium">{{ $row->dispatch_day->service_date ?? '' }}</td>
                                    <td class="px-4 py-3 text-right font-semibold">{{ $row->total_trips }}</td>
                                    <td class="px-4 py-3 text-right">{{ $row->sb_trips }}</td>
                                    <td class="px-4 py-3 text-right">{{ $row->nb_trips }}</td>
                                    <td class="px-4 py-3 text-right">{{ $row->naga_trips }}</td>
                                    <td class="px-4 py-3 text-right">{{ $row->legazpi_trips }}</td>
                                    <td class="px-4 py-3 text-right">{{ $row->sorsogon_trips }}</td>
                                    <td class="px-4 py-3 text-right">{{ $row->virac_trips }}</td>
                                    <td class="px-4 py-3 text-right">{{ $row->visayas_trips }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-1">
                                            <a href="{{ route('reports.daily', $row->dispatch_day->service_date ?? '') }}" class="inline-flex items-center rounded-md px-2.5 py-1.5 text-sm font-medium hover:bg-accent hover:text-accent-foreground">View</a>
                                            <a href="{{ route('reports.export-excel', $row->dispatch_day->service_date ?? '') }}" class="inline-flex h-8 w-8 items-center justify-center rounded-md hover:bg-accent" title="Export Excel">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 text-green-600"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><path d="M8 13h2"/><path d="M8 17h2"/><path d="M14 13h2"/><path d="M14 17h2"/></svg>
                                            </a>
                                            <a href="{{ route('reports.export-pdf', $row->dispatch_day->service_date ?? '') }}" class="inline-flex h-8 w-8 items-center justify-center rounded-md hover:bg-accent" title="Export PDF">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 text-red-600"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="px-4 py-8 text-center text-muted-foreground">
                                        No data available for the selected date range.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if(count($summaries) > 0)
                            <tfoot>
                                <tr class="border-t-2 bg-muted/30 font-semibold">
                                    <td class="px-4 py-3">Totals</td>
                                    <td class="px-4 py-3 text-right">{{ $totals['total_trips'] }}</td>
                                    <td class="px-4 py-3 text-right">{{ $totals['sb_trips'] }}</td>
                                    <td class="px-4 py-3 text-right">{{ $totals['nb_trips'] }}</td>
                                    <td class="px-4 py-3 text-right">{{ $totals['naga_trips'] }}</td>
                                    <td class="px-4 py-3 text-right">{{ $totals['legazpi_trips'] }}</td>
                                    <td class="px-4 py-3 text-right">{{ $totals['sorsogon_trips'] }}</td>
                                    <td class="px-4 py-3 text-right">{{ $totals['virac_trips'] }}</td>
                                    <td class="px-4 py-3 text-right">{{ $totals['visayas_trips'] }}</td>
                                    <td class="px-4 py-3"></td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
