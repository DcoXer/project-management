<template>
    <AppLayout>
        <div class="space-y-6">

            <div>
                <h1 class="text-2xl font-bold text-ink-900 dark:text-sage-200 tracking-tight">Dashboard</h1>
                <p class="text-sm text-ink-500 dark:text-sage-500 mt-1">Selamat datang, {{ $page.props.auth.user.name }}</p>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div v-for="stat in stats_list" :key="stat.label" class="bg-white dark:bg-ink-800 rounded-xl border border-ink-900/8 dark:border-sage-300/8 p-5 shadow-sm">
                    <p class="text-xs font-semibold text-ink-400 dark:text-sage-500 uppercase tracking-widest">{{ stat.label }}</p>
                    <p class="text-3xl font-bold mt-1" :class="stat.accent ? 'text-ink-900 dark:text-sage-300' : 'text-ink-900 dark:text-sage-200'">{{ stat.value }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- Recent Projects -->
                <div class="bg-white dark:bg-ink-800 rounded-xl border border-ink-900/8 dark:border-sage-300/8 shadow-sm">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-ink-100 dark:border-sage-300/8">
                        <h2 class="font-semibold text-ink-900 dark:text-sage-200">Project Terbaru</h2>
                        <Link href="/projects" class="text-sm text-sage-600 dark:text-sage-400 hover:text-sage-700 dark:hover:text-sage-300 font-medium transition">Lihat semua</Link>
                    </div>
                    <div class="divide-y divide-ink-50 dark:divide-sage-300/5">
                        <div v-if="!recentProjects.length" class="px-5 py-8 text-center text-sm text-ink-300 dark:text-sage-600">Belum ada project.</div>
                        <Link v-for="project in recentProjects" :key="project.id" :href="`/projects/${project.id}`" class="block px-5 py-4 hover:bg-sage-50 dark:hover:bg-sage-300/4 transition">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-ink-900 dark:text-sage-200 truncate">{{ project.name }}</p>
                                    <p class="text-xs text-ink-400 dark:text-sage-500 mt-0.5">oleh {{ project.creator.name }}</p>
                                </div>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <span :class="priorityClass(project.priority)" class="text-xs px-2 py-0.5 rounded-full font-medium">{{ project.priority }}</span>
                                    <span :class="statusClass(project.status)" class="text-xs px-2 py-0.5 rounded-full font-medium">{{ statusLabel(project.status) }}</span>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="flex justify-between text-xs text-ink-400 dark:text-sage-500 mb-1">
                                    <span>{{ project.tasks_done_count }}/{{ project.tasks_count }} task</span>
                                    <span>{{ project.progress }}%</span>
                                </div>
                                <div class="h-1.5 bg-ink-100 dark:bg-ink-700 rounded-full overflow-hidden">
                                    <div class="h-full bg-ink-900 dark:bg-sage-300 rounded-full transition-all" :style="{ width: project.progress + '%' }"></div>
                                </div>
                            </div>
                        </Link>
                    </div>
                </div>

                <!-- My Tasks -->
                <div class="bg-white dark:bg-ink-800 rounded-xl border border-ink-900/8 dark:border-sage-300/8 shadow-sm">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-ink-100 dark:border-sage-300/8">
                        <h2 class="font-semibold text-ink-900 dark:text-sage-200">Task Saya</h2>
                        <Link href="/tasks" class="text-sm text-sage-600 dark:text-sage-400 hover:text-sage-700 dark:hover:text-sage-300 font-medium transition">Lihat semua</Link>
                    </div>
                    <div class="divide-y divide-ink-50 dark:divide-sage-300/5">
                        <div v-if="!myTasks.length" class="px-5 py-8 text-center text-sm text-ink-300 dark:text-sage-600">Tidak ada task yang di-assign ke kamu.</div>
                        <Link v-for="task in myTasks" :key="task.id" :href="`/tasks/${task.id}`" class="block px-5 py-4 hover:bg-sage-50 dark:hover:bg-sage-300/4 transition">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-ink-900 dark:text-sage-200 truncate">{{ task.title }}</p>
                                    <p class="text-xs text-ink-400 dark:text-sage-500 mt-0.5">{{ task.project.name }}</p>
                                </div>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <span :class="priorityClass(task.priority)" class="text-xs px-2 py-0.5 rounded-full font-medium">{{ task.priority }}</span>
                                    <span :class="taskStatusClass(task.status)" class="text-xs px-2 py-0.5 rounded-full font-medium">{{ taskStatusLabel(task.status) }}</span>
                                </div>
                            </div>
                            <p v-if="task.due_date" class="text-xs text-ink-400 dark:text-sage-500 mt-1.5">Due: {{ formatDate(task.due_date) }}</p>
                        </Link>
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({ stats: Object, recentProjects: Array, myTasks: Array })
const page = usePage()

const stats_list = computed(() => [
    { label: 'Total Projects', value: props.stats.total_projects },
    { label: 'Total Tasks',    value: props.stats.total_tasks },
    { label: 'Total Users',    value: props.stats.total_users },
    { label: 'Task Saya',      value: props.stats.my_tasks, accent: true },
])

const statusLabel   = s => ({ planning:'Planning', in_progress:'In Progress', on_hold:'On Hold', completed:'Completed' }[s] ?? s)
const statusClass   = s => ({ planning:'bg-ink-100 text-ink-600 dark:bg-ink-700/50 dark:text-ink-300', in_progress:'bg-sage-100 text-sage-700 dark:bg-sage-300/15 dark:text-sage-300', on_hold:'bg-amber-100 text-amber-700 dark:bg-amber-300/15 dark:text-amber-300', completed:'bg-emerald-100 text-emerald-700 dark:bg-emerald-300/15 dark:text-emerald-300' }[s] ?? 'bg-ink-100 text-ink-500')
const taskStatusLabel = s => ({ todo:'Todo', in_progress:'In Progress', review:'Review', done:'Done' }[s] ?? s)
const taskStatusClass = s => ({ todo:'bg-ink-100 text-ink-600 dark:bg-ink-700/50 dark:text-ink-300', in_progress:'bg-sage-100 text-sage-700 dark:bg-sage-300/15 dark:text-sage-300', review:'bg-violet-100 text-violet-700 dark:bg-violet-300/15 dark:text-violet-300', done:'bg-emerald-100 text-emerald-700 dark:bg-emerald-300/15 dark:text-emerald-300' }[s] ?? 'bg-ink-100 text-ink-500')
const priorityClass = p => ({ low:'bg-sage-100 text-sage-700 dark:bg-sage-300/15 dark:text-sage-300', medium:'bg-amber-100 text-amber-700 dark:bg-amber-300/15 dark:text-amber-300', high:'bg-red-100 text-red-700 dark:bg-red-300/15 dark:text-red-300' }[p] ?? 'bg-ink-100 text-ink-500')
const formatDate    = d => d ? new Date(d).toLocaleDateString('id-ID', { day:'numeric', month:'short', year:'numeric' }) : '-'
</script>
