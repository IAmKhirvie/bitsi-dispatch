@extends('layouts.app')

@section('title', "Edit {$driver->name} - BITSI Dispatch")

@section('content')
    <div class="flex h-full flex-1 flex-col gap-4 p-4">
        <div class="mx-auto w-full max-w-2xl">
            <div class="rounded-xl border bg-card text-card-foreground shadow">
                <div class="p-6">
                    <h3 class="font-semibold leading-none tracking-tight">Edit Driver</h3>
                    <p class="text-sm text-muted-foreground">Update information for {{ $driver->name }}.</p>
                </div>
                <div class="p-6 pt-0">
                    <form method="POST" action="{{ route('admin.drivers.update', $driver) }}" class="space-y-4">
                        @csrf
                        @method('PUT')

                        @include('admin.drivers._form', ['driver' => $driver])

                        <div class="flex items-center justify-end gap-3 pt-4">
                            <a href="{{ route('admin.drivers.index') }}" class="inline-flex items-center rounded-md border border-input bg-background px-4 py-2 text-sm font-medium shadow-sm hover:bg-accent hover:text-accent-foreground">Cancel</a>
                            <button type="submit" class="inline-flex items-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground shadow hover:bg-primary/90">
                                Update Driver
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
