<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
    <img 
        src="/bicol_isarog_logo.png" 
        alt="Bicol Isarog Logo" 
        :class="className" 
        v-bind="$attrs"
        style="background: transparent;"
    />
</template>
