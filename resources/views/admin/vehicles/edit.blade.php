@extends('layouts.app')

@section('title', "Edit Vehicle {$vehicle->bus_number} - BITSI Dispatch")

@section('content')
    <div class="flex h-full flex-1 flex-col gap-4 p-4">
        <div class="mx-auto w-full max-w-2xl">
            <div class="rounded-xl border bg-card text-card-foreground shadow">
                <div class="p-6">
                    <h3 class="font-semibold leading-none tracking-tight">Edit Vehicle</h3>
                    <p class="text-sm text-muted-foreground">Update vehicle {{ $vehicle->brand }} {{ $vehicle->bus_number }}.</p>
                </div>
                <div class="p-6 pt-0">
                    <form method="POST" action="{{ route('admin.vehicles.update', $vehicle) }}" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label for="bus_number" class="text-sm font-medium leading-none">Bus Number</label>
                                <input id="bus_number" name="bus_number" type="text" value="{{ old('bus_number', $vehicle->bus_number) }}" placeholder="e.g. 2801" required class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
                                @error('bus_number')
                                    <p class="text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="space-y-2">
                                <label for="brand" class="text-sm font-medium leading-none">Brand</label>
                                <input id="brand" name="brand" type="text" value="{{ old('brand', $vehicle->brand) }}" placeholder="e.g. DLTB" required class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
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
                                    @foreach(['regular', 'deluxe', 'super_deluxe', 'elite', 'sleeper', 'single_seater', 'skybus'] as $type)
                                        <option value="{{ $type }}" {{ old('bus_type', $vehicle->bus_type) === $type ? 'selected' : '' }}>{{ ucwords(str_replace('_', ' ', $type)) }}</option>
                                    @endforeach
                                </select>
                                @error('bus_type')
                                    <p class="text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="space-y-2">
                                <label for="plate_number" class="text-sm font-medium leading-none">Plate Number</label>
                                <input id="plate_number" name="plate_number" type="text" value="{{ old('plate_number', $vehicle->plate_number) }}" placeholder="e.g. ABC 1234" required class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
                                @error('plate_number')
                                    <p class="text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label for="status" class="text-sm font-medium leading-none">Status</label>
                                <select id="status" name="status" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                                    <option value="OK" {{ old('status', $vehicle->status) === 'OK' ? 'selected' : '' }}>OK</option>
                                    <option value="UR" {{ old('status', $vehicle->status) === 'UR' ? 'selected' : '' }}>Under Repair (UR)</option>
                                    <option value="PMS" {{ old('status', $vehicle->status) === 'PMS' ? 'selected' : '' }}>PMS</option>
                                    <option value="In Transit" {{ old('status', $vehicle->status) === 'In Transit' ? 'selected' : '' }}>In Transit</option>
                                    <option value="Lutaw" {{ old('status', $vehicle->status) === 'Lutaw' ? 'selected' : '' }}>Lutaw</option>
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
                                        <option value="kilometers" {{ old('pms_unit', $vehicle->pms_unit) === 'kilometers' ? 'selected' : '' }}>Kilometers</option>
                                        <option value="trips" {{ old('pms_unit', $vehicle->pms_unit) === 'trips' ? 'selected' : '' }}>Trips</option>
                                    </select>
                                </div>
                                <div class="space-y-2">
                                    <label for="pms_threshold" class="text-sm font-medium leading-none">PMS Threshold</label>
                                    <input id="pms_threshold" name="pms_threshold" type="number" value="{{ old('pms_threshold', $vehicle->pms_threshold) }}" min="0" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
                                    @error('pms_threshold')
                                        <p class="text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="space-y-2">
                                    <label for="current_pms_value" class="text-sm font-medium leading-none">Current Value</label>
                                    <input id="current_pms_value" name="current_pms_value" type="number" value="{{ old('current_pms_value', $vehicle->current_pms_value) }}" min="0" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
                                    @error('current_pms_value')
                                        <p class="text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="mt-4 space-y-2">
                                <label for="last_pms_date" class="text-sm font-medium leading-none">Last PMS Date</label>
                                <input id="last_pms_date" name="last_pms_date" type="date" value="{{ old('last_pms_date', $vehicle->last_pms_date) }}" class="flex h-9 max-w-xs rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4">
                            <a href="{{ route('admin.vehicles.index') }}" class="inline-flex items-center rounded-md border border-input bg-background px-4 py-2 text-sm font-medium shadow-sm hover:bg-accent hover:text-accent-foreground">Cancel</a>
                            <button type="submit" class="inline-flex items-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground shadow hover:bg-primary/90">
                                Update Vehicle
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
