@extends('layouts.app')

@section('title', 'Reports - BITSI Dispatch')

@section('content')
    <div class="flex h-full flex-1 flex-col gap-4 p-4">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold">Reports</h1>
                <p class="text-sm text-muted-foreground">Dispatch reports and trip analytics</p>
            </div>
        </div>

        @livewire('report.report-summary-table')
    </div>
@endsection
