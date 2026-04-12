<template>
    <span :class="[taskStatusClass(status), sm ? 'text-[11px] px-2 py-0.5 font-medium' : 'text-xs px-2.5 py-1 font-semibold', 'rounded-full']">
        {{ taskStatusLabel(status) }}
    </span>
</template>

<script setup>
import { taskStatusLabel, taskStatusClass } from '@/composables/useFormatters'

defineProps({
    status: { type: String, required: true },
    sm:     { type: Boolean, default: false },
})
</script>
