<div class="flex h-full flex-1 flex-col gap-4 p-4">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold">Audit Logs</h1>
            <p class="text-sm text-muted-foreground">Track who changed what and when</p>
        </div>
        <div class="relative flex items-center gap-1.5" x-data="{ showCustomExport: false, dateFrom: '', dateTo: '' }">
            <span class="text-xs font-medium text-muted-foreground mr-1">Export:</span>
            <a href="{{ route('admin.export.audit-logs', 'daily') }}" class="inline-flex items-center rounded-md border border-input bg-background px-3 py-1.5 text-xs font-medium shadow-sm hover:bg-accent hover:text-accent-foreground transition-colors">
                Daily
            </a>
            <a href="{{ route('admin.export.audit-logs', 'weekly') }}" class="inline-flex items-center rounded-md border border-input bg-background px-3 py-1.5 text-xs font-medium shadow-sm hover:bg-accent hover:text-accent-foreground transition-colors">
                Weekly
            </a>
            <a href="{{ route('admin.export.audit-logs', 'monthly') }}" class="inline-flex items-center rounded-md border border-input bg-background px-3 py-1.5 text-xs font-medium shadow-sm hover:bg-accent hover:text-accent-foreground transition-colors">
                Monthly
            </a>
            <button type="button" x-on:click="showCustomExport = !showCustomExport"
                class="inline-flex items-center rounded-md border border-orange-300 bg-orange-50 px-3 py-1.5 text-xs font-medium text-orange-700 shadow-sm hover:bg-orange-100 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                Custom
            </button>
            {{-- Custom date range popup --}}
            <div x-show="showCustomExport" x-on:click.away="showCustomExport = false" x-transition
                class="absolute top-full right-0 mt-1 z-50 rounded-lg border bg-card p-3 shadow-lg w-64">
                <div class="space-y-2">
                    <label class="text-xs font-medium text-muted-foreground">From</label>
                    <input type="date" x-model="dateFrom" max="{{ now()->toDateString() }}"
                        class="flex h-8 w-full rounded-md border border-input bg-background px-2 py-1 text-xs" />
                    <label class="text-xs font-medium text-muted-foreground">To</label>
                    <input type="date" x-model="dateTo" max="{{ now()->toDateString() }}" x-bind:min="dateFrom"
                        class="flex h-8 w-full rounded-md border border-input bg-background px-2 py-1 text-xs" />
                    <a x-bind:href="dateFrom && dateTo && dateFrom <= dateTo
                        ? '{{ route('admin.export.audit-logs.custom') }}?date_from=' + dateFrom + '&date_to=' + dateTo
                        : '#'"
                        x-bind:class="dateFrom && dateTo && dateFrom <= dateTo
                            ? 'inline-flex w-full items-center justify-center rounded-md bg-primary px-3 py-1.5 text-xs font-medium text-primary-foreground hover:bg-primary/90 transition-colors'
                            : 'inline-flex w-full items-center justify-center rounded-md bg-muted px-3 py-1.5 text-xs font-medium text-muted-foreground cursor-not-allowed'"
                        x-bind:aria-disabled="!(dateFrom && dateTo && dateFrom <= dateTo)">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Export
                    </a>
                </div>
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
                            <x-sortable-th field="created_at" label="Date/Time" :active="$sortField" :direction="$sortDirection" />
                            <x-sortable-th field="user" label="User" :active="$sortField" :direction="$sortDirection" />
                            <x-sortable-th field="action" label="Action" :active="$sortField" :direction="$sortDirection" />
                            <x-sortable-th field="auditable_type" label="Model" :active="$sortField" :direction="$sortDirection" />
                            <x-sortable-th field="auditable_id" label="Record ID" :active="$sortField" :direction="$sortDirection" />
                            <x-sortable-th field="ip_address" label="IP Address" :active="$sortField" :direction="$sortDirection" />
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Changes</th>
                        </tr>
                    </thead>
                    <tbody x-data="{ expanded: {} }">
                        @forelse ($logs as $log)
                            <tr class="border-b last:border-0 hover:bg-muted/30 transition-colors">
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
                                        <button type="button" x-on:click.prevent="expanded[{{ $log->id }}] = !expanded[{{ $log->id }}]" class="inline-flex items-center text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                            <span x-text="expanded[{{ $log->id }}] ? 'Hide' : 'View'"></span>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 h-3 w-3 transition-transform" x-bind:class="expanded[{{ $log->id }}] && 'rotate-180'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                                        </button>
                                    @else
                                        <span class="text-xs text-muted-foreground">--</span>
                                    @endif
                                </td>
                            </tr>
                            @if ($log->old_values || $log->new_values)
                                <tr x-cloak x-show="expanded[{{ $log->id }}]" class="bg-muted/20">
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

    <x-table-pagination :paginator="$logs" :options="$perPageOptions" />
</div>
