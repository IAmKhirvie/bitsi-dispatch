<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Vehicles', href: '/admin/vehicles' },
    { title: 'Create', href: '/admin/vehicles/create' },
];

const form = useForm({
    bus_number: '',
    brand: '',
    bus_type: '',
    plate_number: '',
    status: 'OK',
    pms_unit: 'km',
    pms_threshold: 10000,
    current_pms_value: 0,
    last_pms_date: '',
});

function submit() {
    form.post('/admin/vehicles', {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head title="Create Vehicle" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="mx-auto w-full max-w-2xl">
                <Card>
                    <CardHeader>
                        <CardTitle>Create Vehicle</CardTitle>
                        <CardDescription>Add a new vehicle to the fleet.</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form @submit.prevent="submit" class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <Label for="bus_number">Bus Number</Label>
                                    <Input id="bus_number" v-model="form.bus_number" placeholder="e.g. 2801" required />
                                    <p v-if="form.errors.bus_number" class="text-xs text-red-500">{{ form.errors.bus_number }}</p>
                                </div>
                                <div class="space-y-2">
                                    <Label for="brand">Brand</Label>
                                    <Input id="brand" v-model="form.brand" placeholder="e.g. DLTB" required />
                                    <p v-if="form.errors.brand" class="text-xs text-red-500">{{ form.errors.brand }}</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <Label for="bus_type">Bus Type</Label>
                                    <Input id="bus_type" v-model="form.bus_type" placeholder="e.g. Airconditioned" required />
                                    <p v-if="form.errors.bus_type" class="text-xs text-red-500">{{ form.errors.bus_type }}</p>
                                </div>
                                <div class="space-y-2">
                                    <Label for="plate_number">Plate Number</Label>
                                    <Input id="plate_number" v-model="form.plate_number" placeholder="e.g. ABC 1234" required />
                                    <p v-if="form.errors.plate_number" class="text-xs text-red-500">{{ form.errors.plate_number }}</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <Label for="status">Status</Label>
                                    <select id="status" v-model="form.status" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                                        <option value="OK">OK</option>
                                        <option value="UR">Under Repair (UR)</option>
                                        <option value="PMS">PMS</option>
                                        <option value="In Transit">In Transit</option>
                                        <option value="Lutaw">Lutaw</option>
                                    </select>
                                    <p v-if="form.errors.status" class="text-xs text-red-500">{{ form.errors.status }}</p>
                                </div>
                            </div>

                            <div class="border-t pt-4">
                                <h3 class="mb-3 font-semibold">Preventive Maintenance Schedule (PMS)</h3>
                                <div class="grid grid-cols-3 gap-4">
                                    <div class="space-y-2">
                                        <Label for="pms_unit">PMS Unit</Label>
                                        <select id="pms_unit" v-model="form.pms_unit" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                                            <option value="km">Kilometers</option>
                                            <option value="trips">Trips</option>
                                        </select>
                                    </div>
                                    <div class="space-y-2">
                                        <Label for="pms_threshold">PMS Threshold</Label>
                                        <Input id="pms_threshold" type="number" v-model.number="form.pms_threshold" min="0" />
                                        <p v-if="form.errors.pms_threshold" class="text-xs text-red-500">{{ form.errors.pms_threshold }}</p>
                                    </div>
                                    <div class="space-y-2">
                                        <Label for="current_pms_value">Current Value</Label>
                                        <Input id="current_pms_value" type="number" v-model.number="form.current_pms_value" min="0" />
                                        <p v-if="form.errors.current_pms_value" class="text-xs text-red-500">{{ form.errors.current_pms_value }}</p>
                                    </div>
                                </div>
                                <div class="mt-4 space-y-2">
                                    <Label for="last_pms_date">Last PMS Date</Label>
                                    <Input id="last_pms_date" type="date" v-model="form.last_pms_date" class="max-w-xs" />
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-3 pt-4">
                                <Button as-child variant="outline">
                                    <Link href="/admin/vehicles">Cancel</Link>
                                </Button>
                                <Button type="submit" :disabled="form.processing">
                                    Create Vehicle
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
