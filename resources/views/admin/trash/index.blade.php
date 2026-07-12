@extends('layouts.app')

@section('title', 'Trash · ' . $label . ' - BITSI Dispatch')

@section('content')
<div class="app-page flex h-full flex-1 flex-col gap-4 p-4" x-data="{ confirmOpen: null, confirmEmpty: false }">
    <div class="app-toolbar flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.trash.overview') }}"
               class="inline-flex h-8 w-8 items-center justify-center rounded-md text-muted-foreground hover:bg-muted hover:text-foreground" title="Back">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                    <path d="m15 18-6-6 6-6"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold">Trashed {{ $label }}</h1>
                <p class="text-sm text-muted-foreground">Restore items to bring them back, or delete permanently.</p>
            </div>
        </div>

        @if ($items->total() > 0)
            <button type="button" @click="confirmEmpty = true"
                    class="inline-flex h-9 items-center rounded-md border border-input bg-background px-3 text-sm font-medium shadow-sm hover:bg-accent">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 h-4 w-4">
                    <path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                </svg>
                Empty trash
            </button>
        @endif
    </div>

    @if (session('success'))
        <div class="rounded-md border bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div>
    @endif

    {{-- Search --}}
    <form method="GET" action="{{ route('admin.trash.index', $resource) }}" class="relative max-w-sm">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground">
            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
        </svg>
        <input type="text" name="search" value="{{ $search }}" placeholder="Search trashed items..."
               class="flex h-9 w-full rounded-md border border-input bg-transparent pl-9 pr-3 py-1 text-sm shadow-sm transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
    </form>

    {{-- Table --}}
    <div class="rounded-lg border bg-card">
        <div class="app-table-scroll overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b bg-muted/50">
                        @foreach ($columns as $col)
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">
                                {{ ucwords(str_replace('_', ' ', $col)) }}
                            </th>
                        @endforeach
                        <th class="px-4 py-3 text-left font-medium text-muted-foreground">Deleted at</th>
                        <th class="px-4 py-3 text-right font-medium text-muted-foreground">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $row)
                        <tr class="border-b last:border-0 hover:bg-muted/30 transition-colors">
                            @foreach ($columns as $col)
                                <td class="px-4 py-3">{{ $row->{$col} ?? '--' }}</td>
                            @endforeach
                            <td class="px-4 py-3 text-muted-foreground">{{ $row->deleted_at?->diffForHumans() }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <form method="POST" action="{{ route('admin.trash.restore', [$resource, $row->id]) }}" class="inline">
                                        @csrf
                                        <button type="submit"
                                                class="inline-flex h-8 items-center rounded-md px-2 text-xs font-medium text-muted-foreground hover:bg-muted hover:text-foreground">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1.5 h-3.5 w-3.5">
                                                <path d="M3 7v6h6"/><path d="M21 17a9 9 0 0 0-15-6.7L3 13"/>
                                            </svg>
                                            Restore
                                        </button>
                                    </form>

                                    <button type="button" @click="confirmOpen = {{ $row->id }}"
                                            class="inline-flex h-8 items-center rounded-md px-2 text-xs font-medium text-destructive hover:bg-destructive/10">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1.5 h-3.5 w-3.5">
                                            <path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                        </svg>
                                        Delete forever
                                    </button>

                                    {{-- Per-row confirm dialog --}}
                                    <div x-show="confirmOpen === {{ $row->id }}" x-cloak
                                         class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4"
                                         @click.self="confirmOpen = null">
                                        <div class="w-full max-w-md rounded-lg border bg-background p-6 shadow-lg">
                                            <h3 class="text-lg font-semibold">Permanently delete this item?</h3>
                                            <p class="mt-2 text-sm text-muted-foreground">
                                                This action cannot be undone. The record will be removed from the database.
                                            </p>
                                            <div class="mt-6 flex justify-end gap-2">
                                                <button type="button" @click="confirmOpen = null"
                                                        class="inline-flex h-9 items-center rounded-md border border-input bg-background px-4 text-sm font-medium hover:bg-accent">
                                                    Cancel
                                                </button>
                                                <form method="POST" action="{{ route('admin.trash.force-delete', [$resource, $row->id]) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="inline-flex h-9 items-center rounded-md bg-destructive px-4 text-sm font-medium text-destructive-foreground hover:bg-destructive/90">
                                                        Delete permanently
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($columns) + 2 }}" class="px-4 py-12 text-center text-muted-foreground">
                                Nothing in the {{ strtolower($label) }} trash.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if ($items->hasPages())
        <div>{{ $items->links() }}</div>
    @endif

    {{-- Empty trash confirm dialog --}}
    <div x-show="confirmEmpty" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4"
         @click.self="confirmEmpty = false">
        <div class="w-full max-w-md rounded-lg border bg-background p-6 shadow-lg">
            <h3 class="text-lg font-semibold">Empty {{ $label }} trash?</h3>
            <p class="mt-2 text-sm text-muted-foreground">
                This permanently deletes all {{ $items->total() }} item(s) in this trash bin. This action cannot be undone.
            </p>
            <div class="mt-6 flex justify-end gap-2">
                <button type="button" @click="confirmEmpty = false"
                        class="inline-flex h-9 items-center rounded-md border border-input bg-background px-4 text-sm font-medium hover:bg-accent">
                    Cancel
                </button>
                <form method="POST" action="{{ route('admin.trash.empty', $resource) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex h-9 items-center rounded-md bg-destructive px-4 text-sm font-medium text-destructive-foreground hover:bg-destructive/90">
                        Permanently delete all
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
