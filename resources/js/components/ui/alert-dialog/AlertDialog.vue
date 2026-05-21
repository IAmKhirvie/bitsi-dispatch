<script setup lang="ts">
import { AlertDialogRoot, useForwardPropsEmits, type AlertDialogRootEmits, type AlertDialogRootProps } from 'radix-vue';

const props = defineProps<AlertDialogRootProps>();
const emits = defineEmits<AlertDialogRootEmits>();

const forwarded = useForwardPropsEmits(props, emits);
</script>

<template>
    <AlertDialogRoot v-bind="forwarded">
        <slot />
    </AlertDialogRoot>
</template>
