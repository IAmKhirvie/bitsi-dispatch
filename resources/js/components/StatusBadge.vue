<script setup lang="ts">
import { computed } from 'vue';

type Status = 'scheduled' | 'departed' | 'on_route' | 'on-route' | 'delayed' | 'arrived' | 'cancelled' | string;

const props = withDefaults(
    defineProps<{
        status: Status;
        label?: string;
        size?: 'sm' | 'md';
    }>(),
    { size: 'sm' },
);

// Semantic palette — single source of truth for status colors.
// Keep these in sync with --status-* tokens in resources/css/app.css.
const PALETTE: Record<string, { bg: string; fg: string; dot: string }> = {
    scheduled: { bg: 'hsl(214 95% 93%)', fg: 'hsl(217 76% 38%)', dot: 'hsl(217 91% 60%)' },
    departed:  { bg: 'hsl(48 96% 89%)',  fg: 'hsl(28 78% 35%)',  dot: 'hsl(38 92% 50%)' },
    on_route:  { bg: 'hsl(204 94% 94%)', fg: 'hsl(201 79% 32%)', dot: 'hsl(199 89% 48%)' },
    delayed:   { bg: 'hsl(33 100% 92%)', fg: 'hsl(20 78% 35%)',  dot: 'hsl(25 95% 53%)' },
    arrived:   { bg: 'hsl(138 76% 92%)', fg: 'hsl(142 65% 26%)', dot: 'hsl(142 71% 45%)' },
    cancelled: { bg: 'hsl(0 93% 94%)',   fg: 'hsl(0 65% 38%)',   dot: 'hsl(0 84% 60%)' },
};

const normalized = computed(() => String(props.status).toLowerCase().replace('-', '_'));
const colors = computed(() => PALETTE[normalized.value] ?? { bg: 'hsl(0 0% 96%)', fg: 'hsl(0 0% 30%)', dot: 'hsl(0 0% 45%)' });
const displayLabel = computed(() => props.label ?? String(props.status).replace(/_/g, ' '));

const sizeClass = computed(() =>
    props.size === 'md' ? 'px-2.5 py-1 text-xs' : 'px-2 py-0.5 text-[11px]',
);
</script>

<template>
    <span
        :class="['inline-flex items-center gap-1.5 rounded-full font-medium capitalize', sizeClass]"
        :style="{ backgroundColor: colors.bg, color: colors.fg }"
    >
        <span class="h-1.5 w-1.5 rounded-full" :style="{ backgroundColor: colors.dot }" />
        {{ displayLabel }}
    </span>
</template>
