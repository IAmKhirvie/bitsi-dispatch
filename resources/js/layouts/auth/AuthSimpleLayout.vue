<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { Link } from '@inertiajs/vue3';
import { onMounted, onUnmounted } from 'vue';

defineProps<{
    title?: string;
    description?: string;
}>();

// Force light mode on auth pages (login, register, forgot password)
// and restore user preference when leaving
let savedAppearance: string | null = null;

onMounted(() => {
    savedAppearance = localStorage.getItem('appearance');
    document.documentElement.classList.remove('dark');
});

onUnmounted(() => {
    if (savedAppearance === 'dark') {
        document.documentElement.classList.add('dark');
    } else if (savedAppearance === 'system') {
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        document.documentElement.classList.toggle('dark', prefersDark);
    }
    // If savedAppearance is 'light' or null, dark is already removed
});
</script>

<template>
    <div class="flex min-h-svh flex-col items-center justify-center gap-6 bg-background p-6 md:p-10">
        <div class="w-full max-w-sm">
            <div class="flex flex-col gap-8">
                <div class="flex flex-col items-center gap-4">
                    <Link :href="route('home')" class="flex flex-col items-center gap-2 font-medium">
                        <div class="mb-1 flex items-center justify-center rounded-md">
                            <AppLogoIcon class="h-24 w-auto fill-current text-[var(--foreground)] dark:text-white" />
                        </div>
                        <span class="sr-only">{{ title }}</span>
                    </Link>
                    <div class="space-y-2 text-center">
                        <h1 class="text-xl font-medium">{{ title }}</h1>
                        <p class="text-center text-sm text-muted-foreground">{{ description }}</p>
                    </div>
                </div>
                <slot />
            </div>
        </div>
    </div>
</template>
