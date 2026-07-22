<script setup lang="ts">
import { DialogRoot, useForwardPropsEmits } from 'radix-vue';

const props = defineProps();
const emits = defineEmits();

const forwarded = useForwardPropsEmits(props, emits);
</script>

<template>
    <DialogRoot v-bind="forwarded">
        <slot />
    </DialogRoot>
</template>
