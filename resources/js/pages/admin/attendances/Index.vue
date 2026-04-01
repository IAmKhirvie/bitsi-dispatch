<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { type BreadcrumbItem, type PaginatedData } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Calendar, CheckCircle, Clock, RefreshCw, TriangleAlert, UserX, XCircle } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Attendance', href: '/admin/attendance' },
];

const props = defineProps<{
    attendances: PaginatedData<any>;
    date: string;
    statistics: {
        total: number;
        on_time: number;
        late: number;
        absent: number;
        pending: number;
        excused: number;
    };
}>();

function initializeToday() {
    if (confirm('Initialize attendance records for all drivers scheduled today?')) {
        router.post('/admin/attendance/initialize-today');
    }
}

function markAsLate(attendance: any) {
    const minutes = prompt('Enter minutes late:', '15');
    if (minutes && !isNaN(Number(minutes))) {
        router.post('/admin/attendance/mark-late', {
            driver_id: attendance.driver_id,
            dispatch_entry_id: attendance.dispatch_entry_id,
            minutes_late: Number(minutes),
        });
    }
}

function markAsAbsent(attendance: any) {
    if (confirm(`Mark ${attendance.driver.name} as absent?`)) {
        router.post('/admin/attendance/mark-absent', {
            driver_id: attendance.driver_id,
            dispatch_entry_id: attendance.dispatch_entry_id,
        });
    }
}

function overrideStatus(attendance: any) {
    const newStatus = prompt('Enter new status (on_time/late/absent/excused):');
    if (newStatus && ['on_time', 'late', 'absent', 'excused'].includes(newStatus)) {
        router.post('/admin/attendance/override', {
            attendance_id: attendance.id,
            status: newStatus,
        });
    }
}

const statusConfig: Record<string, { label: string; badge: string; icon: any }> = {
    pending: {
        label: 'Pending',
        badge: 'bg-gray-100 text-gray-700 dark:bg-gray-900 dark:text-gray-300',
        icon: Clock,
    },
    on_time: {
        label: 'On Time',
        badge: 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300',
        icon: CheckCircle,
    },
    late: {
        label: 'Late',
        badge: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300',
        icon: TriangleAlert,
    },
    absent: {
        label: 'Absent',
        badge: 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300',
        icon: UserX,
    },
    excused: {
        label: 'Excused',
        badge: 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300',
        icon: XCircle,
    },
};
</script>

<template>
    <Head title="Driver Attendance" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold">Driver Attendance</h1>
                    <p class="text-sm text-muted-foreground">Track driver check-ins and attendance status</p>
                </div>
                <div class="flex items-center gap-2">
                    <Link href="/admin/attendance/settings">
                        <Button variant="outline" size="sm">
                            <Settings class="mr-2 h-4 w-4" />
                            Settings
                        </Button>
                    </Link>
                    <Link href="/admin/attendance?date={{ today() }}">
                        <Button variant="outline" size="sm">
                            <Calendar class="mr-2 h-4 w-4" />
                            Today
                        </Button>
                    </Link>
                    <Button @click="initializeToday" variant="outline" size="sm">
                        <RefreshCw class="mr-2 h-4 w-4" />
                        Initialize Today
                    </Button>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <Card>
                    <CardHeader class="pb-2">
                        <CardTitle class="text-sm font-medium text-muted-foreground">Total</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ statistics.total }}</div>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader class="pb-2">
                        <CardTitle class="text-sm font-medium text-muted-foreground flex items-center gap-1">
                            <Clock class="h-3 w-3" />
                            Pending
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ statistics.pending }}</div>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader class="pb-2">
                        <CardTitle class="text-sm font-medium text-green-600 flex items-center gap-1">
                            <CheckCircle class="h-3 w-3" />
                            On Time
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ statistics.on_time }}</div>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader class="pb-2">
                        <CardTitle class="text-sm font-medium text-yellow-600 flex items-center gap-1">
                            <TriangleAlert class="h-3 w-3" />
                            Late
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ statistics.late }}</div>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader class="pb-2">
                        <CardTitle class="text-sm font-medium text-red-600 flex items-center gap-1">
                            <UserX class="h-3 w-3" />
                            Absent
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ statistics.absent }}</div>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader class="pb-2">
                        <CardTitle class="text-sm font-medium text-blue-600 flex items-center gap-1">
                            <XCircle class="h-3 w-3" />
                            Excused
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ statistics.excused }}</div>
                    </CardContent>
                </Card>
            </div>

            <!-- Attendance Table -->
            <Card>
                <CardHeader>
                    <CardTitle>Attendance Records</CardTitle>
                    <CardDescription>Date: {{ date }}</CardDescription>
                </CardHeader>
                <CardContent class="p-0">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b bg-muted/50">
                                    <th class="px-4 py-3 text-left font-medium text-muted-foreground">Driver</th>
                                    <th class="px-4 py-3 text-left font-medium text-muted-foreground">Trip / Route</th>
                                    <th class="px-4 py-3 text-left font-medium text-muted-foreground">Scheduled</th>
                                    <th class="px-4 py-3 text-left font-medium text-muted-foreground">Check In</th>
                                    <th class="px-4 py-3 text-left font-medium text-muted-foreground">Check Out</th>
                                    <th class="px-4 py-3 text-left font-medium text-muted-foreground">Status</th>
                                    <th class="px-4 py-3 text-left font-medium text-muted-foreground">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="attendances.data.length === 0">
                                    <td colspan="7" class="px-4 py-8 text-center text-muted-foreground">
                                        No attendance records found for this date.
                                        Click "Initialize Today" to create records for scheduled drivers.
                                    </td>
                                </tr>
                                <tr v-for="att in attendances.data" :key="att.id" class="border-b last:border-0 hover:bg-muted/30 transition-colors">
                                    <td class="px-4 py-3 font-medium">{{ att.driver.name }}</td>
                                    <td class="px-4 py-3">
                                        {{ att.dispatch_entry?.route || att.dispatch_entry?.trip_code?.code || '--' }}
                                    </td>
                                    <td class="px-4 py-3">{{ att.dispatch_entry?.scheduled_departure || '--' }}</td>
                                    <td class="px-4 py-3">{{ att.check_in_time || '--' }}</td>
                                    <td class="px-4 py-3">{{ att.check_out_time || '--' }}</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium"
                                            :class="statusConfig[att.status]?.badge || statusConfig.pending.badge">
                                            <component :is="statusConfig[att.status]?.icon || Clock" class="h-3 w-3" />
                                            {{ statusConfig[att.status]?.label || 'Pending' }}
                                        </span>
                                        <span v-if="att.status === 'late' && att.minutes_late" class="ml-1 text-xs text-muted-foreground">
                                            ({{ att.minutes_late }}m)
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-1">
                                            <Button
                                                v-if="att.status === 'pending'"
                                                size="sm"
                                                variant="outline"
                                                class="h-7 text-xs"
                                                @click="markAsLate(att)"
                                            >
                                                Mark Late
                                            </Button>
                                            <Button
                                                v-if="att.status === 'pending'"
                                                size="sm"
                                                variant="destructive"
                                                class="h-7 text-xs"
                                                @click="markAsAbsent(att)"
                                            >
                                                Absent
                                            </Button>
                                            <Button
                                                v-if="att.status !== 'pending'"
                                                size="sm"
                                                variant="ghost"
                                                class="h-7 text-xs"
                                                @click="overrideStatus(att)"
                                            >
                                                Override
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
            <div v-if="attendances.last_page > 1" class="flex items-center justify-between">
                <p class="text-sm text-muted-foreground">
                    Showing {{ (attendances.current_page - 1) * attendances.per_page + 1 }} to {{ Math.min(attendances.current_page * attendances.per_page, attendances.total) }} of {{ attendances.total }} results
                </p>
                <div class="flex items-center gap-1">
                    <template v-for="link in attendances.links" :key="link.label">
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
