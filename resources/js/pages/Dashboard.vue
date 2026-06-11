<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import StatusBadge from '@/components/StatusBadge.vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from '@/components/ui/collapsible';
import { type BreadcrumbItem, type DailySummary, type DispatchEntry } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import {
    Bus, CheckCircle2, AlertTriangle, XCircle, Truck, Wrench, UserCheck, ArrowRight, ChevronDown,
} from 'lucide-vue-next';
import { ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Dashboard', href: '/dashboard' }];

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

defineProps<{
    stats: DashboardStats;
    todaySummary: DailySummary | null;
    recentEntries: DispatchEntry[];
}>();

const breakdownOpen = ref(false);

function formatTime(time: string | null): string {
    if (!time) return '--';
    return time.substring(0, 5);
}

const directionStyle = (dir: string) =>
    dir === 'SB'
        ? { backgroundColor: 'hsl(214 95% 93%)', color: 'hsl(217 76% 38%)' }
        : { backgroundColor: 'hsl(280 65% 92%)', color: 'hsl(280 60% 38%)' };
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4">
            <!-- Hero KPI + compact secondary strip -->
            <div class="grid gap-4 lg:grid-cols-3">
                <!-- Hero -->
                <Card class="lg:col-span-2 bg-gradient-to-br from-primary/5 to-primary/10">
                    <CardContent class="flex items-center justify-between gap-6 p-6">
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Today's Trips</p>
                            <p class="mt-1 text-5xl font-bold tracking-tight">{{ stats.today_trips }}</p>
                            <p class="mt-2 text-sm text-muted-foreground">
                                {{ stats.departed }} departed · {{ stats.on_route }} on route · {{ stats.cancelled }} cancelled
                            </p>
                        </div>
                        <div class="flex h-20 w-20 items-center justify-center rounded-2xl bg-primary/10">
                            <Bus class="h-10 w-10 text-primary" />
                        </div>
                    </CardContent>
                </Card>

                <!-- Fleet & crew at-a-glance -->
                <Card>
                    <CardHeader class="pb-2"><CardTitle class="text-sm font-medium">Fleet & crew</CardTitle></CardHeader>
                    <CardContent class="space-y-2.5">
                        <div class="flex items-center justify-between text-sm">
                            <span class="flex items-center gap-2 text-muted-foreground">
                                <Truck class="h-4 w-4" /> Active vehicles
                            </span>
                            <span class="font-semibold">{{ stats.active_vehicles }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="flex items-center gap-2 text-muted-foreground">
                                <UserCheck class="h-4 w-4" /> Active drivers
                            </span>
                            <span class="font-semibold">{{ stats.active_drivers }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="flex items-center gap-2 text-muted-foreground">
                                <Wrench class="h-4 w-4" /> Under repair
                            </span>
                            <span class="font-semibold">{{ stats.under_repair }}</span>
                        </div>
                        <div
                            class="flex items-center justify-between rounded-md px-2 py-1.5 text-sm"
                            :style="stats.pms_warning > 0
                                ? { backgroundColor: 'hsl(33 100% 92%)', color: 'hsl(20 78% 35%)' }
                                : {}"
                        >
                            <span class="flex items-center gap-2">
                                <AlertTriangle class="h-4 w-4" /> PMS warnings
                            </span>
                            <span class="font-semibold">{{ stats.pms_warning }}</span>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Status mini-strip -->
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                <div class="rounded-lg border bg-card px-3 py-2">
                    <p class="text-xs text-muted-foreground">Departed</p>
                    <p class="mt-0.5 text-xl font-semibold">{{ stats.departed }}</p>
                </div>
                <div class="rounded-lg border bg-card px-3 py-2">
                    <p class="text-xs text-muted-foreground">On route</p>
                    <p class="mt-0.5 text-xl font-semibold">{{ stats.on_route }}</p>
                </div>
                <div class="rounded-lg border bg-card px-3 py-2">
                    <p class="text-xs text-muted-foreground">Cancelled</p>
                    <p class="mt-0.5 text-xl font-semibold">{{ stats.cancelled }}</p>
                </div>
                <div class="rounded-lg border bg-card px-3 py-2">
                    <p class="text-xs text-muted-foreground">Arrived</p>
                    <p class="mt-0.5 text-xl font-semibold">{{ Math.max(0, stats.today_trips - stats.departed - stats.on_route - stats.cancelled) }}</p>
                </div>
            </div>

            <!-- Today's Summary + Quick Actions -->
            <div class="grid gap-4 lg:grid-cols-3">
                <Card v-if="todaySummary" class="lg:col-span-2">
                    <Collapsible v-model:open="breakdownOpen">
                        <CardHeader>
                            <div class="flex items-center justify-between">
                                <div>
                                    <CardTitle>Today's Summary</CardTitle>
                                    <CardDescription>Trip breakdown by direction and destination</CardDescription>
                                </div>
                                <CollapsibleTrigger as-child>
                                    <Button variant="ghost" size="sm">
                                        <ChevronDown class="h-4 w-4 transition-transform" :class="{ 'rotate-180': breakdownOpen }" />
                                        <span class="ml-1">{{ breakdownOpen ? 'Hide' : 'See breakdown' }}</span>
                                    </Button>
                                </CollapsibleTrigger>
                            </div>
                        </CardHeader>

                        <CardContent>
                            <!-- Always-visible top row -->
                            <div class="grid grid-cols-3 gap-4">
                                <div class="rounded-md border p-3">
                                    <p class="text-xs text-muted-foreground">SB Trips</p>
                                    <p class="text-2xl font-bold">{{ todaySummary.sb_trips }}</p>
                                </div>
                                <div class="rounded-md border p-3">
                                    <p class="text-xs text-muted-foreground">NB Trips</p>
                                    <p class="text-2xl font-bold">{{ todaySummary.nb_trips }}</p>
                                </div>
                                <div class="rounded-md border p-3">
                                    <p class="text-xs text-muted-foreground">Total</p>
                                    <p class="text-2xl font-bold">{{ todaySummary.total_trips }}</p>
                                </div>
                            </div>

                            <CollapsibleContent>
                                <div class="mt-4 grid grid-cols-2 gap-2 sm:grid-cols-3 lg:grid-cols-4">
                                    <div v-for="(value, label) in {
                                        Naga: todaySummary.naga_trips,
                                        Legazpi: todaySummary.legazpi_trips,
                                        Sorsogon: todaySummary.sorsogon_trips,
                                        Virac: todaySummary.virac_trips,
                                        Masbate: todaySummary.masbate_trips,
                                        Tabaco: todaySummary.tabaco_trips,
                                        Visayas: todaySummary.visayas_trips,
                                        Cargo: todaySummary.cargo_trips,
                                    }" :key="label" class="flex items-center justify-between rounded-md bg-muted/40 px-3 py-2 text-sm">
                                        <span class="text-muted-foreground">{{ label }}</span>
                                        <span class="font-semibold">{{ value }}</span>
                                    </div>
                                </div>
                            </CollapsibleContent>
                        </CardContent>
                    </Collapsible>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Quick Actions</CardTitle>
                        <CardDescription v-if="!todaySummary">No dispatch day created for today yet</CardDescription>
                    </CardHeader>
                    <CardContent class="flex flex-col gap-2">
                        <Button as-child class="w-full justify-start">
                            <Link href="/dispatch"><Bus class="mr-2 h-4 w-4" /> Go to Dispatch Board</Link>
                        </Button>
                        <Button as-child variant="outline" class="w-full justify-start">
                            <Link href="/reports"><ArrowRight class="mr-2 h-4 w-4" /> View Reports</Link>
                        </Button>
                        <Button as-child variant="outline" class="w-full justify-start">
                            <Link href="/history"><ArrowRight class="mr-2 h-4 w-4" /> View History</Link>
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
                    <div v-if="recentEntries.length === 0" class="py-12 text-center">
                        <Bus class="mx-auto mb-3 h-10 w-10 text-muted-foreground" />
                        <p class="mb-3 text-sm text-muted-foreground">No dispatch entries for today yet.</p>
                        <Button as-child size="sm">
                            <Link href="/dispatch">Start today's dispatch</Link>
                        </Button>
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
                                        <span v-if="entry.direction" class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium" :style="directionStyle(entry.direction)">
                                            {{ entry.direction }}
                                        </span>
                                        <span v-else>--</span>
                                    </td>
                                    <td class="py-2 pr-4">{{ formatTime(entry.scheduled_departure) }}</td>
                                    <td class="py-2 pr-4">{{ formatTime(entry.actual_departure) }}</td>
                                    <td class="py-2 pr-4">{{ entry.driver?.name || '--' }}</td>
                                    <td class="py-2"><StatusBadge :status="entry.status" /></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
