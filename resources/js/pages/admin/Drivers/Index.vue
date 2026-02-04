<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { type BreadcrumbItem, type Driver, type DriverStatus, type PaginatedData } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Plus, Pencil, Trash2, Search } from 'lucide-vue-next';
import { ref, watch } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Drivers', href: '/admin/drivers' },
];

const props = defineProps<{
    drivers: PaginatedData<Driver>;
    filters: { search?: string; status?: string };
}>();

const search = ref(props.filters.search || '');
const statusFilter = ref(props.filters.status || '');

let searchTimeout: ReturnType<typeof setTimeout>;

watch(search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(applyFilters, 300);
});

watch(statusFilter, applyFilters);

function applyFilters() {
    router.get('/admin/drivers', {
        search: search.value || undefined,
        status: statusFilter.value || undefined,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
}

function deleteDriver(driver: Driver) {
    if (confirm(`Are you sure you want to delete driver ${driver.name}?`)) {
        router.delete(`/admin/drivers/${driver.id}`);
    }
}

function setStatus(driver: Driver, status: DriverStatus) {
    router.patch(`/admin/drivers/${driver.id}/update-status`, {
        status,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
}

const statusConfig: Record<DriverStatus, { label: string; badge: string; btnActive: string }> = {
    available: {
        label: 'Available',
        badge: 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300',
        btnActive: 'bg-green-600 text-white hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-600',
    },
    dispatched: {
        label: 'Dispatched',
        badge: 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300',
        btnActive: 'bg-blue-600 text-white hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-600',
    },
    on_route: {
        label: 'On Route',
        badge: 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300',
        btnActive: 'bg-indigo-600 text-white hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-600',
    },
    on_leave: {
        label: 'On Leave',
        badge: 'bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-300',
        btnActive: 'bg-orange-600 text-white hover:bg-orange-700 dark:bg-orange-700 dark:hover:bg-orange-600',
    },
};

const allStatuses: DriverStatus[] = ['available', 'dispatched', 'on_route', 'on_leave'];
</script>

<template>
    <Head title="Drivers" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold">Drivers</h1>
                    <p class="text-sm text-muted-foreground">Manage bus drivers and their status</p>
                </div>
                <Button as-child>
                    <Link href="/admin/drivers/create">
                        <Plus class="mr-2 h-4 w-4" />
                        Add Driver
                    </Link>
                </Button>
            </div>

            <!-- Filters -->
            <div class="flex flex-wrap items-center gap-3">
                <div class="relative flex-1 min-w-[200px] max-w-sm">
                    <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                    <Input v-model="search" placeholder="Search drivers..." class="pl-9" />
                </div>
                <select
                    v-model="statusFilter"
                    class="flex h-9 rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                >
                    <option value="">All Statuses</option>
                    <option v-for="s in allStatuses" :key="s" :value="s">{{ statusConfig[s].label }}</option>
                </select>
            </div>

            <!-- Table -->
            <Card>
                <CardContent class="p-0">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b bg-muted/50">
                                    <th class="px-4 py-3 text-left font-medium text-muted-foreground">Name</th>
                                    <th class="px-4 py-3 text-left font-medium text-muted-foreground">Phone</th>
                                    <th class="px-4 py-3 text-left font-medium text-muted-foreground">License Number</th>
                                    <th class="px-4 py-3 text-left font-medium text-muted-foreground">Active</th>
                                    <th class="px-4 py-3 text-left font-medium text-muted-foreground">Status</th>
                                    <th class="px-4 py-3 text-left font-medium text-muted-foreground">Quick Status</th>
                                    <th class="px-4 py-3 text-left font-medium text-muted-foreground">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="drivers.data.length === 0">
                                    <td colspan="7" class="px-4 py-8 text-center text-muted-foreground">No drivers found.</td>
                                </tr>
                                <tr v-for="driver in drivers.data" :key="driver.id" class="border-b last:border-0 hover:bg-muted/30 transition-colors">
                                    <td class="px-4 py-3 font-medium">{{ driver.name }}</td>
                                    <td class="px-4 py-3">{{ driver.phone || '--' }}</td>
                                    <td class="px-4 py-3">{{ driver.license_number || '--' }}</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium"
                                            :class="driver.is_active ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300'">
                                            {{ driver.is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium"
                                            :class="statusConfig[driver.status]?.badge ?? statusConfig.available.badge">
                                            {{ statusConfig[driver.status]?.label ?? 'Available' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-1">
                                            <button
                                                v-for="s in allStatuses"
                                                :key="s"
                                                @click="setStatus(driver, s)"
                                                class="rounded px-2 py-1 text-xs font-medium transition-colors"
                                                :class="driver.status === s
                                                    ? statusConfig[s].btnActive
                                                    : 'bg-muted text-muted-foreground hover:bg-muted/80'"
                                                :title="`Set ${statusConfig[s].label}`"
                                            >
                                                {{ statusConfig[s].label }}
                                            </button>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <Button as-child variant="ghost" size="icon" class="h-8 w-8">
                                                <Link :href="`/admin/drivers/${driver.id}/edit`">
                                                    <Pencil class="h-4 w-4" />
                                                </Link>
                                            </Button>
                                            <Button variant="ghost" size="icon" class="h-8 w-8 text-red-500 hover:text-red-700" @click="deleteDriver(driver)">
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
            <div v-if="drivers.last_page > 1" class="flex items-center justify-between">
                <p class="text-sm text-muted-foreground">
                    Showing {{ (drivers.current_page - 1) * drivers.per_page + 1 }} to {{ Math.min(drivers.current_page * drivers.per_page, drivers.total) }} of {{ drivers.total }} results
                </p>
                <div class="flex items-center gap-1">
                    <template v-for="link in drivers.links" :key="link.label">
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
