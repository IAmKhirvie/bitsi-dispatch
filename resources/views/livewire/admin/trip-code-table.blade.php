@php use App\Enums\Direction; @endphp

<div class="flex h-full flex-1 flex-col gap-4 p-4">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold">Trip Codes</h1>
            <p class="text-sm text-muted-foreground">Manage trip code definitions and routes</p>
        </div>
        <a href="{{ route('admin.trip-codes.create') }}"
           class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring bg-primary text-primary-foreground shadow hover:bg-primary/90 h-9 px-4 py-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add Trip Code
        </a>
    </div>

    {{-- Flash Messages --}}
    @if (session('status'))
        <div class="rounded-md bg-green-50 p-3 text-sm text-green-700 dark:bg-green-900/30 dark:text-green-400">
            {{ session('status') }}
        </div>
    @endif

    {{-- Filters --}}
    <div class="flex flex-wrap items-center gap-3">
        <div class="relative flex-1 min-w-[200px] max-w-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Search trip codes..."
                class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 pl-9 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
            />
        </div>
        <select
            wire:model.live="directionFilter"
            class="flex h-9 rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
        >
            <option value="">All Directions</option>
            @foreach (Direction::cases() as $dir)
                <option value="{{ $dir->value }}">{{ $dir->label() }} ({{ $dir->value }})</option>
            @endforeach
        </select>
        <button
            wire:click="$toggle('showTrashed')"
            class="inline-flex h-9 items-center rounded-md border px-3 text-sm font-medium transition-colors {{ $showTrashed ? 'border-red-300 bg-red-50 text-red-700 dark:border-red-700 dark:bg-red-900/30 dark:text-red-400' : 'border-input bg-transparent hover:bg-accent hover:text-accent-foreground' }}"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1.5 h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
            {{ $showTrashed ? 'Showing Deleted' : 'Show Deleted' }}
        </button>
    </div>

    {{-- Table --}}
    <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
        <div class="p-0">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b bg-muted/50">
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Code</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Operator</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Route</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Bus Type</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Departure</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Direction</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Status</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tripCodes as $tc)
                            <tr class="border-b last:border-0 hover:bg-muted/30 transition-colors {{ $tc->trashed() ? 'opacity-60' : '' }}">
                                <td class="px-4 py-3 font-semibold">{{ $tc->code }}</td>
                                <td class="px-4 py-3">{{ $tc->operator }}</td>
                                <td class="px-4 py-3">{{ $tc->origin_terminal }} - {{ $tc->destination_terminal }}</td>
                                <td class="px-4 py-3">{{ $tc->bus_type?->label() ?? $tc->bus_type }}</td>
                                <td class="px-4 py-3">{{ $tc->scheduled_departure_time ? Str::substr($tc->scheduled_departure_time, 0, 5) : '--' }}</td>
                                <td class="px-4 py-3">
                                    @php $tcDir = $tc->direction ?? Direction::SB; @endphp
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $tcDir->badgeClass() }}">
                                        {{ $tcDir->value }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <button wire:click="toggleActive({{ $tc->id }})" class="cursor-pointer">
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $tc->is_active ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300' }}">
                                            {{ $tc->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </button>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        @if ($tc->trashed())
                                            <button
                                                wire:click="restoreTripCode({{ $tc->id }})"
                                                wire:confirm="Restore trip code {{ $tc->code }}?"
                                                class="inline-flex items-center rounded-md bg-green-50 px-2.5 py-1.5 text-xs font-medium text-green-700 hover:bg-green-100 dark:bg-green-900/30 dark:text-green-400 dark:hover:bg-green-900/50"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
                                                Restore
                                            </button>
                                        @else
                                            <a href="{{ route('admin.trip-codes.edit', $tc) }}"
                                               class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors hover:bg-accent hover:text-accent-foreground h-8 w-8">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                                            </a>
                                            <button
                                                wire:click="deleteTripCode({{ $tc->id }})"
                                                wire:confirm="Are you sure you want to delete trip code {{ $tc->code }}?"
                                                class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors hover:bg-accent hover:text-accent-foreground h-8 w-8 text-red-500 hover:text-red-700"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-muted-foreground">No trip codes found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Pagination --}}
    @if ($tripCodes->hasPages())
        <div class="mt-2">
            {{ $tripCodes->links() }}
        </div>
    @endif
</div>
