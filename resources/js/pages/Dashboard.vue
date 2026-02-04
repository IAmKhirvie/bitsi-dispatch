<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { type BreadcrumbItem, type DailySummary, type DispatchEntry } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Bus, CheckCircle2, AlertTriangle, XCircle, Truck, Wrench, UserCheck, ArrowRight } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
];

interface DashboardStats {
    today_trips: number;
    departed: number;
    on_route: number;
    cancelled: number;
    active_vehicles: number;
    under_repair: number;
    pms_warning: number;
    active_drivers: number;
}

const props = defineProps<{
    stats: DashboardStats;
    todaySummary: DailySummary | null;
    recentEntries: DispatchEntry[];
}>();

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
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4">
            <!-- Stat Cards -->
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Today's Trips</CardTitle>
                        <Bus class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.today_trips }}</div>
                        <p class="text-xs text-muted-foreground">Total dispatched trips today</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Departed</CardTitle>
                        <CheckCircle2 class="h-4 w-4 text-blue-500" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.departed }}</div>
                        <p class="text-xs text-muted-foreground">Buses that have departed</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">On Route</CardTitle>
                        <ArrowRight class="h-4 w-4 text-indigo-500" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.on_route }}</div>
                        <p class="text-xs text-muted-foreground">Currently in transit</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Cancelled</CardTitle>
                        <XCircle class="h-4 w-4 text-red-500" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.cancelled }}</div>
                        <p class="text-xs text-muted-foreground">Cancelled trips today</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Active Vehicles</CardTitle>
                        <Truck class="h-4 w-4 text-green-500" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.active_vehicles }}</div>
                        <p class="text-xs text-muted-foreground">Vehicles in service</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Under Repair</CardTitle>
                        <Wrench class="h-4 w-4 text-red-500" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.under_repair }}</div>
                        <p class="text-xs text-muted-foreground">Vehicles under repair</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">PMS Warning</CardTitle>
                        <AlertTriangle class="h-4 w-4 text-orange-500" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.pms_warning }}</div>
                        <p class="text-xs text-muted-foreground">Vehicles needing maintenance</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Active Drivers</CardTitle>
                        <UserCheck class="h-4 w-4 text-green-500" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.active_drivers }}</div>
                        <p class="text-xs text-muted-foreground">Drivers on roster</p>
                    </CardContent>
                </Card>
            </div>

            <!-- Today's Summary -->
            <div v-if="todaySummary" class="grid gap-4 lg:grid-cols-2">
                <Card>
                    <CardHeader>
                        <CardTitle>Today's Summary</CardTitle>
                        <CardDescription>Trip breakdown by direction and destination</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-muted-foreground">SB Trips</span>
                                    <span class="font-semibold">{{ todaySummary.sb_trips }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-muted-foreground">NB Trips</span>
                                    <span class="font-semibold">{{ todaySummary.nb_trips }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-muted-foreground">Total Trips</span>
                                    <span class="font-bold text-lg">{{ todaySummary.total_trips }}</span>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-muted-foreground">Naga</span>
                                    <span class="font-semibold">{{ todaySummary.naga_trips }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-muted-foreground">Legazpi</span>
                                    <span class="font-semibold">{{ todaySummary.legazpi_trips }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-muted-foreground">Sorsogon</span>
                                    <span class="font-semibold">{{ todaySummary.sorsogon_trips }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-muted-foreground">Virac</span>
                                    <span class="font-semibold">{{ todaySummary.virac_trips }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-muted-foreground">Masbate</span>
                                    <span class="font-semibold">{{ todaySummary.masbate_trips }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-muted-foreground">Tabaco</span>
                                    <span class="font-semibold">{{ todaySummary.tabaco_trips }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-muted-foreground">Visayas</span>
                                    <span class="font-semibold">{{ todaySummary.visayas_trips }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-muted-foreground">Cargo</span>
                                    <span class="font-semibold">{{ todaySummary.cargo_trips }}</span>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Quick Actions -->
                <Card>
                    <CardHeader>
                        <CardTitle>Quick Actions</CardTitle>
                        <CardDescription>Common dispatch operations</CardDescription>
                    </CardHeader>
                    <CardContent class="flex flex-col gap-3">
                        <Button as-child class="w-full justify-start">
                            <Link href="/dispatch">
                                <Bus class="mr-2 h-4 w-4" />
                                Go to Dispatch Board
                            </Link>
                        </Button>
                        <Button as-child variant="outline" class="w-full justify-start">
                            <Link href="/reports">
                                <ArrowRight class="mr-2 h-4 w-4" />
                                View Reports
                            </Link>
                        </Button>
                        <Button as-child variant="outline" class="w-full justify-start">
                            <Link href="/history">
                                <ArrowRight class="mr-2 h-4 w-4" />
                                View History
                            </Link>
                        </Button>
                    </CardContent>
                </Card>
            </div>

            <!-- Quick Actions when no summary -->
            <div v-else>
                <Card>
                    <CardHeader>
                        <CardTitle>Quick Actions</CardTitle>
                        <CardDescription>No dispatch day created for today yet</CardDescription>
                    </CardHeader>
                    <CardContent class="flex gap-3">
                        <Button as-child>
                            <Link href="/dispatch">
                                <Bus class="mr-2 h-4 w-4" />
                                Go to Dispatch Board
                            </Link>
                        </Button>
                        <Button as-child variant="outline">
                            <Link href="/reports">
                                View Reports
                            </Link>
                        </Button>
                    </CardContent>
                </Card>
            </div>

            <!-- Recent Dispatch Entries -->
            <Card>
                <CardHeader>
                    <CardTitle>Recent Dispatch Entries</CardTitle>
                    <CardDescription>Last 10 entries from today's dispatch</CardDescription>
                </CardHeader>
                <CardContent>
                    <div v-if="recentEntries.length === 0" class="py-8 text-center text-muted-foreground">
                        No dispatch entries for today yet.
                    </div>
                    <div v-else class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b text-left">
                                    <th class="pb-2 pr-4 font-medium text-muted-foreground">Bus No.</th>
                                    <th class="pb-2 pr-4 font-medium text-muted-foreground">Route</th>
                                    <th class="pb-2 pr-4 font-medium text-muted-foreground">Direction</th>
                                    <th class="pb-2 pr-4 font-medium text-muted-foreground">Sched. Dep.</th>
                                    <th class="pb-2 pr-4 font-medium text-muted-foreground">Actual Dep.</th>
                                    <th class="pb-2 pr-4 font-medium text-muted-foreground">Driver</th>
                                    <th class="pb-2 font-medium text-muted-foreground">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="entry in recentEntries" :key="entry.id" class="border-b last:border-0">
                                    <td class="py-2 pr-4 font-medium">{{ entry.bus_number || '--' }}</td>
                                    <td class="py-2 pr-4">{{ entry.route || '--' }}</td>
                                    <td class="py-2 pr-4">
                                        <span v-if="entry.direction" class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium"
                                            :class="entry.direction === 'SB' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300' : 'bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300'">
                                            {{ entry.direction }}
                                        </span>
                                        <span v-else>--</span>
                                    </td>
                                    <td class="py-2 pr-4">{{ formatTime(entry.scheduled_departure) }}</td>
                                    <td class="py-2 pr-4">{{ formatTime(entry.actual_departure) }}</td>
                                    <td class="py-2 pr-4">{{ entry.driver?.name || '--' }}</td>
                                    <td class="py-2">
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium"
                                            :class="statusClasses[entry.status] || statusClasses.scheduled">
                                            {{ formatStatus(entry.status) }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
