{{-- Shared dispatch entry form fields --}}
{{-- Expects: $prefix ('add' or 'edit'), $tripCodes, $vehicles, $drivers, $statusOptions --}}

<div class="grid grid-cols-2 gap-4">
    {{-- Trip Code --}}
    <div class="space-y-2">
        <label class="text-sm font-medium leading-none">Trip Code</label>
        <select
            wire:model.live="{{ $prefix }}TripCodeId"
            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
        >
            <option value="">-- Select Trip Code --</option>
            @foreach ($tripCodes as $tc)
                <option value="{{ $tc->id }}">{{ $tc->code }} ({{ $tc->origin_terminal }} - {{ $tc->destination_terminal }})</option>
            @endforeach
        </select>
        @error($prefix . 'TripCodeId') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
    </div>

    {{-- Vehicle --}}
    <div class="space-y-2">
        <label class="text-sm font-medium leading-none">Vehicle</label>
        <select
            wire:model.live="{{ $prefix }}VehicleId"
            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
        >
            <option value="">-- Select Vehicle --</option>
            @foreach ($vehicles as $v)
                <option value="{{ $v->id }}">{{ $v->brand }} {{ $v->bus_number }} ({{ $v->plate_number }})</option>
            @endforeach
        </select>
        @error($prefix . 'VehicleId') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
    </div>

    {{-- Driver 1 --}}
    <div class="space-y-2">
        <label class="text-sm font-medium leading-none">Driver 1</label>
        <select
            wire:model="{{ $prefix }}DriverId"
            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
        >
            <option value="">-- Select Driver --</option>
            @foreach ($drivers as $d)
                <option value="{{ $d->id }}">{{ $d->name }}</option>
            @endforeach
        </select>
        @error($prefix . 'DriverId') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
    </div>

    {{-- Driver 2 --}}
    <div class="space-y-2">
        <label class="text-sm font-medium leading-none">Driver 2</label>
        <select
            wire:model="{{ $prefix }}Driver2Id"
            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
        >
            <option value="">-- Select Driver --</option>
            @foreach ($drivers as $d)
                <option value="{{ $d->id }}">{{ $d->name }}</option>
            @endforeach
        </select>
        @error($prefix . 'Driver2Id') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
    </div>

    {{-- Brand --}}
    <div class="space-y-2">
        <label class="text-sm font-medium leading-none">Brand</label>
        <input
            type="text"
            wire:model="{{ $prefix }}Brand"
            placeholder="e.g. DLTB"
            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
        />
    </div>

    {{-- Bus Number --}}
    <div class="space-y-2">
        <label class="text-sm font-medium leading-none">Bus Number</label>
        <input
            type="text"
            wire:model="{{ $prefix }}BusNumber"
            placeholder="e.g. 2801"
            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
        />
    </div>

    {{-- Route --}}
    <div class="space-y-2">
        <label class="text-sm font-medium leading-none">Route</label>
        <input
            type="text"
            wire:model="{{ $prefix }}Route"
            placeholder="e.g. Cubao - Naga"
            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
        />
    </div>

    {{-- Bus Type --}}
    <div class="space-y-2">
        <label class="text-sm font-medium leading-none">Bus Type</label>
        <input
            type="text"
            wire:model="{{ $prefix }}BusType"
            placeholder="e.g. Airconditioned"
            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
        />
    </div>

    {{-- Departure Terminal --}}
    <div class="space-y-2">
        <label class="text-sm font-medium leading-none">Departure Terminal</label>
        <input
            type="text"
            wire:model="{{ $prefix }}DepartureTerminal"
            placeholder="e.g. Cubao"
            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
        />
    </div>

    {{-- Arrival Terminal --}}
    <div class="space-y-2">
        <label class="text-sm font-medium leading-none">Arrival Terminal</label>
        <input
            type="text"
            wire:model="{{ $prefix }}ArrivalTerminal"
            placeholder="e.g. Naga"
            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
        />
    </div>

    {{-- Scheduled Departure --}}
    <div class="space-y-2">
        <label class="text-sm font-medium leading-none">Sched. Departure</label>
        <input
            type="time"
            wire:model="{{ $prefix }}ScheduledDeparture"
            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
        />
    </div>

    {{-- Actual Departure --}}
    <div class="space-y-2">
        <label class="text-sm font-medium leading-none">Actual Departure</label>
        <input
            type="time"
            wire:model="{{ $prefix }}ActualDeparture"
            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
        />
    </div>

    {{-- Direction --}}
    <div class="space-y-2">
        <label class="text-sm font-medium leading-none">Direction</label>
        <select
            wire:model="{{ $prefix }}Direction"
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
            wire:model="{{ $prefix }}Status"
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
            wire:model="{{ $prefix }}Remarks"
            placeholder="Optional remarks"
            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
        />
    </div>
</div>
