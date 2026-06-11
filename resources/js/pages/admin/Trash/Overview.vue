<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent } from '@/components/ui/card';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Trash2, ChevronRight } from 'lucide-vue-next';

defineProps<{
    resources: Array<{ key: string; label: string; count: number }>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Trash', href: '/admin/trash' },
];
</script>

<template>
    <Head title="Trash" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold flex items-center gap-2">
                        <Trash2 class="h-6 w-6" />
                        Trash
                    </h1>
                    <p class="text-sm text-muted-foreground">
                        Recently deleted records. Restore or permanently delete them.
                    </p>
                </div>
            </div>

            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                <Link
                    v-for="r in resources"
                    :key="r.key"
                    :href="`/admin/trash/${r.key}`"
                    class="block"
                >
                    <Card class="hover:bg-muted/30 transition-colors">
                        <CardContent class="flex items-center justify-between p-4">
                            <div>
                                <p class="font-medium">{{ r.label }}</p>
                                <p class="text-sm text-muted-foreground">
                                    {{ r.count }} deleted {{ r.count === 1 ? 'record' : 'records' }}
                                </p>
                            </div>
                            <ChevronRight class="h-5 w-5 text-muted-foreground" />
                        </CardContent>
                    </Card>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
