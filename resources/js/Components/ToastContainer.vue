<template>
    <Teleport to="body">
        <div class="fixed top-5 right-5 z-[9999] flex flex-col gap-2.5 w-80 pointer-events-none">
            <TransitionGroup
                enter-active-class="transition duration-300 ease-out"
                enter-from-class="opacity-0 translate-x-8 scale-95"
                enter-to-class="opacity-100 translate-x-0 scale-100"
                leave-active-class="transition duration-200 ease-in"
                leave-from-class="opacity-100 translate-x-0 scale-100"
                leave-to-class="opacity-0 translate-x-8 scale-95"
            >
                <div
                    v-for="toast in toasts"
                    :key="toast.id"
                    class="pointer-events-auto relative overflow-hidden rounded-xl shadow-lg border text-sm"
                    :class="toast.type === 'success'
                        ? 'bg-white dark:bg-ink-800 border-emerald-200 dark:border-emerald-400/20'
                        : 'bg-white dark:bg-ink-800 border-red-200 dark:border-red-400/20'"
                >
                    <!-- Content -->
                    <div class="flex items-start gap-3 px-4 py-3.5">
                        <!-- Icon -->
                        <div class="shrink-0 mt-0.5"
                            :class="toast.type === 'success' ? 'text-emerald-500' : 'text-red-500'">
                            <svg v-if="toast.type === 'success'" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <svg v-else class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                            </svg>
                        </div>

                        <!-- Message -->
                        <p class="flex-1 leading-snug text-ink-800 dark:text-white">{{ toast.message }}</p>

                        <!-- Close button -->
                        <button @click="remove(toast.id)"
                            class="shrink-0 text-ink-300 dark:text-white hover:text-ink-600 dark:hover:text-white transition mt-0.5">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Progress bar -->
                    <div class="h-0.5 w-full"
                        :class="toast.type === 'success' ? 'bg-emerald-100 dark:bg-emerald-400/10' : 'bg-red-100 dark:bg-red-400/10'">
                        <div
                            class="h-full transition-all ease-linear"
                            :class="toast.type === 'success' ? 'bg-emerald-400' : 'bg-red-400'"
                            :style="{ width: toast.progress + '%', transitionDuration: '50ms' }"
                        ></div>
                    </div>
                </div>
            </TransitionGroup>
        </div>
    </Teleport>
</template>

<script setup>
import { useToast } from '@/composables/useToast'

const { toasts, remove } = useToast()
</script>
