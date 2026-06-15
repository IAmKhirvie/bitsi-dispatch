@extends('layouts.app')

@section('title', 'Trash - BITSI Dispatch')

@section('content')
<div class="flex h-full flex-1 flex-col gap-4 p-4">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6">
                    <path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/>
                </svg>
                Trash
            </h1>
            <p class="text-sm text-muted-foreground">Recently deleted records. Restore or permanently delete them.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="rounded-md border bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div>
    @endif

    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($resources as $r)
            <a href="{{ route('admin.trash.index', $r['key']) }}"
               class="block rounded-lg border bg-card p-4 transition-colors hover:bg-muted/30">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-medium">{{ $r['label'] }}</p>
                        <p class="text-sm text-muted-foreground">
                            {{ $r['count'] }} deleted {{ $r['count'] === 1 ? 'record' : 'records' }}
                        </p>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5 text-muted-foreground">
                        <polyline points="9 18 15 12 9 6"/>
                    </svg>
                </div>
            </a>
        @endforeach
    </div>
</div>
@endsection
