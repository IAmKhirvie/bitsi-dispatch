@extends('layouts.app')

@section('title', 'Create Trip Code - BITSI Dispatch')

@section('content')
    <div class="flex h-full flex-1 flex-col gap-4 p-4">
        <div class="mx-auto w-full max-w-2xl">
            <div class="rounded-xl border bg-card text-card-foreground shadow">
                <div class="p-6">
                    <h3 class="font-semibold leading-none tracking-tight">Create Trip Code</h3>
                    <p class="text-sm text-muted-foreground">Define a new trip code for bus dispatch.</p>
                </div>
                <div class="p-6 pt-0">
                    <form method="POST" action="{{ route('admin.trip-codes.store') }}" class="space-y-4">
                        @csrf

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label for="code" class="text-sm font-medium leading-none">Trip Code</label>
                                <input id="code" name="code" type="text" value="{{ old('code') }}" placeholder="e.g. TC-001" required class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
                                @error('code')
                                    <p class="text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="space-y-2">
                                <label for="operator" class="text-sm font-medium leading-none">Operator</label>
                                <input id="operator" name="operator" type="text" value="{{ old('operator') }}" placeholder="e.g. DLTB" required class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
                                @error('operator')
                                    <p class="text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label for="origin_terminal" class="text-sm font-medium leading-none">Origin Terminal</label>
                                <input id="origin_terminal" name="origin_terminal" type="text" value="{{ old('origin_terminal') }}" placeholder="e.g. Cubao" required class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
                                @error('origin_terminal')
                                    <p class="text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="space-y-2">
                                <label for="destination_terminal" class="text-sm font-medium leading-none">Destination Terminal</label>
                                <input id="destination_terminal" name="destination_terminal" type="text" value="{{ old('destination_terminal') }}" placeholder="e.g. Naga" required class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
                                @error('destination_terminal')
                                    <p class="text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label for="bus_type" class="text-sm font-medium leading-none">Bus Type</label>
                                <input id="bus_type" name="bus_type" type="text" value="{{ old('bus_type') }}" placeholder="e.g. Airconditioned" required class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
                                @error('bus_type')
                                    <p class="text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="space-y-2">
                                <label for="scheduled_departure_time" class="text-sm font-medium leading-none">Scheduled Departure</label>
                                <input id="scheduled_departure_time" name="scheduled_departure_time" type="time" value="{{ old('scheduled_departure_time') }}" required class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
                                @error('scheduled_departure_time')
                                    <p class="text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label for="direction" class="text-sm font-medium leading-none">Direction</label>
                            <select id="direction" name="direction" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                                <option value="SB" {{ old('direction', 'SB') === 'SB' ? 'selected' : '' }}>Southbound (SB)</option>
                                <option value="NB" {{ old('direction') === 'NB' ? 'selected' : '' }}>Northbound (NB)</option>
                            </select>
                            @error('direction')
                                <p class="text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center gap-2">
                            <input id="is_active" name="is_active" type="checkbox" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="h-4 w-4 rounded border border-primary shadow focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
                            <label for="is_active" class="text-sm font-medium leading-none">Active</label>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4">
                            <a href="{{ route('admin.trip-codes.index') }}" class="inline-flex items-center rounded-md border border-input bg-background px-4 py-2 text-sm font-medium shadow-sm hover:bg-accent hover:text-accent-foreground">Cancel</a>
                            <button type="submit" class="inline-flex items-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground shadow hover:bg-primary/90">
                                Create Trip Code
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
