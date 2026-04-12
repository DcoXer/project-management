<template>
    <div class="space-y-5">

        <!-- Header -->
        <div class="space-y-2">
            <div class="h-7 w-44 rounded-lg bg-ink-200 dark:bg-ink-700 animate-pulse"></div>
            <div class="h-4 w-64 rounded-lg bg-ink-100 dark:bg-ink-800 animate-pulse"></div>
        </div>

        <!-- Filter bar -->
        <div class="bg-white dark:bg-ink-800 rounded-xl border border-ink-900/8 dark:border-sage-300/8 p-4">
            <div class="flex gap-3">
                <div class="h-9 w-32 rounded-lg bg-ink-100 dark:bg-ink-700 animate-pulse"></div>
                <div class="h-9 w-32 rounded-lg bg-ink-100 dark:bg-ink-700 animate-pulse"></div>
                <div class="h-9 w-28 rounded-lg bg-ink-100 dark:bg-ink-700 animate-pulse"></div>
            </div>
        </div>

        <!-- Content card -->
        <div class="bg-white dark:bg-ink-800 rounded-xl border border-ink-900/8 dark:border-sage-300/8 overflow-hidden">
            <!-- Table header -->
            <div class="flex items-center gap-6 px-5 py-3 border-b border-ink-100 dark:border-sage-300/8 bg-sage-50 dark:bg-ink-850">
                <div class="h-3 w-28 rounded bg-ink-200 dark:bg-ink-700 animate-pulse"></div>
                <div class="h-3 w-20 rounded bg-ink-200 dark:bg-ink-700 animate-pulse"></div>
                <div class="h-3 w-16 rounded bg-ink-200 dark:bg-ink-700 animate-pulse ml-auto"></div>
                <div class="h-3 w-14 rounded bg-ink-200 dark:bg-ink-700 animate-pulse"></div>
            </div>

            <!-- Rows -->
            <div class="divide-y divide-ink-50 dark:divide-sage-300/5">
                <div v-for="i in 7" :key="i"
                    class="flex items-center gap-4 px-5 py-3.5"
                    :style="{ opacity: 1 - (i - 1) * 0.1 }">
                    <div class="h-4 rounded flex-1 bg-ink-100 dark:bg-ink-800 animate-pulse"></div>
                    <div class="h-4 w-28 rounded bg-ink-100 dark:bg-ink-800 animate-pulse"></div>
                    <div class="h-4 w-20 rounded bg-ink-100 dark:bg-ink-800 animate-pulse"></div>
                    <div class="h-5 w-16 rounded-full bg-ink-100 dark:bg-ink-800 animate-pulse"></div>
                    <div class="h-5 w-14 rounded-full bg-ink-100 dark:bg-ink-800 animate-pulse"></div>
                </div>
            </div>
        </div>

    </div>
</template>
