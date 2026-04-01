<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Settings } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Settings', href: '/admin/settings' },
];

const props = defineProps<{
    settings: {
        late_threshold_minutes: string;
        pre_departure_alert_minutes: string;
        auto_absent_timeout_minutes: string;
        require_check_in: string;
    };
}>();

const form = ref({
    late_threshold_minutes: props.settings.late_threshold_minutes || '15',
    pre_departure_alert_minutes: props.settings.pre_departure_alert_minutes || '15',
    auto_absent_timeout_minutes: props.settings.auto_absent_timeout_minutes || '30',
    require_check_in: props.settings.require_check_in === 'true',
});

let saveTimeout: ReturnType<typeof setTimeout>;

watch(form, () => {
    clearTimeout(saveTimeout);
    saveTimeout = setTimeout(saveSettings, 1000);
}, { deep: true });

function saveSettings() {
    router.put('/admin/attendance/settings', form.value, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            // Settings saved
        },
    });
}
</script>

<template>
    <Head title="Attendance Settings" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="flex items-center gap-4">
                <div class="p-2 rounded-lg bg-primary/10">
                    <Settings class="h-6 w-6 text-primary" />
                </div>
                <div>
                    <h1 class="text-2xl font-bold">Attendance Settings</h1>
                    <p class="text-sm text-muted-foreground">Configure driver attendance thresholds and alerts</p>
                </div>
            </div>

            <div class="mx-auto w-full max-w-2xl space-y-6">
                <!-- Late Threshold -->
                <Card>
                    <CardHeader>
                        <CardTitle>Late Threshold</CardTitle>
                        <CardDescription>How many minutes after scheduled time is a driver considered late?</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div class="space-y-2">
                                <Label for="late_threshold">Late Threshold (minutes)</Label>
                                <Input
                                    id="late_threshold"
                                    v-model.number="form.late_threshold_minutes"
                                    type="number"
                                    min="1"
                                    max="120"
                                    class="max-w-xs"
                                />
                                <p class="text-xs text-muted-foreground">
                                    Drivers checking in after {{ form.late_threshold_minutes }} minutes past scheduled time will be marked as late.
                                </p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Pre-departure Alert -->
                <Card>
                    <CardHeader>
                        <CardTitle>Pre-departure Alert</CardTitle>
                        <CardDescription>When should the dispatcher be alerted about upcoming trips with no check-in?</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div class="space-y-2">
                                <Label for="pre_departure">Alert Before Departure (minutes)</Label>
                                <Input
                                    id="pre_departure"
                                    v-model.number="form.pre_departure_alert_minutes"
                                    type="number"
                                    min="1"
                                    max="120"
                                    class="max-w-xs"
                                />
                                <p class="text-xs text-muted-foreground">
                                    Alert dispatcher {{ form.pre_departure_alert_minutes }} minutes before scheduled departure if driver hasn't checked in.
                                </p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Auto-absent Timeout -->
                <Card>
                    <CardHeader>
                        <CardTitle>Auto-absent Timeout</CardTitle>
                        <CardDescription>After how many minutes should a no-show driver be marked as absent?</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div class="space-y-2">
                                <Label for="auto_absent">Auto-absent Timeout (minutes)</Label>
                                <Input
                                    id="auto_absent"
                                    v-model.number="form.auto_absent_timeout_minutes"
                                    type="number"
                                    min="1"
                                    max="240"
                                    class="max-w-xs"
                                />
                                <p class="text-xs text-muted-foreground">
                                    Drivers who haven't checked in {{ form.auto_absent_timeout_minutes }} minutes after scheduled time will be auto-marked as absent.
                                </p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Require Check-in -->
                <Card>
                    <CardHeader>
                        <CardTitle>Check-in Requirement</CardTitle>
                        <CardDescription>Should check-in be mandatory for all assigned drivers?</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="flex items-center justify-between">
                            <div class="space-y-0.5">
                                <Label for="require_check_in" class="text-base">Require Check-in</Label>
                                <p class="text-sm text-muted-foreground">
                                    When enabled, all assigned drivers must check in before their trip
                                </p>
                            </div>
                            <Switch
                                id="require_check_in"
                                v-model="form.require_check_in"
                            />
                        </div>
                    </CardContent>
                </Card>

                <!-- Back Button -->
                <div class="flex justify-end">
                    <Button as-child variant="outline">
                        <Link href="/admin/attendance">
                            Back to Attendance
                        </Link>
                    </Button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
