<div>
    {{-- Report Type Tabs --}}
    <div class="flex items-center gap-1 rounded-lg bg-muted p-1 w-fit">
        @foreach (['daily' => 'Daily', 'weekly' => 'Weekly', 'monthly' => 'Monthly'] as $type => $label)
            <button
                wire:click="$set('reportType', '{{ $type }}')"
                class="rounded-md px-4 py-2 text-sm font-medium transition-colors {{ $reportType === $type ? 'bg-background text-foreground shadow-sm' : 'text-muted-foreground hover:text-foreground' }}"
            >
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- Date Range Filter --}}
    <div class="mt-4 rounded-xl border bg-card text-card-foreground shadow">
        <div class="p-6">
            <div class="flex flex-wrap items-end gap-4">
                <div class="space-y-2">
                    <label for="date_from" class="text-sm font-medium leading-none">From Date</label>
                    <div class="relative">
                        <input type="date" id="date_from" wire:model.live.debounce.300ms="dateFrom" class="flex h-9 w-44 rounded-md border border-input bg-transparent px-3 py-1 pr-10 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
                    </div>
                </div>
                <div class="space-y-2">
                    <label for="date_to" class="text-sm font-medium leading-none">To Date</label>
                    <div class="relative">
                        <input type="date" id="date_to" wire:model.live.debounce.300ms="dateTo" class="flex h-9 w-44 rounded-md border border-input bg-transparent px-3 py-1 pr-10 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
                    </div>
                </div>
                @if ($reportType !== 'daily')
                    <div class="text-sm text-muted-foreground">
                        Showing {{ ucfirst($reportType) }} summary
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Summary Cards --}}
    @php
        $dailyAverage = $daysCount > 0 ? round($totals['total_trips'] / $daysCount, 1) : 0;
    @endphp
    <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
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
    <div class="mt-4 grid gap-4 sm:grid-cols-3 lg:grid-cols-6">
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

    {{-- Data Table --}}
    <div class="mt-4 rounded-xl border bg-card text-card-foreground shadow">
        <div class="p-6">
            <h3 class="font-semibold leading-none tracking-tight">{{ ucfirst($reportType) }} Breakdown</h3>
            <p class="text-sm text-muted-foreground">Trip counts per {{ $reportType === 'daily' ? 'day' : ($reportType === 'weekly' ? 'week' : 'month') }} within the selected range</p>
        </div>
        <div class="p-0">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b bg-muted/50">
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">
                                {{ $reportType === 'daily' ? 'Date' : ($reportType === 'weekly' ? 'Week' : 'Month') }}
                            </th>
                            @if ($reportType !== 'daily')
                                <th class="px-4 py-3 text-right font-medium text-muted-foreground">Days</th>
                            @endif
                            <th class="px-4 py-3 text-right font-medium text-muted-foreground">Total</th>
                            <th class="px-4 py-3 text-right font-medium text-muted-foreground">SB</th>
                            <th class="px-4 py-3 text-right font-medium text-muted-foreground">NB</th>
                            <th class="px-4 py-3 text-right font-medium text-muted-foreground">Naga</th>
                            <th class="px-4 py-3 text-right font-medium text-muted-foreground">Legazpi</th>
                            <th class="px-4 py-3 text-right font-medium text-muted-foreground">Sorsogon</th>
                            <th class="px-4 py-3 text-right font-medium text-muted-foreground">Virac</th>
                            <th class="px-4 py-3 text-right font-medium text-muted-foreground">Visayas</th>
                            @if ($reportType === 'daily')
                                <th class="px-4 py-3 text-left font-medium text-muted-foreground">Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @if ($reportType === 'daily' && $summaries)
                            @forelse($summaries as $row)
                                <tr class="border-b last:border-0 transition-colors hover:bg-muted/30">
                                    <td class="px-4 py-3 font-medium">{{ $row->dispatchDay->service_date ?? '' }}</td>
                                    <td class="px-4 py-3 text-right font-semibold">{{ $row->total_trips }}</td>
                                    <td class="px-4 py-3 text-right">{{ $row->tripCount('sb') }}</td>
                                    <td class="px-4 py-3 text-right">{{ $row->tripCount('nb') }}</td>
                                    <td class="px-4 py-3 text-right">{{ $row->tripCount('naga') }}</td>
                                    <td class="px-4 py-3 text-right">{{ $row->tripCount('legazpi') }}</td>
                                    <td class="px-4 py-3 text-right">{{ $row->tripCount('sorsogon') }}</td>
                                    <td class="px-4 py-3 text-right">{{ $row->tripCount('virac') }}</td>
                                    <td class="px-4 py-3 text-right">{{ $row->tripCount('visayas') }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-1">
                                            <a href="{{ route('reports.daily', $row->dispatchDay->service_date ?? '') }}" class="inline-flex items-center rounded-md px-2.5 py-1.5 text-sm font-medium hover:bg-accent hover:text-accent-foreground">View</a>
                                            <a href="{{ route('reports.export-excel', $row->dispatchDay->service_date ?? '') }}" class="inline-flex h-8 w-8 items-center justify-center rounded-md hover:bg-accent" title="Export Excel">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 text-green-600"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><path d="M8 13h2"/><path d="M8 17h2"/><path d="M14 13h2"/><path d="M14 17h2"/></svg>
                                            </a>
                                            <a href="{{ route('reports.export-pdf', $row->dispatchDay->service_date ?? '') }}" class="inline-flex h-8 w-8 items-center justify-center rounded-md hover:bg-accent" title="Export PDF">
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
                        @elseif ($reportType === 'weekly' && isset($weeklyRows))
                            @forelse($weeklyRows as $row)
                                <tr class="border-b last:border-0 transition-colors hover:bg-muted/30">
                                    <td class="px-4 py-3 font-medium">{{ $row->week_label }}</td>
                                    <td class="px-4 py-3 text-right">{{ $row->days_count }}</td>
                                    <td class="px-4 py-3 text-right font-semibold">{{ $row->total_trips }}</td>
                                    <td class="px-4 py-3 text-right">{{ $row->sb_trips }}</td>
                                    <td class="px-4 py-3 text-right">{{ $row->nb_trips }}</td>
                                    <td class="px-4 py-3 text-right">{{ $row->naga_trips }}</td>
                                    <td class="px-4 py-3 text-right">{{ $row->legazpi_trips }}</td>
                                    <td class="px-4 py-3 text-right">{{ $row->sorsogon_trips }}</td>
                                    <td class="px-4 py-3 text-right">{{ $row->virac_trips }}</td>
                                    <td class="px-4 py-3 text-right">{{ $row->visayas_trips }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="px-4 py-8 text-center text-muted-foreground">
                                        No data available for the selected date range.
                                    </td>
                                </tr>
                            @endforelse
                        @elseif ($reportType === 'monthly' && isset($monthlyRows))
                            @forelse($monthlyRows as $row)
                                <tr class="border-b last:border-0 transition-colors hover:bg-muted/30">
                                    <td class="px-4 py-3 font-medium">{{ $row->month_label }}</td>
                                    <td class="px-4 py-3 text-right">{{ $row->days_count }}</td>
                                    <td class="px-4 py-3 text-right font-semibold">{{ $row->total_trips }}</td>
                                    <td class="px-4 py-3 text-right">{{ $row->sb_trips }}</td>
                                    <td class="px-4 py-3 text-right">{{ $row->nb_trips }}</td>
                                    <td class="px-4 py-3 text-right">{{ $row->naga_trips }}</td>
                                    <td class="px-4 py-3 text-right">{{ $row->legazpi_trips }}</td>
                                    <td class="px-4 py-3 text-right">{{ $row->sorsogon_trips }}</td>
                                    <td class="px-4 py-3 text-right">{{ $row->virac_trips }}</td>
                                    <td class="px-4 py-3 text-right">{{ $row->visayas_trips }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="px-4 py-8 text-center text-muted-foreground">
                                        No data available for the selected date range.
                                    </td>
                                </tr>
                            @endforelse
                        @endif
                    </tbody>
                    @if($totals['total_trips'] > 0)
                        <tfoot>
                            <tr class="border-t-2 bg-muted/30 font-semibold">
                                <td class="px-4 py-3">Totals</td>
                                @if ($reportType !== 'daily')
                                    <td class="px-4 py-3 text-right">{{ $daysCount }}</td>
                                @endif
                                <td class="px-4 py-3 text-right">{{ $totals['total_trips'] }}</td>
                                <td class="px-4 py-3 text-right">{{ $totals['sb_trips'] }}</td>
                                <td class="px-4 py-3 text-right">{{ $totals['nb_trips'] }}</td>
                                <td class="px-4 py-3 text-right">{{ $totals['naga_trips'] }}</td>
                                <td class="px-4 py-3 text-right">{{ $totals['legazpi_trips'] }}</td>
                                <td class="px-4 py-3 text-right">{{ $totals['sorsogon_trips'] }}</td>
                                <td class="px-4 py-3 text-right">{{ $totals['virac_trips'] }}</td>
                                <td class="px-4 py-3 text-right">{{ $totals['visayas_trips'] }}</td>
                                @if ($reportType === 'daily')
                                    <td class="px-4 py-3"></td>
                                @endif
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>

        {{-- Pagination (daily only) --}}
        @if($reportType === 'daily' && $summaries && $summaries->hasPages())
            <div class="border-t px-6 py-4">
                {{ $summaries->links() }}
            </div>
        @endif
    </div>
</div>
