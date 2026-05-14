<div class="flex h-full flex-1 flex-col gap-4 p-4" wire:poll.15s>
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold">Fleet Map</h1>
            <p class="text-sm text-muted-foreground">
                Live GPS positions ({{ count($positions) }} vehicle{{ count($positions) === 1 ? '' : 's' }} reporting · stale > {{ $staleMinutes }} min)
            </p>
        </div>
        <div class="flex items-center gap-2 text-xs">
            <span class="inline-flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-green-500"></span> Live</span>
            <span class="inline-flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-gray-400"></span> Stale</span>
        </div>
    </div>

    <div
        wire:ignore
        x-data="fleetMap(@js($positions))"
        x-init="init()"
        @positions-updated.window="update($event.detail)"
        class="relative flex-1 overflow-hidden rounded-lg border bg-card shadow-sm"
        style="min-height: 600px;"
    >
        <div id="fleet-map-canvas" class="absolute inset-0"></div>
    </div>

    @if (empty($positions))
        <div class="rounded-lg border bg-card p-6 text-center text-sm text-muted-foreground">
            No GPS positions yet. Register each vehicle's <code class="rounded bg-muted px-1">gps_device_id</code> and POST to <code class="rounded bg-muted px-1">/api/v1/gps/ingest</code> with header <code class="rounded bg-muted px-1">X-Ingest-Token: $GPS_INGEST_TOKEN</code>.
        </div>
    @endif
</div>
