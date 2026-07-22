<script setup lang="ts">
import { DropdownMenuSub, useForwardPropsEmits } from 'radix-vue';

const props = defineProps();
const emits = defineEmits();

const forwarded = useForwardPropsEmits(props, emits);
</script>

<template>
    <DropdownMenuSub v-bind="forwarded">
        <slot />
    </DropdownMenuSub>
</template>