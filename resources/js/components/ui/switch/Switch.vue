<script setup lang="ts">
import { computed } from 'vue';
import { Checkbox } from '@/components/ui/checkbox';
import { cn } from '@/lib/utils';

const props = defineProps<{
    checked?: boolean;
    disabled?: boolean;
    class?: string;
}>();

const emits = defineEmits<{
    (e: 'update:checked', value: boolean): void;
}>();

const isChecked = computed({
    get: () => props.checked ?? false,
    set: (value) => emits('update:checked', value),
});
</script>

<template>
    <div :class="cn('peer inline-flex h-5 w-9 shrink-0 cursor-pointer items-center rounded-full border-2 border-transparent transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50', isChecked ? 'bg-primary' : 'bg-input', props.class)">
        <Checkbox
            :model-value="isChecked"
            @update:model-value="emits('update:checked', $event)"
            class="sr-only"
        />
        <span
            :class="cn('pointer-events-none block h-4 w-4 rounded-full bg-background shadow-lg ring-0 transition-transform', isChecked ? 'translate-x-4' : 'translate-x-0')"
        />
    </div>
</template>
