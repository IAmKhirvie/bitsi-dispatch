<script setup lang="ts">
import { DropdownMenuRadioGroup, useForwardPropsEmits } from 'radix-vue';

const props = defineProps();
const emits = defineEmits();

const forwarded = useForwardPropsEmits(props, emits);
</script>

<template>
    <DropdownMenuRadioGroup v-bind="forwarded">
        <slot />
    </DropdownMenuRadioGroup>
</template>