@php
    use App\Enums\BusType;
    use App\Enums\VehicleStatus;
    use App\Enums\PmsUnit;
    $isEdit = isset($vehicle);
    $v = $isEdit ? (is_array($vehicle) ? (object) $vehicle : $vehicle) : null;
@endphp

<div class="grid grid-cols-2 gap-4">
    <div class="space-y-2">
        <label for="bus_number" class="text-sm font-medium leading-none">Bus Number</label>
        <input id="bus_number" name="bus_number" type="text" value="{{ old('bus_number', $v->bus_number ?? '') }}" placeholder="e.g. 2801" required class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
        @error('bus_number')
            <p class="text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>
    <div class="space-y-2">
        <label for="brand" class="text-sm font-medium leading-none">Brand</label>
        <input id="brand" name="brand" type="text" value="{{ old('brand', $v->brand ?? '') }}" placeholder="e.g. DLTB" required class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
        @error('brand')
            <p class="text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="grid grid-cols-2 gap-4">
    <div class="space-y-2">
        <label for="bus_type" class="text-sm font-medium leading-none">Bus Type</label>
        <select id="bus_type" name="bus_type" required class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
            <option value="">-- Select Bus Type --</option>
            @foreach (BusType::cases() as $type)
                <option value="{{ $type->value }}" {{ old('bus_type', $v->bus_type ?? '') === $type->value ? 'selected' : '' }}>
                    {{ $type->label() }}
                </option>
            @endforeach
        </select>
        @error('bus_type')
            <p class="text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>
    <div class="space-y-2">
        <label for="plate_number" class="text-sm font-medium leading-none">Plate Number</label>
        <input id="plate_number" name="plate_number" type="text" value="{{ old('plate_number', $v->plate_number ?? '') }}" placeholder="e.g. ABC 1234" required class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
        @error('plate_number')
            <p class="text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="grid grid-cols-2 gap-4">
    <div class="space-y-2">
        <label for="status" class="text-sm font-medium leading-none">Status</label>
        <select id="status" name="status" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
            @foreach (VehicleStatus::cases() as $status)
                <option value="{{ $status->value }}" {{ old('status', $v->status ?? 'OK') === $status->value ? 'selected' : '' }}>
                    {{ $status->label() }}
                </option>
            @endforeach
        </select>
        @error('status')
            <p class="text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="border-t pt-4">
    <h3 class="mb-3 font-semibold">Preventive Maintenance Schedule (PMS)</h3>
    <div class="grid grid-cols-3 gap-4">
        <div class="space-y-2">
            <label for="pms_unit" class="text-sm font-medium leading-none">PMS Unit</label>
            <select id="pms_unit" name="pms_unit" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                @foreach (PmsUnit::cases() as $unit)
                    <option value="{{ $unit->value }}" {{ old('pms_unit', $v->pms_unit ?? 'kilometers') === $unit->value ? 'selected' : '' }}>
                        {{ $unit->label() }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="space-y-2">
            <label for="pms_threshold" class="text-sm font-medium leading-none">PMS Threshold</label>
            <input id="pms_threshold" name="pms_threshold" type="number" value="{{ old('pms_threshold', $v->pms_threshold ?? 10000) }}" min="0" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
            @error('pms_threshold')
                <p class="text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>
        <div class="space-y-2">
            <label for="current_pms_value" class="text-sm font-medium leading-none">Current Value</label>
            <input id="current_pms_value" name="current_pms_value" type="number" value="{{ old('current_pms_value', $v->current_pms_value ?? 0) }}" min="0" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
            @error('current_pms_value')
                <p class="text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>
    </div>
    <div class="mt-4 space-y-2">
        <label for="last_pms_date" class="text-sm font-medium leading-none">Last PMS Date</label>
        <input id="last_pms_date" name="last_pms_date" type="date" value="{{ old('last_pms_date', $v->last_pms_date ?? '') }}" class="flex h-9 max-w-xs rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
    </div>
</div>
