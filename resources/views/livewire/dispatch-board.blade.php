<div class="app-page flex h-full flex-1 flex-col gap-4 p-4" wire:poll.10s>
    @php
        $statusClasses = [
            'scheduled' => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
            'departed' => 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300',
            'on_route' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300',
            'delayed' => 'bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-300',
            'cancelled' => 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300',
            'breakdown' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
            'arrived' => 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300',
        ];
        $statusOptions = ['scheduled', 'departed', 'on_route', 'delayed', 'cancelled', 'breakdown', 'arrived'];
        $sortHeaderClass = 'inline-flex w-full items-center gap-1 text-left font-medium text-muted-foreground transition-colors hover:text-foreground';
        $sortIcon = function (string $field) use ($sortField, $sortDirection) {
            if ($sortField !== $field) {
                return '';
            }

            return $sortDirection === 'asc'
                ? '<svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m18 15-6-6-6 6"/></svg>'
                : '<svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>';
        };
    @endphp

    {{-- Header with date picker --}}
    <div class="app-toolbar flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold">Dispatch Board</h1>
            <p class="text-sm text-muted-foreground">Manage daily bus dispatch operations</p>
        </div>
        <div class="app-toolbar-actions flex items-center gap-3">
            <div class="relative">
                <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search dispatch..."
                    class="flex h-9 w-56 rounded-md border border-input bg-transparent px-3 py-1 pl-9 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                />
            </div>
            <span class="text-xs text-muted-foreground" title="Auto-refreshes every 10 seconds">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 inline h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
                Live
            </span>
            <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-muted-foreground" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <div class="relative">
                    <input
                        type="date"
                        wire:model.live="date"
                        class="flex h-9 w-44 rounded-md border border-input bg-transparent px-3 py-1 pr-10 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                    />
                </div>
            </div>
        </div>
    </div>

    {{-- Summary bar --}}
    @if ($dispatchDay && $dispatchDay->summary)
        @php $summary = $dispatchDay->summary; @endphp
        <div class="grid grid-cols-2 gap-2 sm:grid-cols-4 lg:grid-cols-6">
            <div class="rounded-lg border bg-card p-3 text-center">
                <div class="text-lg font-bold">{{ $summary->total_trips ?? 0 }}</div>
                <div class="text-xs text-muted-foreground">Total</div>
            </div>
            <div class="rounded-lg border bg-card p-3 text-center">
                <div class="text-lg font-bold text-blue-600">{{ $summary->tripCount('sb') }}</div>
                <div class="text-xs text-muted-foreground">SB</div>
            </div>
            <div class="rounded-lg border bg-card p-3 text-center">
                <div class="text-lg font-bold text-purple-600">{{ $summary->tripCount('nb') }}</div>
                <div class="text-xs text-muted-foreground">NB</div>
            </div>
            <div class="rounded-lg border bg-card p-3 text-center">
                <div class="text-lg font-bold">{{ $summary->tripCount('naga') }}</div>
                <div class="text-xs text-muted-foreground">Naga</div>
            </div>
            <div class="rounded-lg border bg-card p-3 text-center">
                <div class="text-lg font-bold">{{ $summary->tripCount('legazpi') }}</div>
                <div class="text-xs text-muted-foreground">Legazpi</div>
            </div>
            <div class="rounded-lg border bg-card p-3 text-center">
                <div class="text-lg font-bold">{{ $summary->tripCount('sorsogon') }}</div>
                <div class="text-xs text-muted-foreground">Sorsogon</div>
            </div>
        </div>
    @endif

    {{-- No dispatch day state --}}
    @if (!$dispatchDay)
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
            <div class="flex flex-col items-center justify-center py-12 px-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="mb-4 h-12 w-12 text-muted-foreground" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <h3 class="mb-2 text-lg font-semibold">No Dispatch Day</h3>
                <p class="mb-4 text-sm text-muted-foreground">No dispatch day exists for {{ $date }}. Create one to start dispatching.</p>
                <button
                    wire:click="createDispatchDay"
                    class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring bg-primary text-primary-foreground shadow hover:bg-primary/90 h-9 px-4 py-2"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Create Dispatch Day
                </button>
            </div>
        </div>
    @else
        {{-- Dispatch Table --}}
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
            <div class="flex flex-row items-center justify-between space-y-0 p-4 pb-3">
                <div>
                    <h3 class="text-base font-semibold leading-none tracking-tight">
                        Entries for {{ $dispatchDay->service_date }}
                        <span class="ml-2 text-sm font-normal text-muted-foreground">({{ $entries->total() }} entries)</span>
                    </h3>
                </div>
                <div class="flex flex-wrap items-center justify-end gap-2">
                    <div class="flex items-center gap-1.5">
                        <span class="text-xs font-medium text-muted-foreground">Export:</span>
                        <a href="{{ route('reports.export-excel', $dispatchDay->service_date, false) }}" class="inline-flex h-9 items-center justify-center rounded-md border border-input bg-background px-3 text-xs font-medium shadow-sm transition-colors hover:bg-accent hover:text-accent-foreground" title="Export daily Excel">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-3.5 w-3.5 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><path d="M8 13h2"/><path d="M8 17h2"/><path d="M14 13h2"/><path d="M14 17h2"/></svg>
                            Excel
                        </a>
                        <a href="{{ route('reports.export-pdf', $dispatchDay->service_date, false) }}" class="inline-flex h-9 items-center justify-center rounded-md border border-input bg-background px-3 text-xs font-medium shadow-sm transition-colors hover:bg-accent hover:text-accent-foreground" title="Export daily PDF">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-3.5 w-3.5 text-red-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
                            PDF
                        </a>
                    </div>
                    <button
                        type="button"
                        wire:click="$set('showAddDialog', true)"
                        class="inline-flex h-9 items-center justify-center whitespace-nowrap rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground shadow transition-colors hover:bg-primary/90 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        Add Entry
                    </button>
                </div>
            </div>

            @if (session('dispatch_error'))
                <div class="mx-4 mb-2 rounded-md bg-red-50 px-3 py-2 text-xs text-red-700 dark:bg-red-900/30 dark:text-red-400">
                    {{ session('dispatch_error') }}
                </div>
            @endif

            <div class="p-0">
                <div class="app-table-scroll overflow-x-auto">
                    <table class="min-w-[1120px] w-full text-xs table-fixed">
                        <thead>
                            <tr class="border-b bg-muted/50">
                                <th class="w-8 px-2 py-2 text-left">
                                    <button type="button" wire:click="sortBy('sort_order')" class="{{ $sortHeaderClass }}">
                                        #
                                        {!! $sortIcon('sort_order') !!}
                                    </button>
                                </th>
                                <th class="w-20 px-2 py-2 text-left">
                                    <button type="button" wire:click="sortBy('trip')" class="{{ $sortHeaderClass }}">
                                        Trip
                                        {!! $sortIcon('trip') !!}
                                    </button>
                                </th>
                                <th class="w-24 px-2 py-2 text-left">
                                    <button type="button" wire:click="sortBy('bus_number')" class="{{ $sortHeaderClass }}">
                                        Bus
                                        {!! $sortIcon('bus_number') !!}
                                    </button>
                                </th>
                                <th class="w-12 px-2 py-2 text-left">
                                    <button type="button" wire:click="sortBy('direction')" class="{{ $sortHeaderClass }}">
                                        Dir
                                        {!! $sortIcon('direction') !!}
                                    </button>
                                </th>
                                <th class="w-36 px-2 py-2 text-left">
                                    <button type="button" wire:click="sortBy('route')" class="{{ $sortHeaderClass }}">
                                        Route
                                        {!! $sortIcon('route') !!}
                                    </button>
                                </th>
                                <th class="w-24 px-2 py-2 text-left">
                                    <button type="button" wire:click="sortBy('scheduled_departure')" class="{{ $sortHeaderClass }}">
                                        Times
                                        {!! $sortIcon('scheduled_departure') !!}
                                    </button>
                                </th>
                                <th class="w-40 px-2 py-2 text-left">
                                    <button type="button" wire:click="sortBy('driver')" class="{{ $sortHeaderClass }}">
                                        Drivers
                                        {!! $sortIcon('driver') !!}
                                    </button>
                                </th>
                                <th class="w-48 px-2 py-2 text-left">
                                    <button type="button" wire:click="sortBy('status')" class="{{ $sortHeaderClass }}">
                                        Status
                                        {!! $sortIcon('status') !!}
                                    </button>
                                </th>
                                <th class="w-56 px-2 py-2 text-left">
                                    <button type="button" wire:click="sortBy('remarks')" class="{{ $sortHeaderClass }}">
                                        Remarks
                                        {!! $sortIcon('remarks') !!}
                                    </button>
                                </th>
                                <th class="w-16 px-2 py-2 text-left font-medium text-muted-foreground">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($entries as $index => $entry)
                                @php
                                    $entryStatus = $entry->status instanceof \BackedEnum ? $entry->status->value : ($entry->status ?? 'scheduled');
                                    $dir = $entry->direction instanceof \BackedEnum ? $entry->direction->value : $entry->direction;
                                @endphp
                                <tr class="border-b align-top hover:bg-muted/30 transition-colors">
                                    <td class="px-2 py-1.5 text-muted-foreground">{{ ($entries->currentPage() - 1) * $entries->perPage() + $index + 1 }}</td>
                                    <td class="px-2 py-1.5">{{ $entry->manual_trip_code ?: $entry->tripCode->code ?? '--' }}</td>
                                    <td class="px-2 py-1.5">
                                        <div class="font-semibold truncate">{{ $entry->bus_number ?? '--' }}</div>
                                        <div class="text-[10px] text-muted-foreground truncate">{{ $entry->brand ?? '' }}{{ $entry->bus_type ? ' · ' . $entry->bus_type : '' }}</div>
                                    </td>
                                    <td class="px-2 py-1.5">
                                        @if ($dir)
                                            <span class="inline-flex items-center rounded px-1.5 py-0.5 text-xs font-medium {{ $dir === 'SB' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300' : 'bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300' }}">
                                                {{ $dir }}
                                            </span>
                                        @else
                                            --
                                        @endif
                                    </td>
                                    <td class="px-2 py-1.5 truncate" title="{{ $entry->route ?? '' }}">{{ $entry->route ?? '--' }}</td>
                                    <td class="px-2 py-1.5 text-[11px] leading-tight">
                                        <div><span class="text-muted-foreground tooltip">Scheduled: </span> {{ $entry->scheduled_departure ? \Carbon\Carbon::parse($entry->scheduled_departure)->format('H:i:s') : '--' }}</div>
                                        <div><span class="text-muted-foreground">Delay:</span> {{ $entry->actual_departure ? \Carbon\Carbon::parse($entry->actual_departure)->format('H:i:s') : '--' }}</div>
                                        <div><span class="text-muted-foreground">Arrival:</span> {{ $entry->actual_arrival ? \Carbon\Carbon::parse($entry->actual_arrival)->format('H:i:s') : '--' }}</div>
                                    </td>
                                    <td class="px-2 py-1.5 text-[11px] leading-tight">
                                        <div class="truncate">{{ $entry->driver->name ?? '--' }}</div>
                                        <div class="truncate text-muted-foreground">{{ $entry->driver2->name ?? '' }}</div>
                                        @if ($entry->driver1_arrived_at || $entry->driver1_cutoff_at || $entry->replacementDriver1)
                                            <div class="mt-0.5 text-[10px] text-muted-foreground">
                                                D1 {{ $entry->driver1_arrived_at ? 'arr ' . $entry->driver1_arrived_at->format('H:i:s') : '' }}
                                                {{ $entry->driver1_cutoff_at ? 'cut ' . $entry->driver1_cutoff_at->format('H:i:s') : '' }}
                                                {{ $entry->replacementDriver1 ? '-> ' . $entry->replacementDriver1->name : '' }}
                                            </div>
                                        @endif
                                        @if ($entry->driver2_arrived_at || $entry->driver2_cutoff_at || $entry->replacementDriver2)
                                            <div class="text-[10px] text-muted-foreground">
                                                D2 {{ $entry->driver2_arrived_at ? 'arr ' . $entry->driver2_arrived_at->format('H:i:s') : '' }}
                                                {{ $entry->driver2_cutoff_at ? 'cut ' . $entry->driver2_cutoff_at->format('H:i:s') : '' }}
                                                {{ $entry->replacementDriver2 ? '-> ' . $entry->replacementDriver2->name : '' }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-2 py-1.5">
                                        <div class="flex flex-col gap-1">
                                            <span class="inline-flex w-fit items-center rounded px-1.5 py-0.5 text-[10px] font-semibold uppercase tracking-wide {{ $statusClasses[$entryStatus] ?? $statusClasses['scheduled'] }}">
                                                {{ ucwords(str_replace('_', ' ', $entryStatus)) }}
                                            </span>
                                            <div class="flex flex-wrap gap-1">
                                                @if (in_array($entryStatus, ['scheduled', 'delayed']))
                                                    <button
                                                        type="button"
                                                        wire:click="openStatusDialog({{ $entry->id }}, 'departed')"
                                                        class="rounded bg-blue-600 px-1.5 py-0.5 text-[10px] font-medium text-white hover:bg-blue-700"
                                                        title="Mark departed (stamps time + KMR out)"
                                                    >Depart</button>
                                                @endif
                                                @if (in_array($entryStatus, ['departed', 'on_route', 'delayed']))
                                                    <button
                                                        type="button"
                                                        wire:click="openStatusDialog({{ $entry->id }}, 'arrived')"
                                                        class="rounded bg-green-600 px-1.5 py-0.5 text-[10px] font-medium text-white hover:bg-green-700"
                                                        title="Mark arrived (stamps time + KMR in)"
                                                    >Arrive</button>
                                                @endif
                                                @if (in_array($entryStatus, ['scheduled', 'departed', 'on_route']))
                                                    <button
                                                        type="button"
                                                        wire:click="openStatusDialog({{ $entry->id }}, 'delayed')"
                                                        class="rounded bg-orange-500 px-1.5 py-0.5 text-[10px] font-medium text-white hover:bg-orange-600"
                                                    >Delay</button>
                                                @endif
                                                @if (in_array($entryStatus, ['scheduled', 'departed', 'on_route', 'delayed']))
                                                    <button
                                                        type="button"
                                                        wire:click="openStatusDialog({{ $entry->id }}, 'breakdown')"
                                                        class="rounded bg-yellow-500 px-1.5 py-0.5 text-[10px] font-medium text-white hover:bg-yellow-600"
                                                        title="Mark bus as under repair; trip details remain editable"
                                                    >Breakdown</button>
                                                @endif
                                                @if (in_array($entryStatus, ['scheduled', 'departed', 'delayed']))
                                                    <button
                                                        type="button"
                                                        wire:click="openStatusDialog({{ $entry->id }}, 'cancelled')"
                                                        class="rounded bg-red-600 px-1.5 py-0.5 text-[10px] font-medium text-white hover:bg-red-700"
                                                    >Cancel</button>
                                                @endif
                                                @if ($entryStatus === 'cancelled')
                                                    <button
                                                        type="button"
                                                        wire:click="transitionStatus({{ $entry->id }}, 'scheduled')"
                                                        class="rounded bg-gray-600 px-1.5 py-0.5 text-[10px] font-medium text-white hover:bg-gray-700"
                                                    >Reschedule</button>
                                                @endif
                                                @if ($entryStatus === 'breakdown')
                                                    <button
                                                        type="button"
                                                        wire:click="transitionStatus({{ $entry->id }}, 'scheduled')"
                                                        class="rounded bg-gray-600 px-1.5 py-0.5 text-[10px] font-medium text-white hover:bg-gray-700"
                                                    >Reset</button>
                                                @endif
                                            </div>
                                            <div class="mt-1 flex flex-wrap gap-1">
                                                @if ($entry->driver_id)
                                                    <button type="button" wire:click="openDriverEventDialog({{ $entry->id }}, 'driver1', 'arrived')" class="rounded border px-1.5 py-0.5 text-[10px] hover:bg-muted">D1 Arr</button>
                                                    <button type="button" wire:click="openDriverEventDialog({{ $entry->id }}, 'driver1', 'cutoff')" class="rounded border px-1.5 py-0.5 text-[10px] hover:bg-muted">D1 Cut</button>
                                                @endif
                                                @if ($entry->driver2_id)
                                                    <button type="button" wire:click="openDriverEventDialog({{ $entry->id }}, 'driver2', 'arrived')" class="rounded border px-1.5 py-0.5 text-[10px] hover:bg-muted">D2 Arr</button>
                                                    <button type="button" wire:click="openDriverEventDialog({{ $entry->id }}, 'driver2', 'cutoff')" class="rounded border px-1.5 py-0.5 text-[10px] hover:bg-muted">D2 Cut</button>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-2 py-1.5 truncate" title="{{ $entry->remarks ?? '' }}">{{ $entry->remarks ?? '--' }}</td>
                                    <td class="px-2 py-1.5">
                                        <div class="flex items-center gap-1">
                                            <button
                                                wire:click="openEditDialog({{ $entry->id }})"
                                                class="rounded p-1 text-muted-foreground hover:bg-muted hover:text-foreground"
                                                title="Edit"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                                            </button>
                                            <button
                                                wire:click="deleteEntry({{ $entry->id }})"
                                                wire:confirm="Are you sure you want to delete this entry?"
                                                class="rounded p-1 text-muted-foreground hover:bg-red-100 hover:text-red-600 dark:hover:bg-red-900 dark:hover:text-red-400"
                                                title="Delete"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="px-3 py-8 text-center text-sm text-muted-foreground">
                                        No entries yet. Click "Add Entry" to start dispatching.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Pagination footer --}}
            <div class="flex flex-wrap items-center justify-between gap-2 border-t p-3 text-xs">
                <div class="flex items-center gap-2">
                    <label class="text-muted-foreground">Rows per page:</label>
                    <select
                        wire:model.live="perPage"
                        class="rounded border border-input bg-transparent px-2 py-1 text-xs shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                    >
                        @foreach ($perPageOptions as $opt)
                            <option value="{{ $opt }}">{{ $opt }}</option>
                        @endforeach
                    </select>
                    <span class="text-muted-foreground">
                        Showing {{ $entries->firstItem() ?? 0 }}–{{ $entries->lastItem() ?? 0 }} of {{ $entries->total() }}
                    </span>
                </div>
                <div>
                    {{ $entries->onEachSide(1)->links() }}
                </div>
            </div>
        </div>
    @endif

    {{-- Add Entry Modal --}}
    <div
        x-data
        x-show="$wire.showAddDialog"
        x-transition.opacity
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
    >
        <div class="fixed inset-0 bg-black/50" x-on:click="$wire.showAddDialog = false"></div>
        <div class="app-modal-panel relative mx-auto my-8 max-w-2xl rounded-lg bg-background p-6 shadow-lg border">
            <div class="mb-4">
                <h2 class="text-lg font-semibold">Add Dispatch Entry</h2>
                <p class="text-sm text-muted-foreground">Add a new bus dispatch entry for this day.</p>
            </div>

            <form wire:submit="submitAddEntry" class="space-y-4">
                @include('livewire._entry-form', ['prefix' => 'add'])

                <div class="flex justify-end gap-2 pt-2">
                    <button
                        type="button"
                        x-on:click="$wire.showAddDialog = false"
                        class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring bg-primary text-primary-foreground shadow hover:bg-primary/90 h-9 px-4 py-2"
                    >
                        Add Entry
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Status Transition Modal --}}
    <div
        x-data
        x-show="$wire.showStatusDialog"
        x-transition.opacity
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
    >
        <div class="fixed inset-0 bg-black/50" x-on:click="$wire.showStatusDialog = false"></div>
        <div class="app-modal-panel relative mx-auto my-16 max-w-sm rounded-lg bg-background p-6 shadow-lg border">
            <div class="mb-4">
                <h2 class="text-lg font-semibold">
                    {{ $statusTo ? 'Mark ' . ucwords(str_replace('_', ' ', $statusTo)) : 'Update Status' }}
                </h2>
                <p class="text-sm text-muted-foreground">
                    {{ $statusEntryLabel ?: 'Dispatch entry' }} — records a dispatch event with timestamp and notes.
                </p>
            </div>
            <form wire:submit.prevent="confirmStatusDialog" class="space-y-4">
                <div>
                    <label class="mb-1 block text-sm font-medium">Event time</label>
                    <input type="datetime-local" step="1" wire:model="statusOccurredAt" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
                    @error('statusOccurredAt') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                @if (in_array($statusTo, ['departed', 'arrived'], true))
                    <div>
                        <label class="mb-1 block text-sm font-medium">
                            KMR {{ $statusTo === 'departed' ? 'Out' : 'In' }} reading
                        </label>
                        <input
                            type="number"
                            min="0"
                            step="1"
                            wire:model="statusKmr"
                            placeholder="{{ $statusKmrSuggested ?? '—' }}"
                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                        />
                        @if ($statusKmrSuggested)
                            <p class="mt-1 text-xs text-muted-foreground">Current vehicle KMR: {{ number_format($statusKmrSuggested) }}</p>
                        @endif
                    </div>
                @endif
                @if (in_array($statusTo, ['delayed', 'cancelled', 'breakdown'], true))
                    <div>
                        <label class="mb-1 block text-sm font-medium">Reason</label>
                        <select wire:model="statusReason" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                            <option value="">-- Select reason --</option>
                            @foreach ([
                                'Traffic / road condition',
                                'Terminal congestion',
                                'Driver late / unavailable',
                                'Bus mechanical issue',
                                'Passenger loading delay',
                                'Weather',
                                'Operations decision',
                                'Other',
                            ] as $reason)
                                <option value="{{ $reason }}">{{ $reason }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div>
                    <label class="mb-1 block text-sm font-medium">Notes</label>
                    <textarea wire:model="statusNotes" rows="3" class="flex w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" placeholder="Optional operations notes"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-1">
                    <button
                        type="button"
                        x-on:click="$wire.showStatusDialog = false"
                        class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2"
                    >Cancel</button>
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring bg-primary text-primary-foreground shadow hover:bg-primary/90 h-9 px-4 py-2"
                    >Confirm</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Driver Arrival / Cut-off Modal --}}
    <div
        x-data
        x-show="$wire.showDriverEventDialog"
        x-transition.opacity
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
    >
        <div class="fixed inset-0 bg-black/50" x-on:click="$wire.showDriverEventDialog = false"></div>
        <div class="app-modal-panel relative mx-auto my-16 max-w-md rounded-lg bg-background p-6 shadow-lg border">
            <div class="mb-4">
                <h2 class="text-lg font-semibold">
                    {{ strtoupper($driverEventSlot === 'driver1' ? 'D1' : 'D2') }} {{ $driverEventType === 'cutoff' ? 'Cut-off' : 'Arrival' }}
                </h2>
                <p class="text-sm text-muted-foreground">{{ $driverEventEntryLabel ?: 'Dispatch entry' }}</p>
            </div>
            <form wire:submit.prevent="confirmDriverEventDialog" class="space-y-4">
                <div>
                    <label class="mb-1 block text-sm font-medium">Event time</label>
                    <input type="datetime-local" step="1" wire:model="driverEventOccurredAt" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
                </div>
                @if ($driverEventType === 'cutoff')
                    <div>
                        <label class="mb-1 block text-sm font-medium">Replacement driver</label>
                        <select wire:model="driverEventReplacementDriverId" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                            <option value="">-- No replacement --</option>
                            @foreach ($drivers as $driver)
                                <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium">Reason</label>
                        <select wire:model="driverEventReason" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                            <option value="">-- Select reason --</option>
                            <option value="Reassigned to another bus">Reassigned to another bus</option>
                            <option value="End of duty / cutoff">End of duty / cutoff</option>
                            <option value="Medical / personal reason">Medical / personal reason</option>
                            <option value="Operations decision">Operations decision</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                @endif
                <div>
                    <label class="mb-1 block text-sm font-medium">Notes</label>
                    <textarea wire:model="driverEventNotes" rows="3" class="flex w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" placeholder="Optional driver event notes"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-1">
                    <button type="button" x-on:click="$wire.showDriverEventDialog = false" class="inline-flex h-9 items-center justify-center rounded-md border border-input bg-background px-4 text-sm font-medium shadow-sm hover:bg-accent">Cancel</button>
                    <button type="submit" class="inline-flex h-9 items-center justify-center rounded-md bg-primary px-4 text-sm font-medium text-primary-foreground shadow hover:bg-primary/90">Save</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Entry Modal --}}
    <div
        x-data
        x-show="$wire.showEditDialog"
        x-transition.opacity
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
    >
        <div class="fixed inset-0 bg-black/50" x-on:click="$wire.showEditDialog = false"></div>
        <div class="app-modal-panel relative mx-auto my-8 max-w-2xl rounded-lg bg-background p-6 shadow-lg border">
            <div class="mb-4">
                <h2 class="text-lg font-semibold">Edit Dispatch Entry</h2>
                <p class="text-sm text-muted-foreground">Update the dispatch entry details.</p>
            </div>

            <form wire:submit="submitEditEntry" class="space-y-4">
                @include('livewire._entry-form', ['prefix' => 'edit'])

                <div class="flex justify-end gap-2 pt-2">
                    <button
                        type="button"
                        x-on:click="$wire.showEditDialog = false"
                        class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring bg-primary text-primary-foreground shadow hover:bg-primary/90 h-9 px-4 py-2"
                    >
                        Update Entry
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
