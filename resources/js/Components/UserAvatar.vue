<template>
    <div
        class="rounded-full font-bold flex items-center justify-center shrink-0 select-none"
        :class="sizeClass"
    >{{ name.charAt(0).toUpperCase() }}</div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    name: { type: String, required: true },
    size: { type: String, default: 'md' },
})

const sizeClass = computed(() => ({
    xs: 'w-5 h-5 text-[9px]',
    sm: 'w-6 h-6 text-[10px]',
    md: 'w-7 h-7 text-xs',
    lg: 'w-8 h-8 text-sm',
    xl: 'w-9 h-9 text-sm',
}[props.size] ?? 'w-7 h-7 text-xs'))
</script>
