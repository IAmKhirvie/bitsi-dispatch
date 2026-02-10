<div class="flex h-full flex-1 flex-col gap-4 p-4">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold">Drivers</h1>
            <p class="text-sm text-muted-foreground">Manage bus drivers and their status</p>
        </div>
        <a href="{{ route('admin.drivers.create') }}"
           class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring bg-primary text-primary-foreground shadow hover:bg-primary/90 h-9 px-4 py-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add Driver
        </a>
    </div>

    {{-- Flash Messages --}}
    @if (session('status'))
        <div class="rounded-md bg-green-50 p-3 text-sm text-green-700 dark:bg-green-900/30 dark:text-green-400">
            {{ session('status') }}
        </div>
    @endif
    @if (session('error'))
        <div class="rounded-md bg-red-50 p-3 text-sm text-red-700 dark:bg-red-900/30 dark:text-red-400">
            {{ session('error') }}
        </div>
    @endif

    {{-- Filters --}}
    <div class="flex flex-wrap items-center gap-3">
        <div class="relative flex-1 min-w-[200px] max-w-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Search drivers..."
                class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 pl-9 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
            />
        </div>
        <select
            wire:model.live="statusFilter"
            class="flex h-9 rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
        >
            <option value="">All Statuses</option>
            <option value="available">Available</option>
            <option value="dispatched">Dispatched</option>
            <option value="on_route">On Route</option>
            <option value="on_leave">On Leave</option>
        </select>
    </div>

    @php
        $statusConfig = [
            'available' => [
                'label' => 'Available',
                'badge' => 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300',
                'btnActive' => 'bg-green-600 text-white hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-600',
            ],
            'dispatched' => [
                'label' => 'Dispatched',
                'badge' => 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300',
                'btnActive' => 'bg-blue-600 text-white hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-600',
            ],
            'on_route' => [
                'label' => 'On Route',
                'badge' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300',
                'btnActive' => 'bg-indigo-600 text-white hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-600',
            ],
            'on_leave' => [
                'label' => 'On Leave',
                'badge' => 'bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-300',
                'btnActive' => 'bg-orange-600 text-white hover:bg-orange-700 dark:bg-orange-700 dark:hover:bg-orange-600',
            ],
        ];
        $allStatuses = ['available', 'dispatched', 'on_route', 'on_leave'];
    @endphp

    {{-- Table --}}
    <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
        <div class="p-0">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b bg-muted/50">
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Name</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Phone</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">License Number</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Active</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Status</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Quick Status</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($drivers as $driver)
                            <tr class="border-b last:border-0 hover:bg-muted/30 transition-colors">
                                <td class="px-4 py-3 font-medium">{{ $driver->name }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-1">
                                        @if ($driver->phone)
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-muted-foreground" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                        @endif
                                        {{ $driver->phone ?? '--' }}
                                    </div>
                                </td>
                                <td class="px-4 py-3">{{ $driver->license_number ?? '--' }}</td>
                                <td class="px-4 py-3">
                                    <button wire:click="toggleActive({{ $driver->id }})" class="cursor-pointer">
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $driver->is_active ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300' }}">
                                            {{ $driver->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </button>
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $driverStatus = $driver->status ?? 'available';
                                        $config = $statusConfig[$driverStatus] ?? $statusConfig['available'];
                                    @endphp
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $config['badge'] }}">
                                        {{ $config['label'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-1">
                                        @foreach ($allStatuses as $s)
                                            <button
                                                wire:click="updateStatus({{ $driver->id }}, '{{ $s }}')"
                                                class="rounded px-2 py-1 text-xs font-medium transition-colors {{ ($driver->status ?? 'available') === $s ? $statusConfig[$s]['btnActive'] : 'bg-muted text-muted-foreground hover:bg-muted/80' }}"
                                                title="Set {{ $statusConfig[$s]['label'] }}"
                                            >
                                                {{ $statusConfig[$s]['label'] }}
                                            </button>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('admin.drivers.edit', $driver) }}"
                                           class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors hover:bg-accent hover:text-accent-foreground h-8 w-8">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                                        </a>
                                        <button
                                            wire:click="deleteDriver({{ $driver->id }})"
                                            wire:confirm="Are you sure you want to delete driver {{ $driver->name }}?"
                                            class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors hover:bg-accent hover:text-accent-foreground h-8 w-8 text-red-500 hover:text-red-700"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-muted-foreground">No drivers found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Pagination --}}
    @if ($drivers->hasPages())
        <div class="mt-2">
            {{ $drivers->links() }}
        </div>
    @endif
</div>
