<div class="flex h-full flex-1 flex-col gap-4 p-4">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold">Driver Attendance</h1>
            <p class="text-sm text-muted-foreground">Track driver check-ins and attendance status</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.attendance.settings') }}"
               class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>
                Settings
            </a>
            <button
                wire:click="$set('date', '{{ now()->toDateString() }}')"
                class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                Today
            </button>
            <button
                wire:click="initializeToday"
                wire:confirm="Initialize attendance records for all drivers scheduled today?"
                class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-6.219-8.56"/><polyline points="21 3 21 9 15 9"/></svg>
                Initialize Today
            </button>
        </div>
    </div>

    {{-- Date Picker --}}
    <div class="flex items-center gap-3">
        <label class="text-sm font-medium">Date:</label>
        <input
            type="date"
            wire:model.live="date"
            class="flex h-9 rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring w-44"
        />
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

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
            <div class="p-4 pb-2">
                <p class="text-sm font-medium text-muted-foreground">Total</p>
            </div>
            <div class="p-4 pt-0">
                <div class="text-2xl font-bold">{{ $stats['total'] }}</div>
            </div>
        </div>
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
            <div class="p-4 pb-2">
                <p class="text-sm font-medium text-muted-foreground flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    Pending
                </p>
            </div>
            <div class="p-4 pt-0">
                <div class="text-2xl font-bold">{{ $stats['pending'] }}</div>
            </div>
        </div>
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
            <div class="p-4 pb-2">
                <p class="text-sm font-medium text-green-600 flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    On Time
                </p>
            </div>
            <div class="p-4 pt-0">
                <div class="text-2xl font-bold">{{ $stats['on_time'] }}</div>
            </div>
        </div>
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
            <div class="p-4 pb-2">
                <p class="text-sm font-medium text-yellow-600 flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>
                    Late
                </p>
            </div>
            <div class="p-4 pt-0">
                <div class="text-2xl font-bold">{{ $stats['late'] }}</div>
            </div>
        </div>
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
            <div class="p-4 pb-2">
                <p class="text-sm font-medium text-red-600 flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="17" y1="11" x2="22" y2="11"/></svg>
                    Absent
                </p>
            </div>
            <div class="p-4 pt-0">
                <div class="text-2xl font-bold">{{ $stats['absent'] }}</div>
            </div>
        </div>
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
            <div class="p-4 pb-2">
                <p class="text-sm font-medium text-blue-600 flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                    Excused
                </p>
            </div>
            <div class="p-4 pt-0">
                <div class="text-2xl font-bold">{{ $stats['excused'] }}</div>
            </div>
        </div>
    </div>

    @php
        $statusConfig = [
            'pending' => ['label' => 'Pending', 'badge' => 'bg-gray-100 text-gray-700 dark:bg-gray-900 dark:text-gray-300'],
            'on_time' => ['label' => 'On Time', 'badge' => 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300'],
            'late' => ['label' => 'Late', 'badge' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300'],
            'absent' => ['label' => 'Absent', 'badge' => 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300'],
            'excused' => ['label' => 'Excused', 'badge' => 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300'],
        ];
    @endphp

    {{-- Attendance Table --}}
    <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
        <div class="p-4 pb-2">
            <h3 class="text-base font-semibold leading-none tracking-tight">Attendance Records</h3>
            <p class="text-sm text-muted-foreground">Date: {{ $date }}</p>
        </div>
        <div class="p-0">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b bg-muted/50">
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Driver</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Trip / Route</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Scheduled</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Check In</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Check Out</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Status</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($attendances as $att)
                            <tr class="border-b last:border-0 hover:bg-muted/30 transition-colors">
                                <td class="px-4 py-3 font-medium">{{ $att->driver->name ?? '--' }}</td>
                                <td class="px-4 py-3">
                                    {{ $att->dispatchEntry->route ?? $att->dispatchEntry->tripCode->code ?? '--' }}
                                </td>
                                <td class="px-4 py-3">{{ $att->dispatchEntry->scheduled_departure ?? '--' }}</td>
                                <td class="px-4 py-3">{{ $att->check_in_time ?? '--' }}</td>
                                <td class="px-4 py-3">{{ $att->check_out_time ?? '--' }}</td>
                                <td class="px-4 py-3">
                                    @php
                                        $attStatus = $att->status ?? 'pending';
                                        $config = $statusConfig[$attStatus] ?? $statusConfig['pending'];
                                    @endphp
                                    <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium {{ $config['badge'] }}">
                                        {{ $config['label'] }}
                                    </span>
                                    @if ($attStatus === 'late' && $att->minutes_late)
                                        <span class="ml-1 text-xs text-muted-foreground">({{ $att->minutes_late }}m)</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3" x-data="{ showOverride: false, overrideStatus: '' }">
                                    <div class="flex items-center gap-1">
                                        @if ($attStatus === 'pending')
                                            <button
                                                x-data
                                                x-on:click="
                                                    let minutes = prompt('Enter minutes late:', '15');
                                                    if (minutes && !isNaN(Number(minutes))) {
                                                        $wire.markLate({{ $att->id }}, Number(minutes));
                                                    }
                                                "
                                                class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-xs font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-7 px-2 py-1"
                                            >
                                                Mark Late
                                            </button>
                                            <button
                                                wire:click="markAbsent({{ $att->id }})"
                                                wire:confirm="Mark {{ $att->driver->name ?? 'this driver' }} as absent?"
                                                class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-xs font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring bg-red-600 text-white shadow-sm hover:bg-red-700 h-7 px-2 py-1"
                                            >
                                                Absent
                                            </button>
                                        @else
                                            <button
                                                x-on:click="
                                                    let newStatus = prompt('Enter new status (on_time/late/absent/excused):');
                                                    if (newStatus && ['on_time', 'late', 'absent', 'excused'].includes(newStatus)) {
                                                        $wire.overrideStatus({{ $att->id }}, newStatus);
                                                    }
                                                "
                                                class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-xs font-medium transition-colors hover:bg-accent hover:text-accent-foreground h-7 px-2 py-1"
                                            >
                                                Override
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-muted-foreground">
                                    No attendance records found for this date.
                                    Click "Initialize Today" to create records for scheduled drivers.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Pagination --}}
    @if ($attendances->hasPages())
        <div class="mt-2">
            {{ $attendances->links() }}
        </div>
    @endif
</div>
