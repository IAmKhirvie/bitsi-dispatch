@php use App\Enums\VehicleStatus; @endphp

<div class="flex h-full flex-1 flex-col gap-4 p-4">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold">Vehicles</h1>
            <p class="text-sm text-muted-foreground">Manage fleet vehicles and maintenance status</p>
        </div>
        <a href="{{ route('admin.vehicles.create') }}"
           class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring bg-primary text-primary-foreground shadow hover:bg-primary/90 h-9 px-4 py-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add Vehicle
        </a>
    </div>

    {{-- Flash Messages --}}
    @if (session('status'))
        <div class="rounded-md bg-green-50 p-3 text-sm text-green-700 dark:bg-green-900/30 dark:text-green-400">
            {{ session('status') }}
        </div>
    @endif

    {{-- Filters --}}
    <div class="flex flex-wrap items-center gap-3">
        <div class="relative flex-1 min-w-[200px] max-w-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Search vehicles..."
                class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 pl-9 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
            />
        </div>
        <select
            wire:model.live="statusFilter"
            class="flex h-9 rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
        >
            <option value="">All Statuses</option>
            @foreach (VehicleStatus::cases() as $status)
                <option value="{{ $status->value }}">{{ $status->label() }}</option>
            @endforeach
        </select>
    </div>

    {{-- Table --}}
    <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
        <div class="p-0">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b bg-muted/50">
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Bus No.</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Brand</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Type</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Plate No.</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Status</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">PMS</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Idle Days</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($vehicles as $vehicle)
                            <tr class="border-b last:border-0 hover:bg-muted/30 transition-colors">
                                <td class="px-4 py-3 font-semibold">{{ $vehicle->bus_number }}</td>
                                <td class="px-4 py-3">{{ $vehicle->brand }}</td>
                                <td class="px-4 py-3">{{ $vehicle->bus_type?->label() ?? $vehicle->bus_type }}</td>
                                <td class="px-4 py-3">{{ $vehicle->plate_number }}</td>
                                <td class="px-4 py-3">
                                    @php $vStatus = $vehicle->status ?? VehicleStatus::OK; @endphp
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $vStatus->badgeClass() }}">
                                        {{ $vStatus->label() }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $pmsPercentage = $vehicle->pms_percentage ?? 0;
                                        $pmsBarColor = $pmsPercentage >= 80 ? 'bg-red-500' : ($pmsPercentage >= 60 ? 'bg-orange-500' : 'bg-green-500');
                                    @endphp
                                    <div class="flex items-center gap-2">
                                        <div class="h-2 w-16 rounded-full bg-gray-200 dark:bg-gray-700">
                                            <div class="h-2 rounded-full transition-all {{ $pmsBarColor }}"
                                                 style="width: {{ min($pmsPercentage, 100) }}%">
                                            </div>
                                        </div>
                                        <span class="text-xs text-muted-foreground">{{ $vehicle->current_pms_value ?? 0 }}/{{ $vehicle->pms_threshold ?? 0 }}</span>
                                        @if ($vehicle->is_pms_warning)
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-orange-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="{{ ($vehicle->idle_days ?? 0) > 7 ? 'text-red-500 font-medium' : '' }}">
                                        {{ $vehicle->idle_days ?? 0 }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('admin.vehicles.edit', $vehicle) }}"
                                           class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors hover:bg-accent hover:text-accent-foreground h-8 w-8">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                                        </a>
                                        <button
                                            wire:click="deleteVehicle({{ $vehicle->id }})"
                                            wire:confirm="Are you sure you want to delete vehicle {{ $vehicle->bus_number }}?"
                                            class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors hover:bg-accent hover:text-accent-foreground h-8 w-8 text-red-500 hover:text-red-700"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-muted-foreground">No vehicles found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Pagination --}}
    @if ($vehicles->hasPages())
        <div class="mt-2">
            {{ $vehicles->links() }}
        </div>
    @endif
</div>
