<template>
    <span :class="[priorityClass(priority), sm ? 'text-[11px] px-2 py-0.5 font-medium' : 'text-xs px-2.5 py-1 font-semibold', 'rounded-full capitalize']">
        {{ priority }}
    </span>
</template>

<script setup>
import { priorityClass } from '@/composables/useFormatters'

defineProps({
    priority: { type: String, required: true },
    sm:       { type: Boolean, default: false },
})
</script>
