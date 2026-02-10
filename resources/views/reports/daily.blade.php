@extends('layouts.app')

@section('title', "Daily Report - {$dispatchDay->service_date ?? ''} - BITSI Dispatch")

@section('content')
    @php
        $statusClasses = [
            'scheduled' => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
            'departed' => 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300',
            'on_route' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300',
            'delayed' => 'bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-300',
            'cancelled' => 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300',
            'arrived' => 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300',
        ];
        $date = $dispatchDay->service_date ?? '';
    @endphp

    <div class="flex h-full flex-1 flex-col gap-4 p-4">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold">Daily Report</h1>
                <p class="text-sm text-muted-foreground">Dispatch data for {{ $date }}</p>
            </div>
            <a href="{{ route('reports.index') }}" class="inline-flex items-center rounded-md border border-input bg-background px-4 py-2 text-sm font-medium shadow-sm hover:bg-accent hover:text-accent-foreground">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 h-4 w-4"><path d="m12 19-7-7 7-7"/><path d="M19 12H5"/></svg>
                Back to Reports
            </a>
        </div>

        {{-- Summary --}}
        @if($summary)
            <div class="grid gap-4 sm:grid-cols-3 lg:grid-cols-6">
                <div class="rounded-xl border bg-card text-card-foreground shadow">
                    <div class="p-6 text-center">
                        <div class="text-2xl font-bold">{{ $summary->total_trips }}</div>
                        <div class="text-xs text-muted-foreground">Total</div>
                    </div>
                </div>
                <div class="rounded-xl border bg-card text-card-foreground shadow">
                    <div class="p-6 text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ $summary->sb_trips }}</div>
                        <div class="text-xs text-muted-foreground">Southbound</div>
                    </div>
                </div>
                <div class="rounded-xl border bg-card text-card-foreground shadow">
                    <div class="p-6 text-center">
                        <div class="text-2xl font-bold text-purple-600">{{ $summary->nb_trips }}</div>
                        <div class="text-xs text-muted-foreground">Northbound</div>
                    </div>
                </div>
                <div class="rounded-xl border bg-card text-card-foreground shadow">
                    <div class="p-6 text-center">
                        <div class="text-2xl font-bold">{{ $summary->naga_trips }}</div>
                        <div class="text-xs text-muted-foreground">Naga</div>
                    </div>
                </div>
                <div class="rounded-xl border bg-card text-card-foreground shadow">
                    <div class="p-6 text-center">
                        <div class="text-2xl font-bold">{{ $summary->legazpi_trips }}</div>
                        <div class="text-xs text-muted-foreground">Legazpi</div>
                    </div>
                </div>
                <div class="rounded-xl border bg-card text-card-foreground shadow">
                    <div class="p-6 text-center">
                        <div class="text-2xl font-bold">{{ $summary->sorsogon_trips }}</div>
                        <div class="text-xs text-muted-foreground">Sorsogon</div>
                    </div>
                </div>
            </div>

            {{-- Destination Breakdown --}}
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
                @foreach([
                    ['name' => 'Virac', 'count' => $summary->virac_trips],
                    ['name' => 'Masbate', 'count' => $summary->masbate_trips],
                    ['name' => 'Tabaco', 'count' => $summary->tabaco_trips],
                    ['name' => 'Visayas', 'count' => $summary->visayas_trips],
                    ['name' => 'Cargo', 'count' => $summary->cargo_trips],
                ] as $dest)
                    <div class="rounded-xl border bg-card text-card-foreground shadow">
                        <div class="p-6 text-center">
                            <div class="text-lg font-bold">{{ $dest['count'] }}</div>
                            <div class="text-xs text-muted-foreground">{{ $dest['name'] }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="py-8 text-center text-muted-foreground">
                No summary data available for this date.
            </div>
        @endif

        {{-- Entries Table --}}
        <div class="rounded-xl border bg-card text-card-foreground shadow">
            <div class="p-6">
                <h3 class="font-semibold leading-none tracking-tight">Dispatch Entries</h3>
                <p class="text-sm text-muted-foreground">All entries for {{ $date }} ({{ count($entries) }} total)</p>
            </div>
            <div class="p-0">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b bg-muted/50">
                                <th class="px-4 py-3 text-left font-medium text-muted-foreground">#</th>
                                <th class="px-4 py-3 text-left font-medium text-muted-foreground">Brand</th>
                                <th class="px-4 py-3 text-left font-medium text-muted-foreground">Bus No.</th>
                                <th class="px-4 py-3 text-left font-medium text-muted-foreground">Route</th>
                                <th class="px-4 py-3 text-left font-medium text-muted-foreground">Dir.</th>
                                <th class="px-4 py-3 text-left font-medium text-muted-foreground">Sched.</th>
                                <th class="px-4 py-3 text-left font-medium text-muted-foreground">Actual</th>
                                <th class="px-4 py-3 text-left font-medium text-muted-foreground">Driver</th>
                                <th class="px-4 py-3 text-left font-medium text-muted-foreground">Status</th>
                                <th class="px-4 py-3 text-left font-medium text-muted-foreground">Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($entries as $index => $entry)
                                <tr class="border-b last:border-0 transition-colors hover:bg-muted/30">
                                    <td class="px-4 py-2 text-muted-foreground">{{ $index + 1 }}</td>
                                    <td class="px-4 py-2">{{ $entry->brand ?? '--' }}</td>
                                    <td class="px-4 py-2 font-semibold">{{ $entry->bus_number ?? '--' }}</td>
                                    <td class="px-4 py-2">{{ $entry->route ?? '--' }}</td>
                                    <td class="px-4 py-2">
                                        @if($entry->direction)
                                            <span class="inline-flex items-center rounded px-1.5 py-0.5 text-xs font-medium {{ $entry->direction === 'SB' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300' : 'bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300' }}">
                                                {{ $entry->direction }}
                                            </span>
                                        @else
                                            --
                                        @endif
                                    </td>
                                    <td class="px-4 py-2">{{ $entry->scheduled_departure ? Str::substr($entry->scheduled_departure, 0, 5) : '--' }}</td>
                                    <td class="px-4 py-2">{{ $entry->actual_departure ? Str::substr($entry->actual_departure, 0, 5) : '--' }}</td>
                                    <td class="px-4 py-2">{{ $entry->driver->name ?? '--' }}</td>
                                    <td class="px-4 py-2">
                                        <span class="inline-flex items-center rounded px-1.5 py-0.5 text-xs font-medium {{ $statusClasses[$entry->status] ?? $statusClasses['scheduled'] }}">
                                            {{ ucwords(str_replace('_', ' ', $entry->status)) }}
                                        </span>
                                    </td>
                                    <td class="max-w-[150px] truncate px-4 py-2" title="{{ $entry->remarks ?? '' }}">{{ $entry->remarks ?? '--' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="px-4 py-8 text-center text-muted-foreground">No entries for this date.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
