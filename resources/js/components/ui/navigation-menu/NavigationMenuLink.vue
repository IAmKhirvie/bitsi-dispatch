<script setup lang="ts">
import {
  NavigationMenuLink,
  useForwardPropsEmits,
} from 'radix-vue'

const props = defineProps()
const emits = defineEmits()

const forwarded = useForwardPropsEmits(props, emits)
</script>

<template>
  <NavigationMenuLink v-bind="forwarded">
    <slot />
  </NavigationMenuLink>
</template>