<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { type BreadcrumbItem, type DailySummary, type DispatchEntry } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';

const props = defineProps<{
    date: string;
    summary: DailySummary | null;
    entries: DispatchEntry[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Reports', href: '/reports' },
    { title: `Daily - ${props.date}`, href: `/reports/daily?date=${props.date}` },
];

const statusClasses: Record<string, string> = {
    scheduled: 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
    departed: 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300',
    on_route: 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300',
    delayed: 'bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-300',
    cancelled: 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300',
    arrived: 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300',
};

function formatStatus(status: string): string {
    return status.replace(/_/g, ' ').replace(/\b\w/g, (c) => c.toUpperCase());
}

function formatTime(time: string | null): string {
    if (!time) return '--';
    return time.substring(0, 5);
}
</script>

<template>
    <Head :title="`Daily Report - ${date}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold">Daily Report</h1>
                    <p class="text-sm text-muted-foreground">Dispatch data for {{ date }}</p>
                </div>
                <Button as-child variant="outline">
                    <Link href="/reports">
                        <ArrowLeft class="mr-2 h-4 w-4" />
                        Back to Reports
                    </Link>
                </Button>
            </div>

            <!-- Summary -->
            <div v-if="summary" class="grid gap-4 sm:grid-cols-3 lg:grid-cols-6">
                <Card>
                    <CardContent class="pt-6 text-center">
                        <div class="text-2xl font-bold">{{ summary.total_trips }}</div>
                        <div class="text-xs text-muted-foreground">Total</div>
                    </CardContent>
                </Card>
                <Card>
                    <CardContent class="pt-6 text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ summary.sb_trips }}</div>
                        <div class="text-xs text-muted-foreground">Southbound</div>
                    </CardContent>
                </Card>
                <Card>
                    <CardContent class="pt-6 text-center">
                        <div class="text-2xl font-bold text-purple-600">{{ summary.nb_trips }}</div>
                        <div class="text-xs text-muted-foreground">Northbound</div>
                    </CardContent>
                </Card>
                <Card>
                    <CardContent class="pt-6 text-center">
                        <div class="text-2xl font-bold">{{ summary.naga_trips }}</div>
                        <div class="text-xs text-muted-foreground">Naga</div>
                    </CardContent>
                </Card>
                <Card>
                    <CardContent class="pt-6 text-center">
                        <div class="text-2xl font-bold">{{ summary.legazpi_trips }}</div>
                        <div class="text-xs text-muted-foreground">Legazpi</div>
                    </CardContent>
                </Card>
                <Card>
                    <CardContent class="pt-6 text-center">
                        <div class="text-2xl font-bold">{{ summary.sorsogon_trips }}</div>
                        <div class="text-xs text-muted-foreground">Sorsogon</div>
                    </CardContent>
                </Card>
            </div>

            <!-- Destination Breakdown -->
            <div v-if="summary" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
                <Card>
                    <CardContent class="pt-6 text-center">
                        <div class="text-lg font-bold">{{ summary.virac_trips }}</div>
                        <div class="text-xs text-muted-foreground">Virac</div>
                    </CardContent>
                </Card>
                <Card>
                    <CardContent class="pt-6 text-center">
                        <div class="text-lg font-bold">{{ summary.masbate_trips }}</div>
                        <div class="text-xs text-muted-foreground">Masbate</div>
                    </CardContent>
                </Card>
                <Card>
                    <CardContent class="pt-6 text-center">
                        <div class="text-lg font-bold">{{ summary.tabaco_trips }}</div>
                        <div class="text-xs text-muted-foreground">Tabaco</div>
                    </CardContent>
                </Card>
                <Card>
                    <CardContent class="pt-6 text-center">
                        <div class="text-lg font-bold">{{ summary.visayas_trips }}</div>
                        <div class="text-xs text-muted-foreground">Visayas</div>
                    </CardContent>
                </Card>
                <Card>
                    <CardContent class="pt-6 text-center">
                        <div class="text-lg font-bold">{{ summary.cargo_trips }}</div>
                        <div class="text-xs text-muted-foreground">Cargo</div>
                    </CardContent>
                </Card>
            </div>

            <div v-if="!summary" class="text-center py-8 text-muted-foreground">
                No summary data available for this date.
            </div>

            <!-- Entries Table -->
            <Card>
                <CardHeader>
                    <CardTitle>Dispatch Entries</CardTitle>
                    <CardDescription>All entries for {{ date }} ({{ entries.length }} total)</CardDescription>
                </CardHeader>
                <CardContent class="p-0">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b bg-muted/50">
                                    <th class="px-4 py-3 text-left font-medium text-muted-foreground">#</th>
                                    <th class="px-4 py-3 text-left font-medium text-muted-foreground">Brand</th>
                                    <th class="px-4 py-3 text-left font-medium text-muted-foreground">Bus No.</th>
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
                                <tr v-if="entries.length === 0">
                                    <td colspan="10" class="px-4 py-8 text-center text-muted-foreground">No entries for this date.</td>
                                </tr>
                                <tr v-for="(entry, index) in entries" :key="entry.id" class="border-b last:border-0 hover:bg-muted/30 transition-colors">
                                    <td class="px-4 py-2 text-muted-foreground">{{ index + 1 }}</td>
                                    <td class="px-4 py-2">{{ entry.brand || '--' }}</td>
                                    <td class="px-4 py-2 font-semibold">{{ entry.bus_number || '--' }}</td>
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
                                    <td class="max-w-[150px] truncate px-4 py-2" :title="entry.remarks || ''">{{ entry.remarks || '--' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
