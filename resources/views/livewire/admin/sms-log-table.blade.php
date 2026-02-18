@php use App\Enums\SmsStatus; @endphp

<div class="flex h-full flex-1 flex-col gap-4 p-4">
    <div>
        <h1 class="text-2xl font-bold">SMS Logs</h1>
        <p class="text-sm text-muted-foreground">Monitor SMS delivery and retry failed messages</p>
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

    {{-- Stats Cards --}}
    <div class="grid gap-4 sm:grid-cols-3">
        <div class="rounded-xl border bg-card text-card-foreground shadow">
            <div class="flex flex-row items-center justify-between p-4">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Sent Today</p>
                    <p class="text-2xl font-bold text-green-600">{{ $todayStats['sent'] }}</p>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-500/30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 2 11 13"/><path d="m22 2-7 20-4-9-9-4z"/></svg>
            </div>
        </div>
        <div class="rounded-xl border bg-card text-card-foreground shadow">
            <div class="flex flex-row items-center justify-between p-4">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Failed Today</p>
                    <p class="text-2xl font-bold text-red-600">{{ $todayStats['failed'] }}</p>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-500/30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6"/><path d="m9 9 6 6"/></svg>
            </div>
        </div>
        <div class="rounded-xl border bg-card text-card-foreground shadow">
            <div class="flex flex-row items-center justify-between p-4">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Pending Today</p>
                    <p class="text-2xl font-bold text-orange-600">{{ $todayStats['pending'] }}</p>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-orange-500/30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="flex flex-wrap items-center gap-3">
        <div class="relative flex-1 min-w-[200px] max-w-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Search by phone or message..."
                class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 pl-9 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
            />
        </div>
        <select wire:model.live="statusFilter" class="flex h-9 rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
            <option value="">All Statuses</option>
            @foreach (SmsStatus::cases() as $status)
                <option value="{{ $status->value }}">{{ $status->label() }}</option>
            @endforeach
        </select>
        <input type="date" wire:model.live="dateFrom" class="flex h-9 rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
        <input type="date" wire:model.live="dateTo" class="flex h-9 rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
    </div>

    {{-- Table --}}
    <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
        <div class="p-0">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b bg-muted/50">
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Date</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Recipient</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Message</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Status</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Provider ID</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $log)
                            <tr class="border-b last:border-0 hover:bg-muted/30 transition-colors">
                                <td class="px-4 py-3 text-xs text-muted-foreground whitespace-nowrap">{{ $log->created_at->format('M d, Y H:i') }}</td>
                                <td class="px-4 py-3 font-mono text-xs">{{ $log->recipient_phone }}</td>
                                <td class="px-4 py-3 max-w-xs truncate" title="{{ $log->message }}">{{ Str::limit($log->message, 60) }}</td>
                                <td class="px-4 py-3">
                                    @php
                                        $smsStatusClasses = [
                                            'sent' => 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300',
                                            'failed' => 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300',
                                            'pending' => 'bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-300',
                                        ];
                                        $statusValue = $log->status?->value ?? $log->status;
                                    @endphp
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $smsStatusClasses[$statusValue] ?? 'bg-gray-100 text-gray-700' }}">
                                        {{ ucfirst($statusValue) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 font-mono text-xs text-muted-foreground">{{ $log->provider_message_id ?? '--' }}</td>
                                <td class="px-4 py-3">
                                    @if (($log->status?->value ?? $log->status) === 'failed')
                                        <button
                                            wire:click="retrySms({{ $log->id }})"
                                            wire:confirm="Retry sending SMS to {{ $log->recipient_phone }}?"
                                            class="inline-flex items-center rounded-md bg-blue-50 px-2.5 py-1.5 text-xs font-medium text-blue-700 hover:bg-blue-100 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
                                            Retry
                                        </button>
                                    @else
                                        <span class="text-xs text-muted-foreground">--</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-muted-foreground">No SMS logs found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Pagination --}}
    @if ($logs->hasPages())
        <div class="mt-2">
            {{ $logs->links() }}
        </div>
    @endif
</div>
