@php use App\Enums\SmsStatus; @endphp

<div class="flex h-full flex-1 flex-col gap-4 p-4">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold">SMS Logs</h1>
            <p class="text-sm text-muted-foreground">Monitor SMS delivery and retry failed messages</p>
        </div>
        <button
            wire:click="openCreateModal"
            class="inline-flex items-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:bg-primary/90 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
            Add SMS Log
        </button>
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
        <input type="date" wire:model.live="dateFrom" class="flex h-9 rounded-md border border-input bg-transparent px-3 py-1 pr-10 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
        <input type="date" wire:model.live="dateTo" class="flex h-9 rounded-md border border-input bg-transparent px-3 py-1 pr-10 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
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
                                    <div class="flex items-center gap-2">
                                        <button
                                            wire:click="sendSms({{ $log->id }})"
                                            wire:confirm="Send SMS to {{ $log->recipient_phone }}?"
                                            class="inline-flex items-center rounded-md bg-green-50 px-2.5 py-1.5 text-xs font-medium text-green-700 hover:bg-green-100 dark:bg-green-900/30 dark:text-green-400 dark:hover:bg-green-900/50"
                                            title="Send SMS"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                                        </button>
                                        <button
                                            wire:click="openViewModal({{ $log->id }})"
                                            class="inline-flex items-center rounded-md bg-gray-50 px-2.5 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-100 dark:bg-gray-900/30 dark:text-gray-400 dark:hover:bg-gray-900/50"
                                            title="View"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                        </button>
                                        <button
                                            wire:click="openEditModal({{ $log->id }})"
                                            class="inline-flex items-center rounded-md bg-blue-50 px-2.5 py-1.5 text-xs font-medium text-blue-700 hover:bg-blue-100 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50"
                                            title="Edit"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                                        </button>
                                        <button
                                            wire:click="openDeleteModal({{ $log->id }})"
                                            class="inline-flex items-center rounded-md bg-red-50 px-2.5 py-1.5 text-xs font-medium text-red-700 hover:bg-red-100 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-900/50"
                                            title="Delete"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                                        </button>
                                        @if ($statusValue === 'failed')
                                            <button
                                                wire:click="retrySms({{ $log->id }})"
                                                wire:confirm="Retry sending SMS to {{ $log->recipient_phone }}?"
                                                class="inline-flex items-center rounded-md bg-orange-50 px-2.5 py-1.5 text-xs font-medium text-orange-700 hover:bg-orange-100 dark:bg-orange-900/30 dark:text-orange-400 dark:hover:bg-orange-900/50"
                                                title="Retry"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
                                            </button>
                                        @endif
                                    </div>
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

    {{-- Create Modal --}}
    @if ($showCreateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/80" wire:click="closeModals">
            <div class="relative w-full max-w-md rounded-lg border bg-background p-6 shadow-lg" wire:click.stop>
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-lg font-semibold">Add SMS Log</h2>
                    <button wire:click="closeModals" class="rounded-sm opacity-70 hover:opacity-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                    </button>
                </div>
                <form wire:submit="createSmsLog">
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium">Recipient Phone</label>
                            <input type="text" wire:model="recipientPhone" class="flex w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" placeholder="09XXXXXXXXX" />
                            @error('recipientPhone') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="text-sm font-medium">Message</label>
                            <textarea wire:model="message" rows="3" class="flex w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" placeholder="Max 160 characters"></textarea>
                            <span class="text-xs text-muted-foreground">{{ strlen($message) }}/160</span>
                            @error('message') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="text-sm font-medium">Status</label>
                            <select wire:model="status" class="flex w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                                <option value="pending">Pending</option>
                                <option value="sent">Sent</option>
                                <option value="failed">Failed</option>
                            </select>
                            @error('status') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="text-sm font-medium">Dispatch Entry (Optional)</label>
                            <input type="number" wire:model="dispatchEntryId" class="flex w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" placeholder="Entry ID" />
                            @error('dispatchEntryId') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end gap-2">
                        <button type="button" wire:click="closeModals" class="rounded-md border bg-background px-4 py-2 text-sm hover:bg-accent">Cancel</button>
                        <button type="submit" class="rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:bg-primary/90">Create</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Edit Modal --}}
    @if ($showEditModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/80" wire:click="closeModals">
            <div class="relative w-full max-w-md rounded-lg border bg-background p-6 shadow-lg" wire:click.stop>
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-lg font-semibold">Edit SMS Log</h2>
                    <button wire:click="closeModals" class="rounded-sm opacity-70 hover:opacity-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                    </button>
                </div>
                <form wire:submit="updateSmsLog">
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium">Recipient Phone</label>
                            <input type="text" wire:model="recipientPhone" class="flex w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" placeholder="09XXXXXXXXX" />
                            @error('recipientPhone') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="text-sm font-medium">Message</label>
                            <textarea wire:model="message" rows="3" class="flex w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" placeholder="Max 160 characters"></textarea>
                            <span class="text-xs text-muted-foreground">{{ strlen($message) }}/160</span>
                            @error('message') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="text-sm font-medium">Status</label>
                            <select wire:model="status" class="flex w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                                <option value="pending">Pending</option>
                                <option value="sent">Sent</option>
                                <option value="failed">Failed</option>
                            </select>
                            @error('status') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="text-sm font-medium">Dispatch Entry (Optional)</label>
                            <input type="number" wire:model="dispatchEntryId" class="flex w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" placeholder="Entry ID" />
                            @error('dispatchEntryId') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end gap-2">
                        <button type="button" wire:click="closeModals" class="rounded-md border bg-background px-4 py-2 text-sm hover:bg-accent">Cancel</button>
                        <button type="submit" class="rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:bg-primary/90">Update</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- View Modal --}}
    @if ($showViewModal)
        @php
            $log = \App\Models\SmsLog::with('dispatchEntry')->find($viewingSmsLogId);
        @endphp
        @if ($log)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/80" wire:click="closeModals">
                <div class="relative w-full max-w-md rounded-lg border bg-background p-6 shadow-lg" wire:click.stop>
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-lg font-semibold">SMS Log Details</h2>
                        <button wire:click="closeModals" class="rounded-sm opacity-70 hover:opacity-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                        </button>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="text-xs font-medium text-muted-foreground">Recipient</label>
                            <p class="text-sm font-mono">{{ $log->recipient_phone }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-muted-foreground">Message</label>
                            <p class="text-sm">{{ $log->message }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-muted-foreground">Status</label>
                            <p class="text-sm">
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
                            </p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-muted-foreground">Provider ID</label>
                            <p class="text-sm font-mono">{{ $log->provider_message_id ?? '--' }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-muted-foreground">Created At</label>
                            <p class="text-sm">{{ $log->created_at->format('M d, Y H:i:s') }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-muted-foreground">Dispatch Entry</label>
                            <p class="text-sm">{{ $log->dispatch_entry_id ?? '--' }}</p>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end">
                        <button type="button" wire:click="closeModals" class="rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:bg-primary/90">Close</button>
                    </div>
                </div>
            </div>
        @endif
    @endif

    {{-- Delete Modal --}}
    @if ($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/80" wire:click="closeModals">
            <div class="relative w-full max-w-sm rounded-lg border bg-background p-6 shadow-lg" wire:click.stop>
                <div class="mb-4">
                    <h2 class="text-lg font-semibold">Delete SMS Log</h2>
                    <p class="text-sm text-muted-foreground mt-1">Are you sure you want to delete this SMS log? This action cannot be undone.</p>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" wire:click="closeModals" class="rounded-md border bg-background px-4 py-2 text-sm hover:bg-accent">Cancel</button>
                    <button type="button" wire:click="deleteSmsLog" class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">Delete</button>
                </div>
            </div>
        </div>
    @endif
</div>
