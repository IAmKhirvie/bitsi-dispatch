@extends('layouts.app')

@section('title', 'Dispatch History - BITSI Dispatch')

@section('content')
    <div class="flex h-full flex-1 flex-col gap-4 p-4">
        <div>
            <h1 class="text-2xl font-bold">Dispatch History</h1>
            <p class="text-sm text-muted-foreground">Search and filter past dispatch entries</p>
        </div>

        @livewire('history-table')
    </div>
@endsection
