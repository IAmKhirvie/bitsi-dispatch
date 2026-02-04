<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
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
const roleFilter = ref(props.filters.role || '');

let searchTimeout: ReturnType<typeof setTimeout>;

watch([search, roleFilter], () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get('/admin/users', {
            search: search.value || undefined,
            role: roleFilter.value || undefined,
        }, {
            preserveState: true,
            preserveScroll: true,
        });
    }, 300);
});

function deleteUser(user: User) {
    if (confirm(`Are you sure you want to delete ${user.name}?`)) {
        router.delete(`/admin/users/${user.id}`);
    }
}

function formatRole(role: string): string {
    return role.replace(/_/g, ' ').replace(/\b\w/g, (c) => c.toUpperCase());
}

const roleClasses: Record<string, string> = {
    admin: 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300',
    operations_manager: 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300',
    dispatcher: 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300',
};
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
                <Button as-child>
                    <Link href="/admin/users/create">
                        <Plus class="mr-2 h-4 w-4" />
                        Add User
                    </Link>
                </Button>
            </div>

            <!-- Filters -->
            <div class="flex flex-wrap items-center gap-3">
                <div class="relative flex-1 min-w-[200px] max-w-sm">
                    <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                    <Input v-model="search" placeholder="Search users..." class="pl-9" />
                </div>
                <select v-model="roleFilter" class="flex h-9 rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                    <option value="">All Roles</option>
                    <option value="admin">Admin</option>
                    <option value="operations_manager">Operations Manager</option>
                    <option value="dispatcher">Dispatcher</option>
                </select>
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
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium"
                                            :class="roleClasses[user.role] || 'bg-gray-100 text-gray-700'">
                                            {{ formatRole(user.role) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">{{ user.phone || '--' }}</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium"
                                            :class="user.is_active ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300'">
                                            {{ user.is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <Button as-child variant="ghost" size="icon" class="h-8 w-8">
                                                <Link :href="`/admin/users/${user.id}/edit`">
                                                    <Pencil class="h-4 w-4" />
                                                </Link>
                                            </Button>
                                            <Button variant="ghost" size="icon" class="h-8 w-8 text-red-500 hover:text-red-700" @click="deleteUser(user)">
                                                <Trash2 class="h-4 w-4" />
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
