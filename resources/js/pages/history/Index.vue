<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { type BreadcrumbItem, type DispatchEntry, type PaginatedData } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Search } from 'lucide-vue-next';
import { ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'History', href: '/history' },
];

interface HistoryFilters {
    search?: string;
    date_from?: string;
    date_to?: string;
    direction?: string;
    status?: string;
}

const props = defineProps<{
    entries: PaginatedData<DispatchEntry>;
    filters: HistoryFilters;
}>();

const search = ref(props.filters.search || '');
const dateFrom = ref(props.filters.date_from || '');
const dateTo = ref(props.filters.date_to || '');
const direction = ref(props.filters.direction || '');
const status = ref(props.filters.status || '');

function applyFilters() {
    router.get('/history', {
        search: search.value || undefined,
        date_from: dateFrom.value || undefined,
        date_to: dateTo.value || undefined,
        direction: direction.value || undefined,
        status: status.value || undefined,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
}

function clearFilters() {
    search.value = '';
    dateFrom.value = '';
    dateTo.value = '';
    direction.value = '';
    status.value = '';
    router.get('/history', {}, { preserveState: true });
}

const statusClasses: Record<string, string> = {
    scheduled: 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
    departed: 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300',
    on_route: 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300',
    delayed: 'bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-300',
    cancelled: 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300',
    arrived: 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300',
};

function formatStatus(s: string): string {
    return s.replace(/_/g, ' ').replace(/\b\w/g, (c) => c.toUpperCase());
}

function formatTime(time: string | null): string {
    if (!time) return '--';
    return time.substring(0, 5);
}
</script>

<template>
    <Head title="History" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div>
                <h1 class="text-2xl font-bold">Dispatch History</h1>
                <p class="text-sm text-muted-foreground">Search and filter past dispatch entries</p>
            </div>

            <!-- Filters -->
            <Card>
                <CardContent class="pt-6">
                    <form @submit.prevent="applyFilters" class="space-y-4">
                        <div class="flex flex-wrap items-end gap-4">
                            <div class="flex-1 min-w-[200px] max-w-sm space-y-2">
                                <Label>Search</Label>
                                <div class="relative">
                                    <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                                    <Input v-model="search" placeholder="Bus number, route, trip code..." class="pl-9" />
                                </div>
                            </div>
                            <div class="space-y-2">
                                <Label>From Date</Label>
                                <Input type="date" v-model="dateFrom" class="w-40" />
                            </div>
                            <div class="space-y-2">
                                <Label>To Date</Label>
                                <Input type="date" v-model="dateTo" class="w-40" />
                            </div>
                            <div class="space-y-2">
                                <Label>Direction</Label>
                                <select v-model="direction" class="flex h-9 rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                                    <option value="">All</option>
                                    <option value="SB">SB</option>
                                    <option value="NB">NB</option>
                                </select>
                            </div>
                            <div class="space-y-2">
                                <Label>Status</Label>
                                <select v-model="status" class="flex h-9 rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                                    <option value="">All</option>
                                    <option value="scheduled">Scheduled</option>
                                    <option value="departed">Departed</option>
                                    <option value="on_route">On Route</option>
                                    <option value="delayed">Delayed</option>
                                    <option value="cancelled">Cancelled</option>
                                    <option value="arrived">Arrived</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <Button type="submit">
                                Apply Filters
                            </Button>
                            <Button type="button" variant="outline" @click="clearFilters">
                                Clear
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>

            <!-- Results Table -->
            <Card>
                <CardHeader>
                    <CardTitle class="text-base">
                        Results
                        <span class="ml-2 text-sm font-normal text-muted-foreground">({{ entries.total }} entries found)</span>
                    </CardTitle>
                </CardHeader>
                <CardContent class="p-0">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b bg-muted/50">
                                    <th class="px-4 py-3 text-left font-medium text-muted-foreground">Date</th>
                                    <th class="px-4 py-3 text-left font-medium text-muted-foreground">Brand</th>
                                    <th class="px-4 py-3 text-left font-medium text-muted-foreground">Bus No.</th>
                                    <th class="px-4 py-3 text-left font-medium text-muted-foreground">Trip Code</th>
                                    <th class="px-4 py-3 text-left font-medium text-muted-foreground">Route</th>
                                    <th class="px-4 py-3 text-left font-medium text-muted-foreground">Dir.</th>
                                    <th class="px-4 py-3 text-left font-medium text-muted-foreground">Sched.</th>
                                    <th class="px-4 py-3 text-left font-medium text-muted-foreground">Actual</th>
                                    <th class="px-4 py-3 text-left font-medium text-muted-foreground">Driver</th>
                                    <th class="px-4 py-3 text-left font-medium text-muted-foreground">Status</th>
                                    <th class="px-4 py-3 text-left font-medium text-muted-foreground">Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="entries.data.length === 0">
                                    <td colspan="11" class="px-4 py-8 text-center text-muted-foreground">
                                        No entries found. Try adjusting your filters.
                                    </td>
                                </tr>
                                <tr v-for="entry in entries.data" :key="entry.id" class="border-b last:border-0 hover:bg-muted/30 transition-colors">
                                    <td class="whitespace-nowrap px-4 py-2 text-muted-foreground">{{ entry.dispatch_day?.service_date || '--' }}</td>
                                    <td class="px-4 py-2">{{ entry.brand || '--' }}</td>
                                    <td class="px-4 py-2 font-semibold">{{ entry.bus_number || '--' }}</td>
                                    <td class="px-4 py-2">{{ entry.trip_code?.code || '--' }}</td>
                                    <td class="px-4 py-2">{{ entry.route || '--' }}</td>
                                    <td class="px-4 py-2">
                                        <span v-if="entry.direction" class="inline-flex items-center rounded px-1.5 py-0.5 text-xs font-medium"
                                            :class="entry.direction === 'SB' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300' : 'bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300'">
                                            {{ entry.direction }}
                                        </span>
                                        <span v-else>--</span>
                                    </td>
                                    <td class="px-4 py-2">{{ formatTime(entry.scheduled_departure) }}</td>
                                    <td class="px-4 py-2">{{ formatTime(entry.actual_departure) }}</td>
                                    <td class="px-4 py-2">{{ entry.driver?.name || '--' }}</td>
                                    <td class="px-4 py-2">
                                        <span class="inline-flex items-center rounded px-1.5 py-0.5 text-xs font-medium"
                                            :class="statusClasses[entry.status] || statusClasses.scheduled">
                                            {{ formatStatus(entry.status) }}
                                        </span>
                                    </td>
                                    <td class="max-w-[120px] truncate px-4 py-2" :title="entry.remarks || ''">{{ entry.remarks || '--' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </CardContent>
            </Card>

            <!-- Pagination -->
            <div v-if="entries.last_page > 1" class="flex items-center justify-between">
                <p class="text-sm text-muted-foreground">
                    Showing {{ (entries.current_page - 1) * entries.per_page + 1 }} to {{ Math.min(entries.current_page * entries.per_page, entries.total) }} of {{ entries.total }} results
                </p>
                <div class="flex items-center gap-1">
                    <template v-for="link in entries.links" :key="link.label">
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
