@props([
    'periodRoute',
    'customRoute',
    'dateFrom' => '',
    'dateTo' => '',
    'filteredQuery' => [],
])

@php
    $cleanFilteredQuery = array_filter($filteredQuery, fn ($value) => filled($value));
@endphp

<div
    class="relative flex items-center gap-1.5"
    x-data="{
        showCustomExport: false,
        dateFrom: @js($dateFrom),
        dateTo: @js($dateTo),
    }"
>
    <span class="mr-1 text-xs font-medium text-muted-foreground">Export:</span>

    <a href="{{ route($periodRoute, 'daily') }}"
       class="inline-flex items-center rounded-md border border-input bg-background px-3 py-1.5 text-xs font-medium shadow-sm transition-colors hover:bg-accent hover:text-accent-foreground">
        Daily
    </a>
    <a href="{{ route($periodRoute, 'weekly') }}"
       class="inline-flex items-center rounded-md border border-input bg-background px-3 py-1.5 text-xs font-medium shadow-sm transition-colors hover:bg-accent hover:text-accent-foreground">
        Weekly
    </a>
    <a href="{{ route($periodRoute, 'monthly') }}"
       class="inline-flex items-center rounded-md border border-input bg-background px-3 py-1.5 text-xs font-medium shadow-sm transition-colors hover:bg-accent hover:text-accent-foreground">
        Monthly
    </a>

    @if (! empty($cleanFilteredQuery))
        <a href="{{ route($customRoute, $cleanFilteredQuery) }}"
           class="inline-flex items-center rounded-md border border-input bg-background px-3 py-1.5 text-xs font-medium shadow-sm transition-colors hover:bg-accent hover:text-accent-foreground">
            Filtered
        </a>
    @endif

    <button
        type="button"
        x-on:click="showCustomExport = !showCustomExport"
        class="inline-flex items-center rounded-md border border-orange-300 bg-orange-50 px-3 py-1.5 text-xs font-medium text-orange-700 shadow-sm transition-colors hover:bg-orange-100 dark:border-orange-700 dark:bg-orange-900/30 dark:text-orange-300"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        Custom
    </button>

    <div
        x-cloak
        x-show="showCustomExport"
        x-on:click.away="showCustomExport = false"
        x-transition
        class="absolute right-0 top-full z-50 mt-1 w-64 rounded-lg border bg-card p-3 shadow-lg"
    >
        <div class="space-y-2">
            <label class="text-xs font-medium text-muted-foreground">From</label>
            <input type="date" x-model="dateFrom" class="flex h-8 w-full rounded-md border border-input bg-background px-2 py-1 text-xs" />

            <label class="text-xs font-medium text-muted-foreground">To</label>
            <input type="date" x-model="dateTo" x-bind:min="dateFrom" class="flex h-8 w-full rounded-md border border-input bg-background px-2 py-1 text-xs" />

            <a
                x-bind:href="dateFrom && dateTo && dateFrom <= dateTo
                    ? '{{ route($customRoute) }}?date_from=' + dateFrom + '&date_to=' + dateTo
                    : '#'"
                x-bind:class="dateFrom && dateTo && dateFrom <= dateTo
                    ? 'inline-flex w-full items-center justify-center rounded-md bg-primary px-3 py-1.5 text-xs font-medium text-primary-foreground transition-colors hover:bg-primary/90'
                    : 'inline-flex w-full cursor-not-allowed items-center justify-center rounded-md bg-muted px-3 py-1.5 text-xs font-medium text-muted-foreground'"
                x-bind:aria-disabled="!(dateFrom && dateTo && dateFrom <= dateTo)"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Export Excel
            </a>
        </div>
    </div>
</div>
