@php
    use App\Enums\DriverStatus;
    $isEdit = isset($driver);
@endphp

<div class="space-y-2">
    <label for="name" class="text-sm font-medium leading-none">Full Name</label>
    <input id="name" name="name" type="text" value="{{ old('name', $isEdit ? $driver->name : '') }}" placeholder="Driver full name" required class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
    @error('name')
        <p class="text-xs text-red-500">{{ $message }}</p>
    @enderror
</div>

<div class="grid grid-cols-2 gap-4">
    <div class="space-y-2">
        <label for="phone" class="text-sm font-medium leading-none">Phone</label>
        <input id="phone" name="phone" type="text" value="{{ old('phone', $isEdit ? $driver->phone : '') }}" placeholder="09XX XXX XXXX" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
        @error('phone')
            <p class="text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>
    <div class="space-y-2">
        <label for="license_number" class="text-sm font-medium leading-none">License Number</label>
        <input id="license_number" name="license_number" type="text" value="{{ old('license_number', $isEdit ? $driver->license_number : '') }}" placeholder="Driver's license number" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
        @error('license_number')
            <p class="text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="space-y-2">
    <label for="status" class="text-sm font-medium leading-none">Status</label>
    <select id="status" name="status" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
        @foreach (DriverStatus::cases() as $status)
            <option value="{{ $status->value }}" {{ old('status', $isEdit ? ($driver->status?->value ?? 'available') : 'available') === $status->value ? 'selected' : '' }}>
                {{ $status->label() }}
            </option>
        @endforeach
    </select>
    @error('status')
        <p class="text-xs text-red-500">{{ $message }}</p>
    @enderror
</div>

<div class="flex items-center gap-2">
    <input id="is_active" name="is_active" type="checkbox" value="1" {{ old('is_active', $isEdit ? $driver->is_active : true) ? 'checked' : '' }} class="h-4 w-4 rounded border border-primary shadow focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
    <label for="is_active" class="text-sm font-medium leading-none">Active</label>
</div>
