<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { MapPin, RefreshCw, Satellite } from 'lucide-vue-next';
import { onMounted, onUnmounted, ref, shallowRef } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Tracking Map', href: '/tracking' },
];

interface VehiclePosition {
    id: number;
    bus_number: string;
    brand: string;
    bus_type: string;
    plate_number: string;
    status: string;
    position: {
        latitude: number;
        longitude: number;
        speed: number | null;
        heading: number | null;
        recorded_at: string;
    };
}

const vehicles = ref<VehiclePosition[]>([]);
const loading = ref(true);
const lastRefresh = ref<string>('');
const selectedVehicle = ref<VehiclePosition | null>(null);
const mapContainer = ref<HTMLDivElement>();
const leafletMap = shallowRef<any>(null);
const markers = shallowRef<any[]>([]);
let refreshInterval: ReturnType<typeof setInterval>;
let L: any;

async function fetchPositions() {
    try {
        const response = await fetch('/api/positions/latest');
        if (response.ok) {
            vehicles.value = await response.json();
            lastRefresh.value = new Date().toLocaleTimeString();
            updateMarkers();
        }
    } catch (e) {
        console.error('Failed to fetch positions:', e);
    } finally {
        loading.value = false;
    }
}

function updateMarkers() {
    if (!leafletMap.value || !L) return;

    // Remove old markers
    markers.value.forEach(m => m.remove());
    const newMarkers: any[] = [];

    vehicles.value.forEach(v => {
        const busIcon = L.divIcon({
            html: `<div class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-600 text-white text-xs font-bold border-2 border-white shadow-lg">${v.bus_number.slice(-2)}</div>`,
            className: '',
            iconSize: [32, 32],
            iconAnchor: [16, 16],
        });

        const marker = L.marker([v.position.latitude, v.position.longitude], { icon: busIcon })
            .addTo(leafletMap.value)
            .bindPopup(`
                <div class="text-sm">
                    <div class="font-bold">${v.brand} ${v.bus_number}</div>
                    <div>Type: ${v.bus_type}</div>
                    <div>Plate: ${v.plate_number}</div>
                    <div>Status: ${v.status}</div>
                    <div>Speed: ${v.position.speed != null ? v.position.speed + ' km/h' : 'N/A'}</div>
                    <div class="text-xs text-gray-500 mt-1">Updated: ${new Date(v.position.recorded_at).toLocaleString()}</div>
                </div>
            `);

        marker.on('click', () => {
            selectedVehicle.value = v;
        });

        newMarkers.push(marker);
    });

    markers.value = newMarkers;
}

function centerOnVehicle(v: VehiclePosition) {
    selectedVehicle.value = v;
    if (leafletMap.value) {
        leafletMap.value.setView([v.position.latitude, v.position.longitude], 14);
        const marker = markers.value.find((m, i) => vehicles.value[i]?.id === v.id);
        if (marker) marker.openPopup();
    }
}

async function initMap() {
    L = await import('leaflet');
    await import('leaflet/dist/leaflet.css');

    if (!mapContainer.value) return;

    leafletMap.value = L.map(mapContainer.value).setView([13.4, 123.4], 8);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://openstreetmap.org">OpenStreetMap</a> contributors',
        maxZoom: 18,
    }).addTo(leafletMap.value);
}

onMounted(async () => {
    await initMap();
    await fetchPositions();
    refreshInterval = setInterval(fetchPositions, 30000);
});

onUnmounted(() => {
    clearInterval(refreshInterval);
    if (leafletMap.value) {
        leafletMap.value.remove();
    }
});

function formatTime(dateStr: string): string {
    return new Date(dateStr).toLocaleTimeString();
}
</script>

<template>
    <Head title="Tracking Map" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold">Tracking Map</h1>
                    <p class="text-sm text-muted-foreground">
                        Real-time GPS tracking of fleet vehicles
                        <span v-if="lastRefresh" class="ml-2">(Last refresh: {{ lastRefresh }})</span>
                    </p>
                </div>
                <Button variant="outline" @click="fetchPositions" :disabled="loading">
                    <RefreshCw class="mr-2 h-4 w-4" :class="{ 'animate-spin': loading }" />
                    Refresh
                </Button>
            </div>

            <div class="flex flex-1 gap-4 min-h-[500px]">
                <!-- Sidebar -->
                <Card class="w-72 shrink-0 overflow-hidden">
                    <CardHeader class="py-3">
                        <CardTitle class="flex items-center gap-2 text-sm">
                            <Satellite class="h-4 w-4" />
                            Tracked Vehicles ({{ vehicles.length }})
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="max-h-[calc(100vh-300px)] overflow-y-auto p-0">
                        <div v-if="vehicles.length === 0" class="px-4 py-8 text-center text-sm text-muted-foreground">
                            No vehicles with GPS data yet.
                        </div>
                        <button
                            v-for="v in vehicles"
                            :key="v.id"
                            @click="centerOnVehicle(v)"
                            class="flex w-full items-start gap-3 border-b px-4 py-3 text-left transition-colors hover:bg-muted/50"
                            :class="{ 'bg-muted': selectedVehicle?.id === v.id }"
                        >
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-600 text-xs font-bold text-white">
                                {{ v.bus_number.slice(-2) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="font-medium text-sm">{{ v.brand }} {{ v.bus_number }}</div>
                                <div class="text-xs text-muted-foreground">{{ v.bus_type }} - {{ v.plate_number }}</div>
                                <div class="mt-0.5 text-xs text-muted-foreground">
                                    {{ v.position.speed != null ? v.position.speed + ' km/h' : 'Speed N/A' }}
                                    <span class="ml-1">{{ formatTime(v.position.recorded_at) }}</span>
                                </div>
                            </div>
                        </button>
                    </CardContent>
                </Card>

                <!-- Map -->
                <div class="relative flex-1 overflow-hidden rounded-xl border">
                    <div ref="mapContainer" class="h-full w-full min-h-[500px]"></div>

                    <!-- Empty state overlay -->
                    <div v-if="!loading && vehicles.length === 0" class="absolute inset-0 flex items-center justify-center bg-background/80 backdrop-blur-sm">
                        <div class="text-center">
                            <MapPin class="mx-auto mb-4 h-12 w-12 text-muted-foreground" />
                            <h3 class="mb-1 text-lg font-semibold">No GPS Data Available</h3>
                            <p class="max-w-sm text-sm text-muted-foreground">
                                No vehicles have reported GPS positions yet.
                                Positions will appear automatically as GPS devices report data.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
