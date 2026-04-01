<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';
import { type BreadcrumbItem, type DriverStatus } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Drivers', href: '/admin/drivers' },
    { title: 'Create', href: '/admin/drivers/create' },
];

const statusOptions: { value: DriverStatus; label: string }[] = [
    { value: 'available', label: 'Available' },
    { value: 'dispatched', label: 'Dispatched' },
    { value: 'on_route', label: 'On Route' },
    { value: 'on_leave', label: 'On Leave' },
];

const form = useForm({
    name: '',
    phone: '',
    license_number: '',
    is_active: true,
    status: 'available' as DriverStatus,
});

function submit() {
    form.post('/admin/drivers', {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head title="Create Driver" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="mx-auto w-full max-w-2xl">
                <Card>
                    <CardHeader>
                        <CardTitle>Create Driver</CardTitle>
                        <CardDescription>Add a new driver to the roster.</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form @submit.prevent="submit" class="space-y-4">
                            <div class="space-y-2">
                                <Label for="name">Full Name</Label>
                                <Input id="name" v-model="form.name" placeholder="Driver full name" required />
                                <p v-if="form.errors.name" class="text-xs text-red-500">{{ form.errors.name }}</p>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <Label for="phone">Phone</Label>
                                    <Input id="phone" v-model="form.phone" placeholder="09XX XXX XXXX" />
                                    <p v-if="form.errors.phone" class="text-xs text-red-500">{{ form.errors.phone }}</p>
                                </div>
                                <div class="space-y-2">
                                    <Label for="license_number">License Number</Label>
                                    <Input id="license_number" v-model="form.license_number" placeholder="Driver's license number" />
                                    <p v-if="form.errors.license_number" class="text-xs text-red-500">{{ form.errors.license_number }}</p>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <Label for="status">Status</Label>
                                <select
                                    id="status"
                                    v-model="form.status"
                                    class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                                >
                                    <option v-for="opt in statusOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                                </select>
                                <p v-if="form.errors.status" class="text-xs text-red-500">{{ form.errors.status }}</p>
                            </div>

                            <div class="flex items-center gap-2">
                                <Checkbox id="is_active" :checked="form.is_active" @update:checked="form.is_active = $event" />
                                <Label for="is_active">Active</Label>
                            </div>

                            <div class="flex items-center justify-end gap-3 pt-4">
                                <Button as-child variant="outline">
                                    <Link href="/admin/drivers">Cancel</Link>
                                </Button>
                                <Button type="submit" :disabled="form.processing">
                                    Create Driver
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
