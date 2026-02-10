<div class="flex h-full flex-1 flex-col gap-4 p-4">
    <div>
        <h1 class="text-2xl font-bold">Dispatch History</h1>
        <p class="text-sm text-muted-foreground">Search and filter past dispatch entries</p>
    </div>

    @php
        $statusClasses = [
            'scheduled' => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
            'departed' => 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300',
            'on_route' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300',
            'delayed' => 'bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-300',
            'cancelled' => 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300',
            'arrived' => 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300',
        ];
    @endphp

    {{-- Filters --}}
    <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
        <div class="p-6">
            <div class="space-y-4">
                <div class="flex flex-wrap items-end gap-4">
                    <div class="flex-1 min-w-[200px] max-w-sm space-y-2">
                        <label class="text-sm font-medium leading-none">Search</label>
                        <div class="relative">
                            <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                            <input
                                type="text"
                                wire:model.live.debounce.300ms="search"
                                placeholder="Bus number, route, trip code..."
                                class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 pl-9 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                            />
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium leading-none">From Date</label>
                        <input
                            type="date"
                            wire:model.live="dateFrom"
                            class="flex h-9 rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring w-40"
                        />
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium leading-none">To Date</label>
                        <input
                            type="date"
                            wire:model.live="dateTo"
                            class="flex h-9 rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring w-40"
                        />
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium leading-none">Direction</label>
                        <select
                            wire:model.live="direction"
                            class="flex h-9 rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                        >
                            <option value="">All</option>
                            <option value="SB">SB</option>
                            <option value="NB">NB</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium leading-none">Status</label>
                        <select
                            wire:model.live="status"
                            class="flex h-9 rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                        >
                            <option value="">All</option>
                            <option value="scheduled">Scheduled</option>
                            <option value="departed">Departed</option>
                            <option value="on_route">On Route</option>
                            <option value="delayed">Delayed</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="arrived">Arrived</option>
                        </select>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button
                        wire:click="clearFilters"
                        class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2"
                    >
                        Clear
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Results Table --}}
    <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
        <div class="p-4 pb-2">
            <h3 class="text-base font-semibold leading-none tracking-tight">
                Results
                <span class="ml-2 text-sm font-normal text-muted-foreground">({{ $entries->total() }} entries found)</span>
            </h3>
        </div>
        <div class="p-0">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b bg-muted/50">
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Date</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Brand</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Bus No.</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Trip Code</th>
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
                        @forelse ($entries as $entry)
                            <tr class="border-b last:border-0 hover:bg-muted/30 transition-colors">
                                <td class="whitespace-nowrap px-4 py-2 text-muted-foreground">{{ $entry->dispatchDay->service_date ?? '--' }}</td>
                                <td class="px-4 py-2">{{ $entry->brand ?? '--' }}</td>
                                <td class="px-4 py-2 font-semibold">{{ $entry->bus_number ?? '--' }}</td>
                                <td class="px-4 py-2">{{ $entry->tripCode->code ?? '--' }}</td>
                                <td class="px-4 py-2">{{ $entry->route ?? '--' }}</td>
                                <td class="px-4 py-2">
                                    @if ($entry->direction)
                                        @php $dir = $entry->direction?->value ?? $entry->direction; @endphp
                                        <span class="inline-flex items-center rounded px-1.5 py-0.5 text-xs font-medium {{ $dir === 'SB' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300' : 'bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300' }}">
                                            {{ $dir }}
                                        </span>
                                    @else
                                        --
                                    @endif
                                </td>
                                <td class="px-4 py-2">{{ $entry->scheduled_departure ? Str::substr($entry->scheduled_departure, 0, 5) : '--' }}</td>
                                <td class="px-4 py-2">{{ $entry->actual_departure ? Str::substr($entry->actual_departure, 0, 5) : '--' }}</td>
                                <td class="px-4 py-2">{{ $entry->driver->name ?? '--' }}</td>
                                <td class="px-4 py-2">
                                    @php
                                        $entryStatus = $entry->status?->value ?? $entry->status ?? 'scheduled';
                                    @endphp
                                    <span class="inline-flex items-center rounded px-1.5 py-0.5 text-xs font-medium {{ $statusClasses[$entryStatus] ?? $statusClasses['scheduled'] }}">
                                        {{ ucwords(str_replace('_', ' ', $entryStatus)) }}
                                    </span>
                                </td>
                                <td class="max-w-[120px] truncate px-4 py-2" title="{{ $entry->remarks ?? '' }}">{{ $entry->remarks ?? '--' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="px-4 py-8 text-center text-muted-foreground">
                                    No entries found. Try adjusting your filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Pagination --}}
    @if ($entries->hasPages())
        <div class="mt-2">
            {{ $entries->links() }}
        </div>
    @endif
</div>
