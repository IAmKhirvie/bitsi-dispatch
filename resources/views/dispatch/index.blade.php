@extends('layouts.app')

@section('title', 'Dispatch Board - BITSI Dispatch')

@section('content')
    <div class="flex h-full flex-1 flex-col gap-4 p-4">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold">Dispatch Board</h1>
                <p class="text-sm text-muted-foreground">Manage daily bus dispatch operations</p>
            </div>
        </div>

        @livewire('dispatch-board', ['date' => $date ?? now()->toDateString()])
    </div>
@endsection
