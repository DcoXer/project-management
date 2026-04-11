<template>
    <AppLayout>
        <div class="space-y-6">

            <div>
                <Link href="/projects" class="text-sm text-ink-400 dark:text-sage-500 hover:text-ink-700 dark:hover:text-sage-300 transition">&larr; Kembali ke Projects</Link>
                <div class="flex flex-wrap items-start justify-between gap-3 mt-3">
                    <div>
                        <h1 class="text-2xl font-bold text-ink-900 dark:text-sage-200 tracking-tight">{{ project.name }}</h1>
                        <p class="text-sm text-ink-400 dark:text-sage-500 mt-1">Dibuat oleh {{ project.creator.name }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span :class="priorityClass(project.priority)" class="text-xs px-3 py-1 rounded-full font-medium">{{ project.priority }}</span>
                        <span :class="statusClass(project.status)" class="text-xs px-3 py-1 rounded-full font-medium">{{ statusLabel(project.status) }}</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Left -->
                <div class="lg:col-span-2 space-y-5">

                    <div v-if="project.description" class="bg-white dark:bg-ink-800 rounded-xl border border-ink-900/8 dark:border-sage-300/8 p-5 shadow-sm">
                        <h2 class="text-sm font-semibold text-ink-700 dark:text-sage-300 mb-2">Deskripsi</h2>
                        <p class="text-sm text-ink-600 dark:text-sage-400 leading-relaxed">{{ project.description }}</p>
                    </div>

                    <!-- Progress -->
                    <div class="bg-white dark:bg-ink-800 rounded-xl border border-ink-900/8 dark:border-sage-300/8 p-5 shadow-sm">
                        <div class="flex justify-between text-sm mb-2">
                            <span class="font-semibold text-ink-700 dark:text-sage-300">Progress</span>
                            <span class="text-ink-400 dark:text-sage-500">{{ project.tasks_done_count }}/{{ project.tasks_count }} task — {{ project.progress }}%</span>
                        </div>
                        <div class="h-2 bg-ink-100 dark:bg-ink-700 rounded-full overflow-hidden">
                            <div class="h-full bg-ink-900 dark:bg-sage-300 rounded-full transition-all" :style="{ width: project.progress + '%' }"></div>
                        </div>
                    </div>

                    <!-- Tasks -->
                    <div class="bg-white dark:bg-ink-800 rounded-xl border border-ink-900/8 dark:border-sage-300/8 shadow-sm">
                        <div class="flex items-center justify-between px-5 py-4 border-b border-ink-100 dark:border-sage-300/8">
                            <h2 class="font-semibold text-ink-900 dark:text-sage-200">Tasks</h2>
                            <span class="text-xs text-ink-300 dark:text-sage-600">{{ project.tasks?.length ?? 0 }} task</span>
                        </div>
                        <div class="divide-y divide-ink-50 dark:divide-sage-300/5">
                            <div v-if="!project.tasks?.length" class="px-5 py-8 text-center text-sm text-ink-300 dark:text-sage-600">Belum ada task.</div>
                            <Link v-for="task in project.tasks" :key="task.id" :href="`/tasks/${task.id}`" class="flex items-center justify-between px-5 py-3.5 hover:bg-sage-50 dark:hover:bg-sage-300/4 transition">
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-ink-900 dark:text-sage-200 truncate">{{ task.title }}</p>
                                    <p v-if="task.assignee" class="text-xs text-ink-400 dark:text-sage-500 mt-0.5">{{ task.assignee.name }}</p>
                                </div>
                                <div class="flex items-center gap-2 shrink-0 ml-3">
                                    <span :class="priorityClass(task.priority)" class="text-xs px-2 py-0.5 rounded-full font-medium">{{ task.priority }}</span>
                                    <span :class="taskStatusClass(task.status)" class="text-xs px-2 py-0.5 rounded-full font-medium">{{ taskStatusLabel(task.status) }}</span>
                                </div>
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Right -->
                <div class="space-y-5">
                    <div class="bg-white dark:bg-ink-800 rounded-xl border border-ink-900/8 dark:border-sage-300/8 p-5 shadow-sm space-y-3">
                        <h2 class="text-sm font-semibold text-ink-700 dark:text-sage-300">Detail</h2>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-ink-400 dark:text-sage-500">Mulai</span>
                                <span class="text-ink-800 dark:text-sage-200">{{ formatDate(project.start_date) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-ink-400 dark:text-sage-500">Selesai</span>
                                <span class="text-ink-800 dark:text-sage-200">{{ formatDate(project.end_date) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-ink-800 rounded-xl border border-ink-900/8 dark:border-sage-300/8 p-5 shadow-sm">
                        <h2 class="text-sm font-semibold text-ink-700 dark:text-sage-300 mb-3">Members</h2>
                        <div v-if="!project.members?.length" class="text-sm text-ink-300 dark:text-sage-600">Belum ada member.</div>
                        <div class="space-y-2.5">
                            <div v-for="member in project.members" :key="member.id" class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-sage-200 dark:bg-sage-300/20 text-sage-700 dark:text-sage-300 text-sm font-semibold flex items-center justify-center shrink-0">{{ member.name.charAt(0).toUpperCase() }}</div>
                                <div>
                                    <p class="text-sm font-medium text-ink-800 dark:text-sage-200">{{ member.name }}</p>
                                    <p class="text-xs text-ink-400 dark:text-sage-500">{{ member.pivot?.role ?? '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link } from '@inertiajs/vue3'

defineProps({ project: Object })

const statusLabel     = s => ({ planning:'Planning', in_progress:'In Progress', on_hold:'On Hold', completed:'Completed' }[s] ?? s)
const statusClass     = s => ({ planning:'bg-ink-100 text-ink-600 dark:bg-ink-700/50 dark:text-ink-300', in_progress:'bg-sage-100 text-sage-700 dark:bg-sage-300/15 dark:text-sage-300', on_hold:'bg-amber-100 text-amber-700 dark:bg-amber-300/15 dark:text-amber-300', completed:'bg-emerald-100 text-emerald-700 dark:bg-emerald-300/15 dark:text-emerald-300' }[s] ?? 'bg-ink-100 text-ink-500')
const taskStatusLabel = s => ({ todo:'Todo', in_progress:'In Progress', review:'Review', done:'Done' }[s] ?? s)
const taskStatusClass = s => ({ todo:'bg-ink-100 text-ink-600 dark:bg-ink-700/50 dark:text-ink-300', in_progress:'bg-sage-100 text-sage-700 dark:bg-sage-300/15 dark:text-sage-300', review:'bg-violet-100 text-violet-700 dark:bg-violet-300/15 dark:text-violet-300', done:'bg-emerald-100 text-emerald-700 dark:bg-emerald-300/15 dark:text-emerald-300' }[s] ?? 'bg-ink-100 text-ink-500')
const priorityClass   = p => ({ low:'bg-sage-100 text-sage-700 dark:bg-sage-300/15 dark:text-sage-300', medium:'bg-amber-100 text-amber-700 dark:bg-amber-300/15 dark:text-amber-300', high:'bg-red-100 text-red-700 dark:bg-red-300/15 dark:text-red-300' }[p] ?? 'bg-ink-100 text-ink-500')
const formatDate      = d => d ? new Date(d).toLocaleDateString('id-ID', { day:'numeric', month:'short', year:'numeric' }) : '-'
</script>
