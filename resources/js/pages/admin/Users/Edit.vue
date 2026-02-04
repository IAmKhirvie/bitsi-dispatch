<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';
import { type BreadcrumbItem, type User } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    user: User;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Users', href: '/admin/users' },
    { title: 'Edit', href: `/admin/users/${props.user.id}/edit` },
];

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    password: '',
    password_confirmation: '',
    role: props.user.role,
    phone: props.user.phone || '',
    is_active: props.user.is_active,
});

function submit() {
    form.put(`/admin/users/${props.user.id}`, {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head :title="`Edit ${user.name}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="mx-auto w-full max-w-2xl">
                <Card>
                    <CardHeader>
                        <CardTitle>Edit User</CardTitle>
                        <CardDescription>Update user information for {{ user.name }}.</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form @submit.prevent="submit" class="space-y-4">
                            <div class="space-y-2">
                                <Label for="name">Name</Label>
                                <Input id="name" v-model="form.name" placeholder="Full name" required />
                                <p v-if="form.errors.name" class="text-xs text-red-500">{{ form.errors.name }}</p>
                            </div>

                            <div class="space-y-2">
                                <Label for="email">Email</Label>
                                <Input id="email" type="email" v-model="form.email" placeholder="email@example.com" required />
                                <p v-if="form.errors.email" class="text-xs text-red-500">{{ form.errors.email }}</p>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <Label for="password">Password</Label>
                                    <Input id="password" type="password" v-model="form.password" placeholder="Leave blank to keep current" />
                                    <p v-if="form.errors.password" class="text-xs text-red-500">{{ form.errors.password }}</p>
                                </div>
                                <div class="space-y-2">
                                    <Label for="password_confirmation">Confirm Password</Label>
                                    <Input id="password_confirmation" type="password" v-model="form.password_confirmation" placeholder="Leave blank to keep current" />
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <Label for="role">Role</Label>
                                    <select id="role" v-model="form.role" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                                        <option value="admin">Admin</option>
                                        <option value="operations_manager">Operations Manager</option>
                                        <option value="dispatcher">Dispatcher</option>
                                    </select>
                                    <p v-if="form.errors.role" class="text-xs text-red-500">{{ form.errors.role }}</p>
                                </div>
                                <div class="space-y-2">
                                    <Label for="phone">Phone</Label>
                                    <Input id="phone" v-model="form.phone" placeholder="09XX XXX XXXX" />
                                    <p v-if="form.errors.phone" class="text-xs text-red-500">{{ form.errors.phone }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <Checkbox id="is_active" :checked="form.is_active" @update:checked="form.is_active = $event" />
                                <Label for="is_active">Active</Label>
                            </div>

                            <div class="flex items-center justify-end gap-3 pt-4">
                                <Button as-child variant="outline">
                                    <Link href="/admin/users">Cancel</Link>
                                </Button>
                                <Button type="submit" :disabled="form.processing">
                                    Update User
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
