<div class="flex h-full flex-1 flex-col gap-4 p-4" wire:poll.10s>
    @php
        $statusClasses = [
            'scheduled' => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
            'departed' => 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300',
            'on_route' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300',
            'delayed' => 'bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-300',
            'cancelled' => 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300',
            'arrived' => 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300',
        ];
        $statusOptions = ['scheduled', 'departed', 'on_route', 'delayed', 'cancelled', 'arrived'];
    @endphp

    {{-- Header with date picker --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold">Dispatch Board</h1>
            <p class="text-sm text-muted-foreground">Manage daily bus dispatch operations</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-xs text-muted-foreground" title="Auto-refreshes every 10 seconds">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 inline h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
                Live
            </span>
            <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-muted-foreground" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <input
                    type="date"
                    wire:model.live="date"
                    class="flex h-9 rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring w-44"
                />
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
                        <span class="ml-2 text-sm font-normal text-muted-foreground">({{ $dispatchDay->entries->count() }} entries)</span>
                    </h3>
                </div>
                <button
                    wire:click="$set('showAddDialog', true)"
                    class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring bg-primary text-primary-foreground shadow hover:bg-primary/90 h-9 px-4 py-2"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Add Entry
                </button>
            </div>
            <div class="p-0">
                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="border-b bg-muted/50">
                                <th class="whitespace-nowrap px-3 py-2 text-left font-medium text-muted-foreground">#</th>
                                <th class="whitespace-nowrap px-3 py-2 text-left font-medium text-muted-foreground">Brand</th>
                                <th class="whitespace-nowrap px-3 py-2 text-left font-medium text-muted-foreground">Bus No.</th>
                                <th class="whitespace-nowrap px-3 py-2 text-left font-medium text-muted-foreground">Trip Code</th>
                                <th class="whitespace-nowrap px-3 py-2 text-left font-medium text-muted-foreground">Route</th>
                                <th class="whitespace-nowrap px-3 py-2 text-left font-medium text-muted-foreground">Bus Type</th>
                                <th class="whitespace-nowrap px-3 py-2 text-left font-medium text-muted-foreground">Dep. Terminal</th>
                                <th class="whitespace-nowrap px-3 py-2 text-left font-medium text-muted-foreground">Arr. Terminal</th>
                                <th class="whitespace-nowrap px-3 py-2 text-left font-medium text-muted-foreground">Sched. Dep.</th>
                                <th class="whitespace-nowrap px-3 py-2 text-left font-medium text-muted-foreground">Actual Dep.</th>
                                <th class="whitespace-nowrap px-3 py-2 text-left font-medium text-muted-foreground">Dir.</th>
                                <th class="whitespace-nowrap px-3 py-2 text-left font-medium text-muted-foreground">Driver 1</th>
                                <th class="whitespace-nowrap px-3 py-2 text-left font-medium text-muted-foreground">Driver 2</th>
                                <th class="whitespace-nowrap px-3 py-2 text-left font-medium text-muted-foreground">Status</th>
                                <th class="whitespace-nowrap px-3 py-2 text-left font-medium text-muted-foreground">Remarks</th>
                                <th class="whitespace-nowrap px-3 py-2 text-left font-medium text-muted-foreground">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($dispatchDay->entries as $index => $entry)
                                <tr class="border-b hover:bg-muted/30 transition-colors">
                                    <td class="whitespace-nowrap px-3 py-1.5 text-muted-foreground">{{ $index + 1 }}</td>
                                    <td class="whitespace-nowrap px-3 py-1.5 font-medium">{{ $entry->brand ?? '--' }}</td>
                                    <td class="whitespace-nowrap px-3 py-1.5 font-semibold">{{ $entry->bus_number ?? '--' }}</td>
                                    <td class="whitespace-nowrap px-3 py-1.5">{{ $entry->tripCode->code ?? '--' }}</td>
                                    <td class="whitespace-nowrap px-3 py-1.5">{{ $entry->route ?? '--' }}</td>
                                    <td class="whitespace-nowrap px-3 py-1.5">{{ $entry->bus_type ?? '--' }}</td>
                                    <td class="whitespace-nowrap px-3 py-1.5">{{ $entry->departure_terminal ?? '--' }}</td>
                                    <td class="whitespace-nowrap px-3 py-1.5">{{ $entry->arrival_terminal ?? '--' }}</td>
                                    <td class="whitespace-nowrap px-3 py-1.5">{{ $entry->scheduled_departure ? Str::substr($entry->scheduled_departure, 0, 5) : '--' }}</td>
                                    <td class="whitespace-nowrap px-3 py-1.5">{{ $entry->actual_departure ? Str::substr($entry->actual_departure, 0, 5) : '--' }}</td>
                                    <td class="whitespace-nowrap px-3 py-1.5">
                                        @if ($entry->direction)
                                            @php $dir = $entry->direction?->value ?? $entry->direction; @endphp
                                            <span class="inline-flex items-center rounded px-1.5 py-0.5 text-xs font-medium {{ $dir === 'SB' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300' : 'bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300' }}">
                                                {{ $dir }}
                                            </span>
                                        @else
                                            --
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-1.5">{{ $entry->driver->name ?? '--' }}</td>
                                    <td class="whitespace-nowrap px-3 py-1.5">{{ $entry->driver2->name ?? '--' }}</td>
                                    <td class="whitespace-nowrap px-3 py-1.5">
                                        @php $entryStatus = $entry->status?->value ?? $entry->status ?? 'scheduled'; @endphp
                                        <span class="inline-flex items-center rounded px-1.5 py-0.5 text-xs font-medium {{ $statusClasses[$entryStatus] ?? $statusClasses['scheduled'] }}">
                                            {{ ucwords(str_replace('_', ' ', $entryStatus)) }}
                                        </span>
                                    </td>
                                    <td class="max-w-[120px] truncate px-3 py-1.5" title="{{ $entry->remarks ?? '' }}">{{ $entry->remarks ?? '--' }}</td>
                                    <td class="whitespace-nowrap px-3 py-1.5">
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
                                    <td colspan="16" class="px-3 py-8 text-center text-sm text-muted-foreground">
                                        No entries yet. Click "Add Entry" to start dispatching.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
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
        <div class="relative mx-auto my-8 max-w-2xl rounded-lg bg-background p-6 shadow-lg border">
            <div class="mb-4">
                <h2 class="text-lg font-semibold">Add Dispatch Entry</h2>
                <p class="text-sm text-muted-foreground">Add a new bus dispatch entry for this day.</p>
            </div>

            <form wire:submit="submitAddEntry" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    {{-- Trip Code --}}
                    <div class="space-y-2">
                        <label class="text-sm font-medium leading-none">Trip Code</label>
                        <select
                            wire:model.live="addTripCodeId"
                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                        >
                            <option value="">-- Select Trip Code --</option>
                            @foreach ($tripCodes as $tc)
                                <option value="{{ $tc->id }}">{{ $tc->code }} ({{ $tc->origin_terminal }} - {{ $tc->destination_terminal }})</option>
                            @endforeach
                        </select>
                        @error('addTripCodeId') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Vehicle --}}
                    <div class="space-y-2">
                        <label class="text-sm font-medium leading-none">Vehicle</label>
                        <select
                            wire:model.live="addVehicleId"
                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                        >
                            <option value="">-- Select Vehicle --</option>
                            @foreach ($vehicles as $v)
                                <option value="{{ $v->id }}">{{ $v->brand }} {{ $v->bus_number }} ({{ $v->plate_number }})</option>
                            @endforeach
                        </select>
                        @error('addVehicleId') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Driver 1 --}}
                    <div class="space-y-2">
                        <label class="text-sm font-medium leading-none">Driver 1</label>
                        <select
                            wire:model="addDriverId"
                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                        >
                            <option value="">-- Select Driver --</option>
                            @foreach ($drivers as $d)
                                <option value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </select>
                        @error('addDriverId') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Driver 2 --}}
                    <div class="space-y-2">
                        <label class="text-sm font-medium leading-none">Driver 2</label>
                        <select
                            wire:model="addDriver2Id"
                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                        >
                            <option value="">-- Select Driver --</option>
                            @foreach ($drivers as $d)
                                <option value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </select>
                        @error('addDriver2Id') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Brand --}}
                    <div class="space-y-2">
                        <label class="text-sm font-medium leading-none">Brand</label>
                        <input
                            type="text"
                            wire:model="addBrand"
                            placeholder="e.g. DLTB"
                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                        />
                    </div>

                    {{-- Bus Number --}}
                    <div class="space-y-2">
                        <label class="text-sm font-medium leading-none">Bus Number</label>
                        <input
                            type="text"
                            wire:model="addBusNumber"
                            placeholder="e.g. 2801"
                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                        />
                    </div>

                    {{-- Route --}}
                    <div class="space-y-2">
                        <label class="text-sm font-medium leading-none">Route</label>
                        <input
                            type="text"
                            wire:model="addRoute"
                            placeholder="e.g. Cubao - Naga"
                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                        />
                    </div>

                    {{-- Bus Type --}}
                    <div class="space-y-2">
                        <label class="text-sm font-medium leading-none">Bus Type</label>
                        <input
                            type="text"
                            wire:model="addBusType"
                            placeholder="e.g. Airconditioned"
                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                        />
                    </div>

                    {{-- Departure Terminal --}}
                    <div class="space-y-2">
                        <label class="text-sm font-medium leading-none">Departure Terminal</label>
                        <input
                            type="text"
                            wire:model="addDepartureTerminal"
                            placeholder="e.g. Cubao"
                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                        />
                    </div>

                    {{-- Arrival Terminal --}}
                    <div class="space-y-2">
                        <label class="text-sm font-medium leading-none">Arrival Terminal</label>
                        <input
                            type="text"
                            wire:model="addArrivalTerminal"
                            placeholder="e.g. Naga"
                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                        />
                    </div>

                    {{-- Scheduled Departure --}}
                    <div class="space-y-2">
                        <label class="text-sm font-medium leading-none">Sched. Departure</label>
                        <input
                            type="time"
                            wire:model="addScheduledDeparture"
                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                        />
                    </div>

                    {{-- Actual Departure --}}
                    <div class="space-y-2">
                        <label class="text-sm font-medium leading-none">Actual Departure</label>
                        <input
                            type="time"
                            wire:model="addActualDeparture"
                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                        />
                    </div>

                    {{-- Direction --}}
                    <div class="space-y-2">
                        <label class="text-sm font-medium leading-none">Direction</label>
                        <select
                            wire:model="addDirection"
                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                        >
                            <option value="">-- Select --</option>
                            <option value="SB">SB (Southbound)</option>
                            <option value="NB">NB (Northbound)</option>
                        </select>
                    </div>

                    {{-- Status --}}
                    <div class="space-y-2">
                        <label class="text-sm font-medium leading-none">Status</label>
                        <select
                            wire:model="addStatus"
                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                        >
                            @foreach ($statusOptions as $s)
                                <option value="{{ $s }}">{{ ucwords(str_replace('_', ' ', $s)) }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Remarks --}}
                    <div class="col-span-2 space-y-2">
                        <label class="text-sm font-medium leading-none">Remarks</label>
                        <input
                            type="text"
                            wire:model="addRemarks"
                            placeholder="Optional remarks"
                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                        />
                    </div>
                </div>

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

    {{-- Edit Entry Modal --}}
    <div
        x-data
        x-show="$wire.showEditDialog"
        x-transition.opacity
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
    >
        <div class="fixed inset-0 bg-black/50" x-on:click="$wire.showEditDialog = false"></div>
        <div class="relative mx-auto my-8 max-w-2xl rounded-lg bg-background p-6 shadow-lg border">
            <div class="mb-4">
                <h2 class="text-lg font-semibold">Edit Dispatch Entry</h2>
                <p class="text-sm text-muted-foreground">Update the dispatch entry details.</p>
            </div>

            <form wire:submit="submitEditEntry" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    {{-- Trip Code --}}
                    <div class="space-y-2">
                        <label class="text-sm font-medium leading-none">Trip Code</label>
                        <select
                            wire:model.live="editTripCodeId"
                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                        >
                            <option value="">-- Select Trip Code --</option>
                            @foreach ($tripCodes as $tc)
                                <option value="{{ $tc->id }}">{{ $tc->code }} ({{ $tc->origin_terminal }} - {{ $tc->destination_terminal }})</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Vehicle --}}
                    <div class="space-y-2">
                        <label class="text-sm font-medium leading-none">Vehicle</label>
                        <select
                            wire:model.live="editVehicleId"
                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                        >
                            <option value="">-- Select Vehicle --</option>
                            @foreach ($vehicles as $v)
                                <option value="{{ $v->id }}">{{ $v->brand }} {{ $v->bus_number }} ({{ $v->plate_number }})</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Driver 1 --}}
                    <div class="space-y-2">
                        <label class="text-sm font-medium leading-none">Driver 1</label>
                        <select
                            wire:model="editDriverId"
                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                        >
                            <option value="">-- Select Driver --</option>
                            @foreach ($drivers as $d)
                                <option value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Driver 2 --}}
                    <div class="space-y-2">
                        <label class="text-sm font-medium leading-none">Driver 2</label>
                        <select
                            wire:model="editDriver2Id"
                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                        >
                            <option value="">-- Select Driver --</option>
                            @foreach ($drivers as $d)
                                <option value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Brand --}}
                    <div class="space-y-2">
                        <label class="text-sm font-medium leading-none">Brand</label>
                        <input
                            type="text"
                            wire:model="editBrand"
                            placeholder="e.g. DLTB"
                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                        />
                    </div>

                    {{-- Bus Number --}}
                    <div class="space-y-2">
                        <label class="text-sm font-medium leading-none">Bus Number</label>
                        <input
                            type="text"
                            wire:model="editBusNumber"
                            placeholder="e.g. 2801"
                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                        />
                    </div>

                    {{-- Route --}}
                    <div class="space-y-2">
                        <label class="text-sm font-medium leading-none">Route</label>
                        <input
                            type="text"
                            wire:model="editRoute"
                            placeholder="e.g. Cubao - Naga"
                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                        />
                    </div>

                    {{-- Bus Type --}}
                    <div class="space-y-2">
                        <label class="text-sm font-medium leading-none">Bus Type</label>
                        <input
                            type="text"
                            wire:model="editBusType"
                            placeholder="e.g. Airconditioned"
                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                        />
                    </div>

                    {{-- Departure Terminal --}}
                    <div class="space-y-2">
                        <label class="text-sm font-medium leading-none">Departure Terminal</label>
                        <input
                            type="text"
                            wire:model="editDepartureTerminal"
                            placeholder="e.g. Cubao"
                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                        />
                    </div>

                    {{-- Arrival Terminal --}}
                    <div class="space-y-2">
                        <label class="text-sm font-medium leading-none">Arrival Terminal</label>
                        <input
                            type="text"
                            wire:model="editArrivalTerminal"
                            placeholder="e.g. Naga"
                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                        />
                    </div>

                    {{-- Scheduled Departure --}}
                    <div class="space-y-2">
                        <label class="text-sm font-medium leading-none">Sched. Departure</label>
                        <input
                            type="time"
                            wire:model="editScheduledDeparture"
                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                        />
                    </div>

                    {{-- Actual Departure --}}
                    <div class="space-y-2">
                        <label class="text-sm font-medium leading-none">Actual Departure</label>
                        <input
                            type="time"
                            wire:model="editActualDeparture"
                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                        />
                    </div>

                    {{-- Direction --}}
                    <div class="space-y-2">
                        <label class="text-sm font-medium leading-none">Direction</label>
                        <select
                            wire:model="editDirection"
                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                        >
                            <option value="">-- Select --</option>
                            <option value="SB">SB (Southbound)</option>
                            <option value="NB">NB (Northbound)</option>
                        </select>
                    </div>

                    {{-- Status --}}
                    <div class="space-y-2">
                        <label class="text-sm font-medium leading-none">Status</label>
                        <select
                            wire:model="editStatus"
                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                        >
                            @foreach ($statusOptions as $s)
                                <option value="{{ $s }}">{{ ucwords(str_replace('_', ' ', $s)) }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Remarks --}}
                    <div class="col-span-2 space-y-2">
                        <label class="text-sm font-medium leading-none">Remarks</label>
                        <input
                            type="text"
                            wire:model="editRemarks"
                            placeholder="Optional remarks"
                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                        />
                    </div>
                </div>

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
