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

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

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

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script>
        // Push fresh positions to the Alpine map on every Livewire roundtrip.
        document.addEventListener('livewire:update', () => {
            const positions = @json($positions);
            window.dispatchEvent(new CustomEvent('positions-updated', { detail: positions }));
        });

        function fleetMap(initial) {
            return {
                map: null,
                markers: {},
                init() {
                    const center = initial.length
                        ? [initial[0].lat, initial[0].lng]
                        : [13.6218, 123.1948]; // Naga City fallback
                    this.map = L.map('fleet-map-canvas').setView(center, initial.length ? 11 : 9);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; OpenStreetMap'
                    }).addTo(this.map);
                    this.update(initial);
                },
                update(positions) {
                    if (!this.map) return;
                    const seen = new Set();
                    positions.forEach(p => {
                        seen.add(p.id);
                        const color = p.stale ? '#9ca3af' : '#16a34a';
                        const html = `<div style="background:${color};color:#fff;font-size:10px;font-weight:600;padding:2px 6px;border-radius:9999px;border:2px solid #fff;box-shadow:0 1px 2px rgba(0,0,0,.3);white-space:nowrap;">${p.bus_number || '#' + p.id}</div>`;
                        const icon = L.divIcon({ html, className: '', iconSize: null });
                        const popup = `
                            <div style="font-size:12px;line-height:1.4;">
                                <div style="font-weight:600;">${p.bus_number || ''} <span style="color:#666;font-weight:400;">${p.brand || ''}</span></div>
                                <div>Status: ${p.status || '—'}</div>
                                <div>KMR: ${p.kmr ?? '—'}</div>
                                <div>Updated: ${p.recorded_at ? new Date(p.recorded_at).toLocaleString() : '—'}</div>
                                ${p.stale ? '<div style="color:#b91c1c;font-weight:600;">STALE</div>' : ''}
                            </div>`;
                        if (this.markers[p.id]) {
                            this.markers[p.id].setLatLng([p.lat, p.lng]).setIcon(icon).bindPopup(popup);
                        } else {
                            this.markers[p.id] = L.marker([p.lat, p.lng], { icon }).addTo(this.map).bindPopup(popup);
                        }
                    });
                    Object.keys(this.markers).forEach(id => {
                        if (!seen.has(parseInt(id))) {
                            this.map.removeLayer(this.markers[id]);
                            delete this.markers[id];
                        }
                    });
                }
            };
        }
    </script>

    @if (empty($positions))
        <div class="rounded-lg border bg-card p-6 text-center text-sm text-muted-foreground">
            No GPS positions yet. Register each vehicle's <code class="rounded bg-muted px-1">gps_device_id</code> and POST to <code class="rounded bg-muted px-1">/api/v1/gps/ingest</code> with header <code class="rounded bg-muted px-1">X-Ingest-Token: $GPS_INGEST_TOKEN</code>.
        </div>
    @endif
</div>
