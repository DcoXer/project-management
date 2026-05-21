<template>
    <AppLayout>

        <!-- ── Page header ─────────────────────────────────────────── -->
        <div class="mb-7">
            <Link href="/projects" class="inline-flex items-center gap-1.5 text-xs font-medium text-ink-400 dark:text-white hover:text-ink-700 dark:hover:text-white transition mb-4">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg>
                Kembali ke Projects
            </Link>

            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="min-w-0">
                    <h1 class="text-2xl sm:text-3xl font-bold text-ink-900 dark:text-white tracking-tight leading-tight">{{ project.name }}</h1>
                    <p class="text-sm text-ink-400 dark:text-white mt-1.5">Dibuat oleh {{ project.creator.name }}</p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <PriorityBadge :priority="project.priority" />
                    <ProjectStatusBadge :status="project.status" />
                    <button v-if="is_pm && project.status === 'planning'"
                        @click="startProject"
                        :disabled="startForm.processing"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-emerald-600 text-white hover:bg-emerald-700 transition disabled:opacity-60">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z" />
                        </svg>
                        Mulai Project
                    </button>
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
                            <span class="text-xs font-semibold text-ink-400 dark:text-white uppercase tracking-wide">Progress</span>
                            <span class="text-sm font-bold text-ink-900 dark:text-white">{{ project.progress }}%</span>
                        </div>
                        <div class="h-1.5 bg-ink-100 dark:bg-ink-800 rounded-full overflow-hidden">
                            <div class="h-full bg-ink-900 dark:bg-sage-300 rounded-full transition-all duration-500" :style="{ width: project.progress + '%' }"></div>
                        </div>
                    </div>
                </div>
                <!-- Tasks count -->
                <div class="bg-white dark:bg-ink-900 border border-ink-900/6 dark:border-white/5 rounded-2xl px-4 py-3 text-center">
                    <p class="text-xl font-bold text-ink-900 dark:text-white leading-none">{{ project.tasks_done_count }}<span class="text-ink-300 dark:text-white font-normal">/{{ project.tasks_count }}</span></p>
                    <p class="text-[11px] text-ink-400 dark:text-white mt-1 uppercase tracking-wide font-medium">Tasks</p>
                </div>
                <!-- Members count -->
                <div class="bg-white dark:bg-ink-900 border border-ink-900/6 dark:border-white/5 rounded-2xl px-4 py-3 text-center">
                    <p class="text-xl font-bold text-ink-900 dark:text-white leading-none">{{ project.members?.length ?? 0 }}</p>
                    <p class="text-[11px] text-ink-400 dark:text-white mt-1 uppercase tracking-wide font-medium">Members</p>
                </div>
                <!-- Dates -->
                <div v-if="project.start_date || project.end_date" class="bg-white dark:bg-ink-900 border border-ink-900/6 dark:border-white/5 rounded-2xl px-4 py-3">
                    <p class="text-[11px] text-ink-400 dark:text-white uppercase tracking-wide font-medium mb-1">Timeline</p>
                    <p class="text-xs font-medium text-ink-700 dark:text-white">{{ formatDate(project.start_date) }} → {{ formatDate(project.end_date) }}</p>
                </div>
            </div>
        </div>

        <!-- ── Content grid ────────────────────────────────────────── -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Task list -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-ink-900 rounded-2xl border border-ink-900/6 dark:border-white/5 overflow-hidden">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-ink-50 dark:border-white/4">
                        <h2 class="text-xs font-semibold text-ink-400 dark:text-white uppercase tracking-widest">Tasks</h2>
                        <span class="text-xs text-ink-300 dark:text-white font-medium">{{ project.tasks?.length ?? 0 }} task</span>
                    </div>
                    <div class="divide-y divide-ink-50 dark:divide-white/4">
                        <div v-if="!project.tasks?.length" class="px-6 py-10 text-center text-sm text-ink-300 dark:text-white">Belum ada task.</div>
                        <Link v-for="task in project.tasks" :key="task.id" :href="`/tasks/${task.id}`"
                            class="flex items-center gap-4 px-6 py-3.5 hover:bg-ink-50/60 dark:hover:bg-white/2 transition group">
                            <!-- Status dot -->
                            <div class="w-2 h-2 rounded-full shrink-0" :class="statusDotClass(task.status)"></div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-ink-900 dark:text-white truncate group-hover:text-ink-700 dark:group-hover:text-white transition">{{ task.title }}</p>
                                <p v-if="task.assignee" class="text-xs text-ink-400 dark:text-white mt-0.5">{{ task.assignee.name }}</p>
                            </div>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <span v-if="task.deadline_status === 'overdue'" class="text-[10px] bg-red-100 dark:bg-red-400/15 text-red-600 dark:text-red-400 px-1.5 py-0.5 rounded font-semibold">Overdue</span>
                                <span v-else-if="task.deadline_status === 'warning'" class="text-[10px] bg-amber-100 dark:bg-amber-400/15 text-amber-600 dark:text-amber-400 px-1.5 py-0.5 rounded font-semibold">Due soon</span>
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
                    <h2 class="text-xs font-semibold text-ink-400 dark:text-white uppercase tracking-widest mb-3">Deskripsi</h2>
                    <p class="text-sm text-ink-600 dark:text-white leading-relaxed">{{ project.description }}</p>
                </div>

                <!-- Members -->
                <div class="bg-white dark:bg-ink-900 rounded-2xl border border-ink-900/6 dark:border-white/5 p-5">
                    <h2 class="text-xs font-semibold text-ink-400 dark:text-white uppercase tracking-widest mb-4">Tim</h2>
                    <div v-if="!project.members?.length" class="text-sm text-ink-300 dark:text-white">Belum ada member.</div>
                    <div class="space-y-3">
                        <div v-for="member in project.members" :key="member.id" class="flex items-center gap-3">
                            <UserAvatar :name="member.name" size="lg" class="bg-ink-100 dark:bg-ink-800 text-ink-600 dark:text-white" />
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-ink-800 dark:text-white truncate">{{ member.name }}</p>
                                <p class="text-xs text-ink-400 dark:text-white">
                                    {{ member.pivot?.role === 'manager' ? 'Project Manager' : specializationLabel(member.pivot?.specialization) }}
                                </p>
                            </div>
                            <span v-if="member.pivot?.role === 'manager'" class="text-[10px] font-semibold text-sage-600 dark:text-white bg-sage-100 dark:bg-sage-300/10 px-1.5 py-0.5 rounded-md shrink-0">PM</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- ── Workload Tim (PM & Admin only) ─────────────────────── -->
        <div v-if="isPmOrAdmin" class="mt-8">
            <div class="flex items-center gap-3 mb-4">
                <h2 class="text-xs font-semibold text-ink-400 dark:text-white uppercase tracking-widest">Workload Tim</h2>
                <div class="flex-1 h-px bg-ink-100 dark:bg-white/5"></div>
                <span class="text-xs text-ink-300 dark:text-white font-medium">{{ memberWorkload.length }} member</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">

                <!-- Member workload card -->
                <div v-for="member in memberWorkload" :key="member.id"
                    class="bg-white dark:bg-ink-900 rounded-2xl border border-ink-900/6 dark:border-white/5 p-5 flex flex-col gap-4">

                    <!-- Member header -->
                    <div class="flex items-center gap-3">
                        <UserAvatar :name="member.name" size="lg" />
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <p class="text-sm font-semibold text-ink-800 dark:text-white truncate">{{ member.name }}</p>
                                <span v-if="member.pivot?.role === 'manager'" class="text-[10px] font-semibold text-sage-600 dark:text-white bg-sage-100 dark:bg-sage-300/10 px-1.5 py-0.5 rounded-md shrink-0">PM</span>
                            </div>
                            <p class="text-xs text-ink-400 dark:text-white mt-0.5">
                                {{ member.pivot?.role === 'manager' ? 'Project Manager' : specializationLabel(member.pivot?.specialization) }}
                            </p>
                        </div>
                        <!-- Task counter -->
                        <div class="text-right shrink-0">
                            <p class="text-sm font-bold text-ink-900 dark:text-white leading-none">
                                {{ member.done_count }}<span class="text-ink-300 dark:text-white font-normal text-xs">/{{ member.task_count }}</span>
                            </p>
                            <p class="text-[10px] text-ink-400 dark:text-white mt-0.5">selesai</p>
                        </div>
                    </div>

                    <!-- Progress bar -->
                    <div class="h-1.5 bg-ink-100 dark:bg-ink-800 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-500"
                            :class="member.task_count === 0 ? 'bg-ink-200 dark:bg-ink-700' : 'bg-ink-900 dark:bg-sage-300'"
                            :style="{ width: member.task_count > 0 ? (member.done_count / member.task_count * 100) + '%' : '0%' }">
                        </div>
                    </div>

                    <!-- Task list -->
                    <div class="space-y-1 flex-1">
                        <p v-if="!member.tasks.length" class="text-xs text-ink-300 dark:text-white italic py-1">Belum ada task.</p>
                        <Link v-for="task in member.tasks" :key="task.id" :href="`/tasks/${task.id}`"
                            class="flex items-center gap-2.5 px-2.5 py-2 rounded-xl hover:bg-ink-50 dark:hover:bg-white/3 transition group -mx-1">
                            <div class="w-1.5 h-1.5 rounded-full shrink-0" :class="statusDotClass(task.status)"></div>
                            <span class="text-xs text-ink-700 dark:text-white truncate flex-1 group-hover:text-ink-900 dark:group-hover:text-white transition">{{ task.title }}</span>
                            <TaskStatusBadge :status="task.status" sm />
                        </Link>
                    </div>
                </div>

                <!-- Unassigned tasks card -->
                <div v-if="unassignedTasks.length"
                    class="bg-white dark:bg-ink-900 rounded-2xl border border-amber-200/60 dark:border-amber-400/10 p-5 flex flex-col gap-4">

                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-amber-50 dark:bg-amber-400/10 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-amber-500 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-ink-800 dark:text-white">Belum Ditugaskan</p>
                            <p class="text-xs text-amber-600 dark:text-amber-400 mt-0.5">{{ unassignedTasks.length }} task perlu assignee</p>
                        </div>
                    </div>

                    <div class="h-1.5 bg-amber-100 dark:bg-amber-400/10 rounded-full overflow-hidden">
                        <div class="h-full w-full bg-amber-300 dark:bg-amber-400/30 rounded-full"></div>
                    </div>

                    <div class="space-y-1 flex-1">
                        <Link v-for="task in unassignedTasks" :key="task.id" :href="`/tasks/${task.id}`"
                            class="flex items-center gap-2.5 px-2.5 py-2 rounded-xl hover:bg-amber-50 dark:hover:bg-amber-400/5 transition group -mx-1">
                            <div class="w-1.5 h-1.5 rounded-full shrink-0 bg-amber-300 dark:bg-amber-400"></div>
                            <span class="text-xs text-ink-700 dark:text-white truncate flex-1 group-hover:text-ink-900 dark:group-hover:text-white transition">{{ task.title }}</span>
                            <TaskStatusBadge :status="task.status" sm />
                        </Link>
                    </div>
                </div>

            </div>
        </div>

    </AppLayout>
</template>

<script setup>
import { computed } from 'vue'
import { usePage, Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { formatDate, specializationLabel } from '@/composables/useFormatters'
import ProjectStatusBadge from '@/Components/ProjectStatusBadge.vue'
import TaskStatusBadge from '@/Components/TaskStatusBadge.vue'
import PriorityBadge from '@/Components/PriorityBadge.vue'
import UserAvatar from '@/Components/UserAvatar.vue'

const props = defineProps({ project: Object, is_pm: Boolean })

const auth = usePage().props.auth
const isPmOrAdmin = auth.user.is_pm || auth.user.role === 'admin'

const statusDotClass = (status) => ({
    'bg-ink-200 dark:bg-ink-700': status === 'todo',
    'bg-sage-400 dark:bg-sage-400': status === 'in_progress',
    'bg-violet-400': status === 'review',
    'bg-emerald-400': status === 'done',
})

// Hitung tasks per member dari data yang sudah ada
const memberWorkload = computed(() => {
    const tasks = props.project.tasks ?? []
    return (props.project.members ?? []).map(member => {
        const memberTasks = tasks.filter(t => t.assignee?.id === member.id)
        return {
            ...member,
            tasks: memberTasks,
            task_count: memberTasks.length,
            done_count: memberTasks.filter(t => t.status === 'done').length,
        }
    })
})

// Tasks yang belum punya assignee
const unassignedTasks = computed(() =>
    (props.project.tasks ?? []).filter(t => !t.assignee)
)

const startForm = useForm({})
const startProject = () => {
    startForm.post(`/projects/${props.project.id}/start`)
}
</script>
