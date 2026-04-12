<template>
    <AppLayout>

        <!-- ── Page header ─────────────────────────────────────────── -->
        <div class="mb-7">
            <Link href="/projects" class="inline-flex items-center gap-1.5 text-xs font-medium text-ink-400 dark:text-sage-600 hover:text-ink-700 dark:hover:text-sage-300 transition mb-4">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg>
                Kembali ke Projects
            </Link>

            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="min-w-0">
                    <h1 class="text-2xl sm:text-3xl font-bold text-ink-900 dark:text-sage-100 tracking-tight leading-tight">{{ project.name }}</h1>
                    <p class="text-sm text-ink-400 dark:text-sage-600 mt-1.5">Dibuat oleh {{ project.creator.name }}</p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <PriorityBadge :priority="project.priority" />
                    <ProjectStatusBadge :status="project.status" />
                    <a :href="`/projects/${project.id}/export-pdf`" target="_blank"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-ink-900 dark:bg-sage-300 text-white dark:text-ink-900 hover:bg-ink-700 dark:hover:bg-sage-200 transition">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                        Export PDF
                    </a>
                </div>
            </div>

            <!-- Stats strip -->
            <div class="flex flex-wrap items-center gap-3 mt-5">
                <!-- Progress bar + % -->
                <div class="flex items-center gap-3 bg-white dark:bg-ink-900 border border-ink-900/6 dark:border-white/5 rounded-2xl px-4 py-3 flex-1 min-w-[200px]">
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-xs font-semibold text-ink-400 dark:text-sage-600 uppercase tracking-wide">Progress</span>
                            <span class="text-sm font-bold text-ink-900 dark:text-sage-200">{{ project.progress }}%</span>
                        </div>
                        <div class="h-1.5 bg-ink-100 dark:bg-ink-800 rounded-full overflow-hidden">
                            <div class="h-full bg-ink-900 dark:bg-sage-300 rounded-full transition-all duration-500" :style="{ width: project.progress + '%' }"></div>
                        </div>
                    </div>
                </div>
                <!-- Tasks count -->
                <div class="bg-white dark:bg-ink-900 border border-ink-900/6 dark:border-white/5 rounded-2xl px-4 py-3 text-center">
                    <p class="text-xl font-bold text-ink-900 dark:text-sage-200 leading-none">{{ project.tasks_done_count }}<span class="text-ink-300 dark:text-sage-700 font-normal">/{{ project.tasks_count }}</span></p>
                    <p class="text-[11px] text-ink-400 dark:text-sage-600 mt-1 uppercase tracking-wide font-medium">Tasks</p>
                </div>
                <!-- Members count -->
                <div class="bg-white dark:bg-ink-900 border border-ink-900/6 dark:border-white/5 rounded-2xl px-4 py-3 text-center">
                    <p class="text-xl font-bold text-ink-900 dark:text-sage-200 leading-none">{{ project.members?.length ?? 0 }}</p>
                    <p class="text-[11px] text-ink-400 dark:text-sage-600 mt-1 uppercase tracking-wide font-medium">Members</p>
                </div>
                <!-- Dates -->
                <div v-if="project.start_date || project.end_date" class="bg-white dark:bg-ink-900 border border-ink-900/6 dark:border-white/5 rounded-2xl px-4 py-3">
                    <p class="text-[11px] text-ink-400 dark:text-sage-600 uppercase tracking-wide font-medium mb-1">Timeline</p>
                    <p class="text-xs font-medium text-ink-700 dark:text-sage-300">{{ formatDate(project.start_date) }} → {{ formatDate(project.end_date) }}</p>
                </div>
            </div>
        </div>

        <!-- ── Content grid ────────────────────────────────────────── -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Task list -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-ink-900 rounded-2xl border border-ink-900/6 dark:border-white/5 overflow-hidden">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-ink-50 dark:border-white/4">
                        <h2 class="text-xs font-semibold text-ink-400 dark:text-sage-600 uppercase tracking-widest">Tasks</h2>
                        <span class="text-xs text-ink-300 dark:text-sage-700 font-medium">{{ project.tasks?.length ?? 0 }} task</span>
                    </div>
                    <div class="divide-y divide-ink-50 dark:divide-white/4">
                        <div v-if="!project.tasks?.length" class="px-6 py-10 text-center text-sm text-ink-300 dark:text-sage-700">Belum ada task.</div>
                        <Link v-for="task in project.tasks" :key="task.id" :href="`/tasks/${task.id}`"
                            class="flex items-center gap-4 px-6 py-3.5 hover:bg-ink-50/60 dark:hover:bg-white/2 transition group">
                            <!-- Status dot -->
                            <div class="w-2 h-2 rounded-full shrink-0" :class="{
                                'bg-ink-200 dark:bg-ink-700':   task.status === 'todo',
                                'bg-sage-400 dark:bg-sage-400': task.status === 'in_progress',
                                'bg-violet-400':                task.status === 'review',
                                'bg-emerald-400':               task.status === 'done',
                            }"></div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-ink-900 dark:text-sage-200 truncate group-hover:text-ink-700 dark:group-hover:text-sage-100 transition">{{ task.title }}</p>
                                <p v-if="task.assignee" class="text-xs text-ink-400 dark:text-sage-600 mt-0.5">{{ task.assignee.name }}</p>
                            </div>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <PriorityBadge :priority="task.priority" sm />
                                <TaskStatusBadge :status="task.status" sm />
                            </div>
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Sidebar: description + members + details -->
            <div class="space-y-5">

                <!-- Description -->
                <div v-if="project.description" class="bg-white dark:bg-ink-900 rounded-2xl border border-ink-900/6 dark:border-white/5 p-5">
                    <h2 class="text-xs font-semibold text-ink-400 dark:text-sage-600 uppercase tracking-widest mb-3">Deskripsi</h2>
                    <p class="text-sm text-ink-600 dark:text-sage-400 leading-relaxed">{{ project.description }}</p>
                </div>

                <!-- Members -->
                <div class="bg-white dark:bg-ink-900 rounded-2xl border border-ink-900/6 dark:border-white/5 p-5">
                    <h2 class="text-xs font-semibold text-ink-400 dark:text-sage-600 uppercase tracking-widest mb-4">Tim</h2>
                    <div v-if="!project.members?.length" class="text-sm text-ink-300 dark:text-sage-700">Belum ada member.</div>
                    <div class="space-y-3">
                        <div v-for="member in project.members" :key="member.id" class="flex items-center gap-3">
                            <UserAvatar :name="member.name" size="lg" class="bg-ink-100 dark:bg-ink-800 text-ink-600 dark:text-sage-400" />
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-ink-800 dark:text-sage-200 truncate">{{ member.name }}</p>
                                <p class="text-xs text-ink-400 dark:text-sage-600">
                                    {{ member.pivot?.role === 'manager' ? 'Project Manager' : specializationLabel(member.pivot?.specialization) }}
                                </p>
                            </div>
                            <span v-if="member.pivot?.role === 'manager'" class="text-[10px] font-semibold text-sage-600 dark:text-sage-400 bg-sage-100 dark:bg-sage-300/10 px-1.5 py-0.5 rounded-md shrink-0">PM</span>
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
import { formatDate, specializationLabel } from '@/composables/useFormatters'
import ProjectStatusBadge from '@/Components/ProjectStatusBadge.vue'
import TaskStatusBadge from '@/Components/TaskStatusBadge.vue'
import PriorityBadge from '@/Components/PriorityBadge.vue'
import UserAvatar from '@/Components/UserAvatar.vue'

defineProps({ project: Object })
</script>
