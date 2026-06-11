<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
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
import { buttonVariants } from '@/components/ui/button';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowLeft, RotateCcw, Search, Trash2 } from 'lucide-vue-next';
import { ref, watch } from 'vue';

type PaginatedRow = Record<string, unknown> & { id: number; deleted_at: string };
type Paginated<T> = {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: Array<{ url: string | null; label: string; active: boolean }>;
};

const props = defineProps<{
    resource: string;
    label: string;
    columns: string[];
    items: Paginated<PaginatedRow>;
    filters: { search?: string };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Trash', href: '/admin/trash' },
    { title: props.label, href: `/admin/trash/${props.resource}` },
];

const search = ref(props.filters.search || '');
let timeout: ReturnType<typeof setTimeout>;

watch(search, () => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        router.get(`/admin/trash/${props.resource}`, { search: search.value || undefined }, {
            preserveState: true,
            preserveScroll: true,
        });
    }, 300);
});

function restore(id: number) {
    router.post(`/admin/trash/${props.resource}/${id}/restore`, {}, { preserveScroll: true });
}

function forceDelete(id: number) {
    router.delete(`/admin/trash/${props.resource}/${id}`, { preserveScroll: true });
}

function emptyAll() {
    router.delete(`/admin/trash/${props.resource}`, { preserveScroll: true });
}

function formatCell(value: unknown): string {
    if (value === null || value === undefined || value === '') return '--';
    if (typeof value === 'object') return JSON.stringify(value);
    return String(value);
}

function formatColumnHeader(col: string): string {
    return col.replace(/_/g, ' ').replace(/\b\w/g, (c) => c.toUpperCase());
}
</script>

<template>
    <Head :title="`Trash — ${label}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <Button as-child variant="ghost" size="icon" class="h-8 w-8">
                        <Link href="/admin/trash"><ArrowLeft class="h-4 w-4" /></Link>
                    </Button>
                    <div>
                        <h1 class="text-2xl font-bold">Trashed {{ label }}</h1>
                        <p class="text-sm text-muted-foreground">
                            Restore items to bring them back, or delete permanently.
                        </p>
                    </div>
                </div>

                <AlertDialog v-if="items.data.length > 0">
                    <AlertDialogTrigger :class="buttonVariants({ variant: 'outline' })">
                        <Trash2 class="mr-2 h-4 w-4" /> Empty trash
                    </AlertDialogTrigger>
                    <AlertDialogContent>
                        <AlertDialogHeader>
                            <AlertDialogTitle>Empty {{ label }} trash?</AlertDialogTitle>
                            <AlertDialogDescription>
                                This permanently deletes all {{ items.total }} item(s) in this trash bin. This action cannot be undone.
                            </AlertDialogDescription>
                        </AlertDialogHeader>
                        <AlertDialogFooter>
                            <AlertDialogCancel>Cancel</AlertDialogCancel>
                            <AlertDialogAction :class="buttonVariants({ variant: 'destructive' })" @click="emptyAll">
                                Permanently delete all
                            </AlertDialogAction>
                        </AlertDialogFooter>
                    </AlertDialogContent>
                </AlertDialog>
            </div>

            <div class="relative max-w-sm">
                <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                <Input v-model="search" placeholder="Search trashed items..." class="pl-9" />
            </div>

            <Card>
                <CardContent class="p-0">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b bg-muted/50">
                                    <th
                                        v-for="col in columns"
                                        :key="col"
                                        class="px-4 py-3 text-left font-medium text-muted-foreground"
                                    >
                                        {{ formatColumnHeader(col) }}
                                    </th>
                                    <th class="px-4 py-3 text-left font-medium text-muted-foreground">Deleted at</th>
                                    <th class="px-4 py-3 text-right font-medium text-muted-foreground">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="items.data.length === 0">
                                    <td :colspan="columns.length + 2" class="px-4 py-12 text-center text-muted-foreground">
                                        Nothing in the {{ label.toLowerCase() }} trash.
                                    </td>
                                </tr>
                                <tr
                                    v-for="row in items.data"
                                    :key="row.id"
                                    class="border-b last:border-0 hover:bg-muted/30 transition-colors"
                                >
                                    <td v-for="col in columns" :key="col" class="px-4 py-3">
                                        {{ formatCell(row[col]) }}
                                    </td>
                                    <td class="px-4 py-3 text-muted-foreground">{{ row.deleted_at }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-end gap-2">
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                class="h-8"
                                                @click="restore(row.id)"
                                            >
                                                <RotateCcw class="mr-1.5 h-3.5 w-3.5" /> Restore
                                            </Button>

                                            <AlertDialog>
                                                <AlertDialogTrigger
                                                    :class="buttonVariants({ variant: 'ghost', size: 'sm' }) + ' h-8 text-destructive hover:text-destructive'"
                                                >
                                                    <Trash2 class="mr-1.5 h-3.5 w-3.5" /> Delete forever
                                                </AlertDialogTrigger>
                                                <AlertDialogContent>
                                                    <AlertDialogHeader>
                                                        <AlertDialogTitle>Permanently delete this item?</AlertDialogTitle>
                                                        <AlertDialogDescription>
                                                            This action cannot be undone. The record will be removed from the database.
                                                        </AlertDialogDescription>
                                                    </AlertDialogHeader>
                                                    <AlertDialogFooter>
                                                        <AlertDialogCancel>Cancel</AlertDialogCancel>
                                                        <AlertDialogAction
                                                            :class="buttonVariants({ variant: 'destructive' })"
                                                            @click="forceDelete(row.id)"
                                                        >
                                                            Delete permanently
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

            <div v-if="items.last_page > 1" class="flex items-center justify-between">
                <p class="text-sm text-muted-foreground">
                    Showing {{ (items.current_page - 1) * items.per_page + 1 }} to
                    {{ Math.min(items.current_page * items.per_page, items.total) }} of {{ items.total }} results
                </p>
                <div class="flex items-center gap-1">
                    <template v-for="link in items.links" :key="link.label">
                        <Button
                            v-if="link.url"
                            as-child
                            variant="outline"
                            size="sm"
                            :class="{ 'bg-primary text-primary-foreground': link.active }"
                        >
                            <Link :href="link.url" v-html="link.label" preserve-scroll />
                        </Button>
                        <Button v-else variant="outline" size="sm" disabled v-html="link.label" />
                    </template>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
