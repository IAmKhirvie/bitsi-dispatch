@props([
    'resource' => '',          // e.g. "vehicles", "drivers", "trip-codes", "users", "attendance", "audit-logs", "sms-logs"
    'routePrefix' => 'admin.export',
])

@php
    $periodRoute = "{$routePrefix}.{$resource}";
    $customRoute = "{$routePrefix}.{$resource}.custom";
@endphp

<div class="flex items-center gap-1.5 relative" x-data="{ showCustomExport: false, dateFrom: '', dateTo: '' }">
    <span class="text-xs font-medium text-muted-foreground mr-1">Export:</span>

    <a href="{{ route($periodRoute, 'daily') }}"
        class="inline-flex items-center rounded-md border border-input bg-background px-3 py-1.5 text-xs font-medium shadow-sm hover:bg-accent hover:text-accent-foreground transition-colors">
        Daily
    </a>
    <a href="{{ route($periodRoute, 'weekly') }}"
        class="inline-flex items-center rounded-md border border-input bg-background px-3 py-1.5 text-xs font-medium shadow-sm hover:bg-accent hover:text-accent-foreground transition-colors">
        Weekly
    </a>
    <a href="{{ route($periodRoute, 'monthly') }}"
        class="inline-flex items-center rounded-md border border-input bg-background px-3 py-1.5 text-xs font-medium shadow-sm hover:bg-accent hover:text-accent-foreground transition-colors">
        Monthly
    </a>

    <button x-on:click="showCustomExport = !showCustomExport"
        class="inline-flex items-center rounded-md border border-orange-300 bg-orange-50 px-3 py-1.5 text-xs font-medium text-orange-700 shadow-sm hover:bg-orange-100 transition-colors dark:border-orange-700 dark:bg-orange-900/30 dark:text-orange-300">
        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        Custom
    </button>

    <div x-show="showCustomExport" x-on:click.away="showCustomExport = false" x-transition
        class="app-mobile-dropdown absolute top-full right-0 mt-1 z-50 rounded-lg border bg-card p-3 shadow-lg w-64">
        <div class="space-y-2">
            <label class="text-xs font-medium text-muted-foreground">From</label>
            <input type="date" x-model="dateFrom" max="{{ now()->toDateString() }}"
                class="flex h-8 w-full rounded-md border border-input bg-background px-2 py-1 text-xs" />
            <label class="text-xs font-medium text-muted-foreground">To</label>
            <input type="date" x-model="dateTo" max="{{ now()->toDateString() }}" x-bind:min="dateFrom"
                class="flex h-8 w-full rounded-md border border-input bg-background px-2 py-1 text-xs" />
            <a x-bind:href="dateFrom && dateTo && dateFrom <= dateTo
                ? '{{ route($customRoute) }}?date_from=' + dateFrom + '&date_to=' + dateTo
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
