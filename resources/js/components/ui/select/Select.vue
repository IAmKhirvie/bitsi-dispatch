<script setup lang="ts">
import { SelectRoot, useForwardPropsEmits } from 'radix-vue';

const props = defineProps();
const emits = defineEmits();

const forwarded = useForwardPropsEmits(props, emits);
</script>

<template>
    <SelectRoot v-bind="forwarded">
        <slot />
    </SelectRoot>
</template>
