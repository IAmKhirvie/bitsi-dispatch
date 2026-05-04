<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';

interface Props {
    class?: string;
}

defineProps<Props>();
</script>

<template>
    <AppLogoIcon class="size-8 fill-current" style="background: transparent;" />
    <div class="ml-2 grid flex-1 text-left text-sm">
        <span class="mb-0.5 truncate font-semibold leading-none">BITSI Dispatch</span>
    </div>
</template>
