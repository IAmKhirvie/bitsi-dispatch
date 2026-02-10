@extends('layouts.app')

@section('title', 'Trip Codes - BITSI Dispatch')

@section('content')
    <div class="flex h-full flex-1 flex-col gap-4 p-4">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold">Trip Codes</h1>
                <p class="text-sm text-muted-foreground">Manage trip code definitions and routes</p>
            </div>
            <a href="{{ route('admin.trip-codes.create') }}" class="inline-flex items-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground shadow hover:bg-primary/90">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 h-4 w-4"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                Add Trip Code
            </a>
        </div>

        @livewire('admin.trip-code-table')
    </div>
@endsection
