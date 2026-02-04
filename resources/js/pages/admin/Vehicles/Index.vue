<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { type BreadcrumbItem, type PaginatedData, type Vehicle } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Plus, Pencil, Trash2, Search, AlertTriangle } from 'lucide-vue-next';
import { ref, watch } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Vehicles', href: '/admin/vehicles' },
];

const props = defineProps<{
    vehicles: PaginatedData<Vehicle>;
    filters: { search?: string; status?: string };
}>();

const search = ref(props.filters.search || '');
const statusFilter = ref(props.filters.status || '');

let searchTimeout: ReturnType<typeof setTimeout>;

watch([search, statusFilter], () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get('/admin/vehicles', {
            search: search.value || undefined,
            status: statusFilter.value || undefined,
        }, {
            preserveState: true,
            preserveScroll: true,
        });
    }, 300);
});

function deleteVehicle(vehicle: Vehicle) {
    if (confirm(`Are you sure you want to delete vehicle ${vehicle.bus_number}?`)) {
        router.delete(`/admin/vehicles/${vehicle.id}`);
    }
}

const vehicleStatusClasses: Record<string, string> = {
    OK: 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300',
    UR: 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300',
    PMS: 'bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-300',
    'In Transit': 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300',
    Lutaw: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300',
};
</script>

<template>
    <Head title="Vehicles" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold">Vehicles</h1>
                    <p class="text-sm text-muted-foreground">Manage fleet vehicles and maintenance status</p>
                </div>
                <Button as-child>
                    <Link href="/admin/vehicles/create">
                        <Plus class="mr-2 h-4 w-4" />
                        Add Vehicle
                    </Link>
                </Button>
            </div>

            <!-- Filters -->
            <div class="flex flex-wrap items-center gap-3">
                <div class="relative flex-1 min-w-[200px] max-w-sm">
                    <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                    <Input v-model="search" placeholder="Search vehicles..." class="pl-9" />
                </div>
                <select v-model="statusFilter" class="flex h-9 rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                    <option value="">All Statuses</option>
                    <option value="OK">OK</option>
                    <option value="UR">Under Repair (UR)</option>
                    <option value="PMS">PMS</option>
                    <option value="In Transit">In Transit</option>
                    <option value="Lutaw">Lutaw</option>
                </select>
            </div>

            <!-- Table -->
            <Card>
                <CardContent class="p-0">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b bg-muted/50">
                                    <th class="px-4 py-3 text-left font-medium text-muted-foreground">Bus No.</th>
                                    <th class="px-4 py-3 text-left font-medium text-muted-foreground">Brand</th>
                                    <th class="px-4 py-3 text-left font-medium text-muted-foreground">Type</th>
                                    <th class="px-4 py-3 text-left font-medium text-muted-foreground">Plate No.</th>
                                    <th class="px-4 py-3 text-left font-medium text-muted-foreground">Status</th>
                                    <th class="px-4 py-3 text-left font-medium text-muted-foreground">PMS</th>
                                    <th class="px-4 py-3 text-left font-medium text-muted-foreground">Idle Days</th>
                                    <th class="px-4 py-3 text-left font-medium text-muted-foreground">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="vehicles.data.length === 0">
                                    <td colspan="8" class="px-4 py-8 text-center text-muted-foreground">No vehicles found.</td>
                                </tr>
                                <tr v-for="vehicle in vehicles.data" :key="vehicle.id" class="border-b last:border-0 hover:bg-muted/30 transition-colors">
                                    <td class="px-4 py-3 font-semibold">{{ vehicle.bus_number }}</td>
                                    <td class="px-4 py-3">{{ vehicle.brand }}</td>
                                    <td class="px-4 py-3">{{ vehicle.bus_type }}</td>
                                    <td class="px-4 py-3">{{ vehicle.plate_number }}</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium"
                                            :class="vehicleStatusClasses[vehicle.status] || 'bg-gray-100 text-gray-700'">
                                            {{ vehicle.status }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <div class="h-2 w-16 rounded-full bg-gray-200 dark:bg-gray-700">
                                                <div class="h-2 rounded-full transition-all"
                                                    :class="(vehicle.pms_percentage || 0) >= 80 ? 'bg-red-500' : (vehicle.pms_percentage || 0) >= 60 ? 'bg-orange-500' : 'bg-green-500'"
                                                    :style="{ width: `${Math.min(vehicle.pms_percentage || 0, 100)}%` }">
                                                </div>
                                            </div>
                                            <span class="text-xs text-muted-foreground">{{ vehicle.current_pms_value }}/{{ vehicle.pms_threshold }}</span>
                                            <AlertTriangle v-if="vehicle.is_pms_warning" class="h-4 w-4 text-orange-500" />
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span :class="vehicle.idle_days > 7 ? 'text-red-500 font-medium' : ''">
                                            {{ vehicle.idle_days }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <Button as-child variant="ghost" size="icon" class="h-8 w-8">
                                                <Link :href="`/admin/vehicles/${vehicle.id}/edit`">
                                                    <Pencil class="h-4 w-4" />
                                                </Link>
                                            </Button>
                                            <Button variant="ghost" size="icon" class="h-8 w-8 text-red-500 hover:text-red-700" @click="deleteVehicle(vehicle)">
                                                <Trash2 class="h-4 w-4" />
                                            </Button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </CardContent>
            </Card>

            <!-- Pagination -->
            <div v-if="vehicles.last_page > 1" class="flex items-center justify-between">
                <p class="text-sm text-muted-foreground">
                    Showing {{ (vehicles.current_page - 1) * vehicles.per_page + 1 }} to {{ Math.min(vehicles.current_page * vehicles.per_page, vehicles.total) }} of {{ vehicles.total }} results
                </p>
                <div class="flex items-center gap-1">
                    <template v-for="link in vehicles.links" :key="link.label">
                        <Button
                            v-if="link.url"
                            as-child
                            variant="outline"
                            size="sm"
                            :class="{ 'bg-primary text-primary-foreground': link.active }"
                        >
                            <Link :href="link.url" v-html="link.label" preserve-scroll />
                        </Button>
                        <Button
                            v-else
                            variant="outline"
                            size="sm"
                            disabled
                            v-html="link.label"
                        />
                    </template>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
