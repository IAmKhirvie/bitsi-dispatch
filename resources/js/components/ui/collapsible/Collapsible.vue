<script setup lang="ts">
import { CollapsibleRoot, useForwardPropsEmits } from 'radix-vue';

const props = defineProps();
const emits = defineEmits();

const forwarded = useForwardPropsEmits(props, emits);
</script>

<template>
    <CollapsibleRoot v-slot="{ open }" v-bind="forwarded">
        <slot :open="open" />
    </CollapsibleRoot>
</template>
