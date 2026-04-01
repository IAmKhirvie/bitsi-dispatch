<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { type BreadcrumbItem, type DailySummary, type DispatchDay } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { BarChart3, Calendar, Download, FileSpreadsheet, FileText } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Reports', href: '/reports' },
];

interface SummaryRow extends DailySummary {
    dispatch_day?: DispatchDay;
}

const props = defineProps<{
    summaries: SummaryRow[];
    totals: {
        total_trips: number;
        sb_trips: number;
        nb_trips: number;
        naga_trips: number;
        legazpi_trips: number;
        sorsogon_trips: number;
        virac_trips: number;
        masbate_trips: number;
        tabaco_trips: number;
        visayas_trips: number;
        cargo_trips: number;
    };
    filters: { date_from: string; date_to: string };
}>();

const dateFrom = ref(props.filters.date_from || '');
const dateTo = ref(props.filters.date_to || '');

const daysCount = computed(() => props.summaries.length);
const dailyAverage = computed(() => {
    if (daysCount.value === 0) return 0;
    return Math.round(props.totals.total_trips / daysCount.value * 10) / 10;
});

function applyFilters() {
    router.get('/reports', {
        date_from: dateFrom.value || undefined,
        date_to: dateTo.value || undefined,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
}

function getServiceDate(row: SummaryRow): string {
    return row.dispatch_day?.service_date ?? '';
}
</script>

<template>
    <Head title="Reports" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold">Reports</h1>
                    <p class="text-sm text-muted-foreground">Dispatch reports and trip analytics</p>
                </div>
            </div>

            <!-- Date Range Filter -->
            <Card>
                <CardContent class="pt-6">
                    <form @submit.prevent="applyFilters" class="flex flex-wrap items-end gap-4">
                        <div class="space-y-2">
                            <Label>From Date</Label>
                            <Input type="date" v-model="dateFrom" class="w-44" />
                        </div>
                        <div class="space-y-2">
                            <Label>To Date</Label>
                            <Input type="date" v-model="dateTo" class="w-44" />
                        </div>
                        <Button type="submit">
                            <Calendar class="mr-2 h-4 w-4" />
                            Apply
                        </Button>
                    </form>
                </CardContent>
            </Card>

            <!-- Summary Cards -->
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Trips</CardTitle>
                        <BarChart3 class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ totals.total_trips }}</div>
                        <p class="text-xs text-muted-foreground">Across {{ daysCount }} day{{ daysCount !== 1 ? 's' : '' }}</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">SB / NB Split</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ totals.sb_trips }} / {{ totals.nb_trips }}</div>
                        <p class="text-xs text-muted-foreground">Southbound / Northbound</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Daily Average</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ dailyAverage }}</div>
                        <p class="text-xs text-muted-foreground">Trips per day</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Top Destinations</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ totals.naga_trips }}</div>
                        <p class="text-xs text-muted-foreground">Naga trips (most served)</p>
                    </CardContent>
                </Card>
            </div>

            <!-- Destination Breakdown -->
            <div class="grid gap-4 sm:grid-cols-3 lg:grid-cols-6">
                <Card v-for="dest in [
                    { name: 'Naga', count: totals.naga_trips },
                    { name: 'Legazpi', count: totals.legazpi_trips },
                    { name: 'Sorsogon', count: totals.sorsogon_trips },
                    { name: 'Virac', count: totals.virac_trips },
                    { name: 'Tabaco', count: totals.tabaco_trips },
                    { name: 'Visayas', count: totals.visayas_trips },
                ]" :key="dest.name">
                    <CardContent class="pt-4 pb-3 text-center">
                        <div class="text-xl font-bold">{{ dest.count }}</div>
                        <div class="text-xs text-muted-foreground">{{ dest.name }}</div>
                    </CardContent>
                </Card>
            </div>

            <!-- Daily Breakdown Table -->
            <Card>
                <CardHeader>
                    <CardTitle>Daily Breakdown</CardTitle>
                    <CardDescription>Trip counts per day within the selected range</CardDescription>
                </CardHeader>
                <CardContent class="p-0">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b bg-muted/50">
                                    <th class="px-4 py-3 text-left font-medium text-muted-foreground">Date</th>
                                    <th class="px-4 py-3 text-right font-medium text-muted-foreground">Total</th>
                                    <th class="px-4 py-3 text-right font-medium text-muted-foreground">SB</th>
                                    <th class="px-4 py-3 text-right font-medium text-muted-foreground">NB</th>
                                    <th class="px-4 py-3 text-right font-medium text-muted-foreground">Naga</th>
                                    <th class="px-4 py-3 text-right font-medium text-muted-foreground">Legazpi</th>
                                    <th class="px-4 py-3 text-right font-medium text-muted-foreground">Sorsogon</th>
                                    <th class="px-4 py-3 text-right font-medium text-muted-foreground">Virac</th>
                                    <th class="px-4 py-3 text-right font-medium text-muted-foreground">Visayas</th>
                                    <th class="px-4 py-3 text-left font-medium text-muted-foreground">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="summaries.length === 0">
                                    <td colspan="10" class="px-4 py-8 text-center text-muted-foreground">
                                        No data available for the selected date range.
                                    </td>
                                </tr>
                                <tr v-for="row in summaries" :key="row.id" class="border-b last:border-0 hover:bg-muted/30 transition-colors">
                                    <td class="px-4 py-3 font-medium">{{ getServiceDate(row) }}</td>
                                    <td class="px-4 py-3 text-right font-semibold">{{ row.total_trips }}</td>
                                    <td class="px-4 py-3 text-right">{{ row.sb_trips }}</td>
                                    <td class="px-4 py-3 text-right">{{ row.nb_trips }}</td>
                                    <td class="px-4 py-3 text-right">{{ row.naga_trips }}</td>
                                    <td class="px-4 py-3 text-right">{{ row.legazpi_trips }}</td>
                                    <td class="px-4 py-3 text-right">{{ row.sorsogon_trips }}</td>
                                    <td class="px-4 py-3 text-right">{{ row.virac_trips }}</td>
                                    <td class="px-4 py-3 text-right">{{ row.visayas_trips }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-1">
                                            <Button as-child variant="ghost" size="sm">
                                                <Link :href="`/reports/daily/${getServiceDate(row)}`">
                                                    View
                                                </Link>
                                            </Button>
                                            <Button as-child variant="ghost" size="icon" class="h-8 w-8" title="Export Excel">
                                                <a :href="`/reports/export/excel/${getServiceDate(row)}`">
                                                    <FileSpreadsheet class="h-4 w-4 text-green-600" />
                                                </a>
                                            </Button>
                                            <Button as-child variant="ghost" size="icon" class="h-8 w-8" title="Export PDF">
                                                <a :href="`/reports/export/pdf/${getServiceDate(row)}`">
                                                    <FileText class="h-4 w-4 text-red-600" />
                                                </a>
                                            </Button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot v-if="summaries.length > 0">
                                <tr class="border-t-2 bg-muted/30 font-semibold">
                                    <td class="px-4 py-3">Totals</td>
                                    <td class="px-4 py-3 text-right">{{ totals.total_trips }}</td>
                                    <td class="px-4 py-3 text-right">{{ totals.sb_trips }}</td>
                                    <td class="px-4 py-3 text-right">{{ totals.nb_trips }}</td>
                                    <td class="px-4 py-3 text-right">{{ totals.naga_trips }}</td>
                                    <td class="px-4 py-3 text-right">{{ totals.legazpi_trips }}</td>
                                    <td class="px-4 py-3 text-right">{{ totals.sorsogon_trips }}</td>
                                    <td class="px-4 py-3 text-right">{{ totals.virac_trips }}</td>
                                    <td class="px-4 py-3 text-right">{{ totals.visayas_trips }}</td>
                                    <td class="px-4 py-3"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
