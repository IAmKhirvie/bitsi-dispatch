<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import ExportButtons from '@/components/ExportButtons.vue';
import RoleBadge from '@/components/RoleBadge.vue';
import { Button, buttonVariants } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger,
} from '@/components/ui/alert-dialog';
import { type BreadcrumbItem, type PaginatedData, type User } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Plus, Pencil, Trash2, Search } from 'lucide-vue-next';
import { ref, watch } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Users', href: '/admin/users' },
];

const props = defineProps<{
    users: PaginatedData<User>;
    filters: { search?: string; role?: string };
}>();

const search = ref(props.filters.search || '');
const roleFilter = ref(props.filters.role || 'all');

let searchTimeout: ReturnType<typeof setTimeout>;

watch([search, roleFilter], () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get('/admin/users', {
            search: search.value || undefined,
            role: roleFilter.value === 'all' ? undefined : roleFilter.value,
        }, {
            preserveState: true,
            preserveScroll: true,
        });
    }, 150);
});

function deleteUser(user: User) {
    router.delete(`/admin/users/${user.id}`, { preserveScroll: true });
}

function formatRole(role: string): string {
    return role.replace(/_/g, ' ').replace(/\b\w/g, (c) => c.toUpperCase());
}
</script>

<template>
    <Head title="Users" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold">Users</h1>
                    <p class="text-sm text-muted-foreground">Manage system users and their roles</p>
                </div>
                <div class="flex items-center gap-2">
                    <ExportButtons entity="users" />
                    <Button as-child>
                        <Link href="/admin/users/create" prefetch>
                            <Plus class="mr-2 h-4 w-4" />
                            Add User
                        </Link>
                    </Button>
                </div>
            </div>

            <!-- Filters -->
            <div class="flex flex-wrap items-center gap-3">
                <div class="relative flex-1 min-w-[200px] max-w-sm">
                    <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                    <Input v-model="search" placeholder="Search users..." class="pl-9" />
                </div>
                <Select v-model="roleFilter">
                    <SelectTrigger class="w-[180px]">
                        <SelectValue placeholder="All roles" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="all">All Roles</SelectItem>
                        <SelectItem value="admin">Admin</SelectItem>
                        <SelectItem value="operations_manager">Operations Manager</SelectItem>
                        <SelectItem value="dispatcher">Dispatcher</SelectItem>
                    </SelectContent>
                </Select>
            </div>

            <!-- Table -->
            <Card>
                <CardContent class="p-0">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b bg-muted/50">
                                    <th class="px-4 py-3 text-left font-medium text-muted-foreground">Name</th>
                                    <th class="px-4 py-3 text-left font-medium text-muted-foreground">Email</th>
                                    <th class="px-4 py-3 text-left font-medium text-muted-foreground">Role</th>
                                    <th class="px-4 py-3 text-left font-medium text-muted-foreground">Phone</th>
                                    <th class="px-4 py-3 text-left font-medium text-muted-foreground">Status</th>
                                    <th class="px-4 py-3 text-left font-medium text-muted-foreground">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="users.data.length === 0">
                                    <td colspan="6" class="px-4 py-8 text-center text-muted-foreground">No users found.</td>
                                </tr>
                                <tr v-for="user in users.data" :key="user.id" class="border-b last:border-0 hover:bg-muted/30 transition-colors">
                                    <td class="px-4 py-3 font-medium">{{ user.name }}</td>
                                    <td class="px-4 py-3">{{ user.email }}</td>
                                    <td class="px-4 py-3">
                                        <RoleBadge :role="user.role" :label="formatRole(user.role)" />
                                    </td>
                                    <td class="px-4 py-3">{{ user.phone || '--' }}</td>
                                    <td class="px-4 py-3">
                                        <span
                                            class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-medium"
                                            :style="user.is_active
                                                ? { backgroundColor: 'hsl(138 76% 92%)', color: 'hsl(142 65% 26%)' }
                                                : { backgroundColor: 'hsl(0 93% 94%)', color: 'hsl(0 65% 38%)' }"
                                        >
                                            {{ user.is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <Button as-child variant="ghost" size="icon" class="h-8 w-8" title="Edit user">
                                                <Link :href="`/admin/users/${user.id}/edit`" prefetch>
                                                    <Pencil class="h-4 w-4" />
                                                </Link>
                                            </Button>

                                            <AlertDialog>
                                                <AlertDialogTrigger
                                                    :class="buttonVariants({ variant: 'ghost', size: 'icon' }) + ' h-8 w-8 text-destructive hover:text-destructive'"
                                                    title="Delete user"
                                                >
                                                    <Trash2 class="h-4 w-4" />
                                                </AlertDialogTrigger>
                                                <AlertDialogContent>
                                                    <AlertDialogHeader>
                                                        <AlertDialogTitle>Delete {{ user.name }}?</AlertDialogTitle>
                                                        <AlertDialogDescription>
                                                            The user will be moved to Trash. You can restore them within 30 days.
                                                        </AlertDialogDescription>
                                                    </AlertDialogHeader>
                                                    <AlertDialogFooter>
                                                        <AlertDialogCancel>Cancel</AlertDialogCancel>
                                                        <AlertDialogAction
                                                            :class="buttonVariants({ variant: 'destructive' })"
                                                            @click="deleteUser(user)"
                                                        >
                                                            Delete
                                                        </AlertDialogAction>
                                                    </AlertDialogFooter>
                                                </AlertDialogContent>
                                            </AlertDialog>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </CardContent>
            </Card>

            <!-- Pagination -->
            <div v-if="users.last_page > 1" class="flex items-center justify-between">
                <p class="text-sm text-muted-foreground">
                    Showing {{ (users.current_page - 1) * users.per_page + 1 }} to {{ Math.min(users.current_page * users.per_page, users.total) }} of {{ users.total }} results
                </p>
                <div class="flex items-center gap-1">
                    <template v-for="link in users.links" :key="link.label">
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
