<div class="flex h-full flex-1 flex-col gap-4 p-4">
    <div>
        <h1 class="text-2xl font-bold">Audit Logs</h1>
        <p class="text-sm text-muted-foreground">Track who changed what and when</p>
    </div>

    {{-- Filters --}}
    <div class="flex flex-wrap items-center gap-3">
        <div class="relative flex-1 min-w-[200px] max-w-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Search by user name..."
                class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 pl-9 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
            />
        </div>
        <select wire:model.live="actionFilter" class="flex h-9 rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
            <option value="">All Actions</option>
            <option value="created">Created</option>
            <option value="updated">Updated</option>
            <option value="deleted">Deleted</option>
            <option value="restored">Restored</option>
        </select>
        <select wire:model.live="modelFilter" class="flex h-9 rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
            <option value="">All Models</option>
            @foreach ($modelTypes as $type)
                <option value="{{ $type }}">{{ $this->getShortModelName($type) }}</option>
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
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Date/Time</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">User</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Action</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Model</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Record ID</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">IP Address</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Changes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $log)
                            <tr class="border-b last:border-0 hover:bg-muted/30 transition-colors" x-data="{ expanded: false }">
                                <td class="px-4 py-3 text-xs text-muted-foreground whitespace-nowrap">{{ $log->created_at->format('M d, Y H:i') }}</td>
                                <td class="px-4 py-3 font-medium">{{ $log->user?->name ?? 'System' }}</td>
                                <td class="px-4 py-3">
                                    @php
                                        $actionClasses = [
                                            'created' => 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300',
                                            'updated' => 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300',
                                            'deleted' => 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300',
                                            'restored' => 'bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300',
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $actionClasses[$log->action] ?? 'bg-gray-100 text-gray-700' }}">
                                        {{ ucfirst($log->action) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">{{ class_basename($log->auditable_type) }}</td>
                                <td class="px-4 py-3 font-mono text-xs">{{ $log->auditable_id }}</td>
                                <td class="px-4 py-3 text-xs text-muted-foreground">{{ $log->ip_address }}</td>
                                <td class="px-4 py-3">
                                    @if ($log->old_values || $log->new_values)
                                        <button x-on:click="expanded = !expanded" class="inline-flex items-center text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                            <span x-text="expanded ? 'Hide' : 'View'"></span>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 h-3 w-3 transition-transform" x-bind:class="expanded && 'rotate-180'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                                        </button>
                                    @else
                                        <span class="text-xs text-muted-foreground">--</span>
                                    @endif
                                </td>
                            </tr>
                            @if ($log->old_values || $log->new_values)
                                <tr x-show="expanded" x-collapse class="bg-muted/20">
                                    <td colspan="7" class="px-4 py-3">
                                        <div class="grid gap-3 md:grid-cols-2">
                                            @if ($log->old_values)
                                                <div>
                                                    <p class="mb-1 text-xs font-semibold text-red-600 dark:text-red-400">Old Values</p>
                                                    <pre class="rounded bg-muted p-2 text-xs overflow-x-auto">{{ json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                </div>
                                            @endif
                                            @if ($log->new_values)
                                                <div>
                                                    <p class="mb-1 text-xs font-semibold text-green-600 dark:text-green-400">New Values</p>
                                                    <pre class="rounded bg-muted p-2 text-xs overflow-x-auto">{{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-muted-foreground">No audit logs found.</td>
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
