<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{ role: string; label?: string }>();

const PALETTE: Record<string, { bg: string; fg: string }> = {
    admin:      { bg: 'hsl(0 93% 94%)',   fg: 'hsl(0 65% 38%)' },
    ops:        { bg: 'hsl(214 95% 93%)', fg: 'hsl(217 76% 38%)' },
    operations: { bg: 'hsl(214 95% 93%)', fg: 'hsl(217 76% 38%)' },
    dispatcher: { bg: 'hsl(138 76% 92%)', fg: 'hsl(142 65% 26%)' },
    driver:     { bg: 'hsl(48 96% 89%)',  fg: 'hsl(28 78% 35%)' },
};

const normalized = computed(() => String(props.role).toLowerCase());
const colors = computed(() => PALETTE[normalized.value] ?? { bg: 'hsl(0 0% 96%)', fg: 'hsl(0 0% 30%)' });
const displayLabel = computed(() => props.label ?? props.role);
</script>

<template>
    <span
        class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-medium capitalize"
        :style="{ backgroundColor: colors.bg, color: colors.fg }"
    >
        {{ displayLabel }}
    </span>
</template>
