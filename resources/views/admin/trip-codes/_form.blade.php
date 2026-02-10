@php
    use App\Enums\BusType;
    use App\Enums\Direction;
    $isEdit = isset($tripCode);
@endphp

<div class="grid grid-cols-2 gap-4">
    <div class="space-y-2">
        <label for="code" class="text-sm font-medium leading-none">Trip Code</label>
        <input id="code" name="code" type="text" value="{{ old('code', $isEdit ? $tripCode->code : '') }}" placeholder="e.g. TC-001" required class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
        @error('code')
            <p class="text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>
    <div class="space-y-2">
        <label for="operator" class="text-sm font-medium leading-none">Operator</label>
        <input id="operator" name="operator" type="text" value="{{ old('operator', $isEdit ? $tripCode->operator : '') }}" placeholder="e.g. DLTB" required class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
        @error('operator')
            <p class="text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="grid grid-cols-2 gap-4">
    <div class="space-y-2">
        <label for="origin_terminal" class="text-sm font-medium leading-none">Origin Terminal</label>
        <input id="origin_terminal" name="origin_terminal" type="text" value="{{ old('origin_terminal', $isEdit ? $tripCode->origin_terminal : '') }}" placeholder="e.g. Cubao" required class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
        @error('origin_terminal')
            <p class="text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>
    <div class="space-y-2">
        <label for="destination_terminal" class="text-sm font-medium leading-none">Destination Terminal</label>
        <input id="destination_terminal" name="destination_terminal" type="text" value="{{ old('destination_terminal', $isEdit ? $tripCode->destination_terminal : '') }}" placeholder="e.g. Naga" required class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
        @error('destination_terminal')
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
                <option value="{{ $type->value }}" {{ old('bus_type', $isEdit ? ($tripCode->bus_type?->value ?? $tripCode->bus_type) : '') === $type->value ? 'selected' : '' }}>
                    {{ $type->label() }}
                </option>
            @endforeach
        </select>
        @error('bus_type')
            <p class="text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>
    <div class="space-y-2">
        <label for="scheduled_departure_time" class="text-sm font-medium leading-none">Scheduled Departure</label>
        <input id="scheduled_departure_time" name="scheduled_departure_time" type="time" value="{{ old('scheduled_departure_time', $isEdit ? $tripCode->scheduled_departure_time : '') }}" required class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
        @error('scheduled_departure_time')
            <p class="text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="space-y-2">
    <label for="direction" class="text-sm font-medium leading-none">Direction</label>
    <select id="direction" name="direction" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
        @foreach (Direction::cases() as $dir)
            <option value="{{ $dir->value }}" {{ old('direction', $isEdit ? ($tripCode->direction?->value ?? $tripCode->direction) : 'SB') === $dir->value ? 'selected' : '' }}>
                {{ $dir->label() }} ({{ $dir->value }})
            </option>
        @endforeach
    </select>
    @error('direction')
        <p class="text-xs text-red-500">{{ $message }}</p>
    @enderror
</div>

<div class="flex items-center gap-2">
    <input id="is_active" name="is_active" type="checkbox" value="1" {{ old('is_active', $isEdit ? $tripCode->is_active : true) ? 'checked' : '' }} class="h-4 w-4 rounded border border-primary shadow focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
    <label for="is_active" class="text-sm font-medium leading-none">Active</label>
</div>
