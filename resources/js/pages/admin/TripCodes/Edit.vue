<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';
import { type BreadcrumbItem, type TripCode } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    tripCode: TripCode;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Trip Codes', href: '/admin/trip-codes' },
    { title: 'Edit', href: `/admin/trip-codes/${props.tripCode.id}/edit` },
];

const form = useForm({
    code: props.tripCode.code,
    operator: props.tripCode.operator,
    origin_terminal: props.tripCode.origin_terminal,
    destination_terminal: props.tripCode.destination_terminal,
    bus_type: props.tripCode.bus_type,
    scheduled_departure_time: props.tripCode.scheduled_departure_time,
    direction: props.tripCode.direction,
    is_active: props.tripCode.is_active,
});

function submit() {
    form.put(`/admin/trip-codes/${props.tripCode.id}`, {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head :title="`Edit ${tripCode.code}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="mx-auto w-full max-w-2xl">
                <Card>
                    <CardHeader>
                        <CardTitle>Edit Trip Code</CardTitle>
                        <CardDescription>Update trip code {{ tripCode.code }}.</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form @submit.prevent="submit" class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <Label for="code">Trip Code</Label>
                                    <Input id="code" v-model="form.code" placeholder="e.g. TC-001" required />
                                    <p v-if="form.errors.code" class="text-xs text-red-500">{{ form.errors.code }}</p>
                                </div>
                                <div class="space-y-2">
                                    <Label for="operator">Operator</Label>
                                    <Input id="operator" v-model="form.operator" placeholder="e.g. DLTB" required />
                                    <p v-if="form.errors.operator" class="text-xs text-red-500">{{ form.errors.operator }}</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <Label for="origin_terminal">Origin Terminal</Label>
                                    <Input id="origin_terminal" v-model="form.origin_terminal" placeholder="e.g. Cubao" required />
                                    <p v-if="form.errors.origin_terminal" class="text-xs text-red-500">{{ form.errors.origin_terminal }}</p>
                                </div>
                                <div class="space-y-2">
                                    <Label for="destination_terminal">Destination Terminal</Label>
                                    <Input id="destination_terminal" v-model="form.destination_terminal" placeholder="e.g. Naga" required />
                                    <p v-if="form.errors.destination_terminal" class="text-xs text-red-500">{{ form.errors.destination_terminal }}</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <Label for="bus_type">Bus Type</Label>
                                    <Input id="bus_type" v-model="form.bus_type" placeholder="e.g. Airconditioned" required />
                                    <p v-if="form.errors.bus_type" class="text-xs text-red-500">{{ form.errors.bus_type }}</p>
                                </div>
                                <div class="space-y-2">
                                    <Label for="scheduled_departure_time">Scheduled Departure</Label>
                                    <Input id="scheduled_departure_time" type="time" v-model="form.scheduled_departure_time" required />
                                    <p v-if="form.errors.scheduled_departure_time" class="text-xs text-red-500">{{ form.errors.scheduled_departure_time }}</p>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <Label for="direction">Direction</Label>
                                <select id="direction" v-model="form.direction" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                                    <option value="SB">Southbound (SB)</option>
                                    <option value="NB">Northbound (NB)</option>
                                </select>
                                <p v-if="form.errors.direction" class="text-xs text-red-500">{{ form.errors.direction }}</p>
                            </div>

                            <div class="flex items-center gap-2">
                                <Checkbox id="is_active" :checked="form.is_active" @update:checked="form.is_active = $event" />
                                <Label for="is_active">Active</Label>
                            </div>

                            <div class="flex items-center justify-end gap-3 pt-4">
                                <Button as-child variant="outline">
                                    <Link href="/admin/trip-codes">Cancel</Link>
                                </Button>
                                <Button type="submit" :disabled="form.processing">
                                    Update Trip Code
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
