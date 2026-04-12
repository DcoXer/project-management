<template>
    <AppLayout>
        <div class="space-y-6">

            <div>
                <h1 class="text-2xl font-bold text-ink-900 dark:text-sage-200 tracking-tight">Dashboard</h1>
                <p class="text-sm text-ink-500 dark:text-sage-500 mt-1">Selamat datang, {{ $page.props.auth.user.name }}</p>
            </div>

            <!-- Stats row 1 -->
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-white dark:bg-ink-800 rounded-xl border border-ink-900/8 dark:border-sage-300/8 p-5 shadow-sm">
                    <p class="text-xs font-semibold text-ink-400 dark:text-sage-500 uppercase tracking-widest">Projects Saya</p>
                    <p class="text-3xl font-bold mt-1 text-ink-900 dark:text-sage-200">{{ stats.my_projects }}</p>
                </div>
                <div class="bg-white dark:bg-ink-800 rounded-xl border border-ink-900/8 dark:border-sage-300/8 p-5 shadow-sm">
                    <p class="text-xs font-semibold text-ink-400 dark:text-sage-500 uppercase tracking-widest">Total Task</p>
                    <p class="text-3xl font-bold mt-1 text-ink-900 dark:text-sage-200">{{ stats.my_tasks }}</p>
                </div>
            </div>

            <!-- Stats row 2: task by status -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-ink-800 rounded-xl border border-ink-900/8 dark:border-sage-300/8 p-5 shadow-sm">
                    <p class="text-xs font-semibold text-ink-400 dark:text-sage-500 uppercase tracking-widest">Todo</p>
                    <p class="text-3xl font-bold mt-1 text-ink-400 dark:text-ink-400">{{ stats.todo }}</p>
                </div>
                <div class="bg-white dark:bg-ink-800 rounded-xl border border-ink-900/8 dark:border-sage-300/8 p-5 shadow-sm">
                    <p class="text-xs font-semibold text-ink-400 dark:text-sage-500 uppercase tracking-widest">In Progress</p>
                    <p class="text-3xl font-bold mt-1 text-sage-600 dark:text-sage-400">{{ stats.in_progress }}</p>
                </div>
                <div class="bg-white dark:bg-ink-800 rounded-xl border border-ink-900/8 dark:border-sage-300/8 p-5 shadow-sm">
                    <p class="text-xs font-semibold text-ink-400 dark:text-sage-500 uppercase tracking-widest">Review</p>
                    <p class="text-3xl font-bold mt-1 text-violet-600 dark:text-violet-400">{{ stats.review }}</p>
                </div>
                <div class="bg-white dark:bg-ink-800 rounded-xl border border-ink-900/8 dark:border-sage-300/8 p-5 shadow-sm">
                    <p class="text-xs font-semibold text-ink-400 dark:text-sage-500 uppercase tracking-widest">Done</p>
                    <p class="text-3xl font-bold mt-1 text-emerald-600 dark:text-emerald-400">{{ stats.done }}</p>
                </div>
            </div>

            <!-- Charts -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-ink-800 rounded-xl border border-ink-900/8 dark:border-sage-300/8 p-5 shadow-sm">
                    <h2 class="font-semibold text-ink-900 dark:text-sage-200 mb-4">Task by Status</h2>
                    <div class="flex items-center justify-center" style="height: 220px">
                        <canvas ref="statusChartRef"></canvas>
                    </div>
                </div>
                <div class="bg-white dark:bg-ink-800 rounded-xl border border-ink-900/8 dark:border-sage-300/8 p-5 shadow-sm">
                    <h2 class="font-semibold text-ink-900 dark:text-sage-200 mb-4">Task by Priority</h2>
                    <div style="height: 220px">
                        <canvas ref="priorityChartRef"></canvas>
                    </div>
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
                                    <PriorityBadge :priority="project.priority" sm />
                                    <ProjectStatusBadge :status="project.status" sm />
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
                        <div v-if="!myTasks.length" class="px-5 py-8 text-center text-sm text-ink-300 dark:text-sage-600">Tidak ada task aktif.</div>
                        <Link v-for="task in myTasks" :key="task.id" :href="`/tasks/${task.id}`" class="block px-5 py-4 hover:bg-sage-50 dark:hover:bg-sage-300/4 transition">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-ink-900 dark:text-sage-200 truncate">{{ task.title }}</p>
                                    <p class="text-xs text-ink-400 dark:text-sage-500 mt-0.5">{{ task.project.name }}</p>
                                </div>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <PriorityBadge :priority="task.priority" sm />
                                    <TaskStatusBadge :status="task.status" sm />
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
import { Link } from '@inertiajs/vue3'
import { onMounted, ref } from 'vue'
import { Chart, DoughnutController, BarController, ArcElement, BarElement, CategoryScale, LinearScale, Tooltip, Legend } from 'chart.js'
import { formatDate } from '@/composables/useFormatters'
import ProjectStatusBadge from '@/Components/ProjectStatusBadge.vue'
import TaskStatusBadge from '@/Components/TaskStatusBadge.vue'
import PriorityBadge from '@/Components/PriorityBadge.vue'

Chart.register(DoughnutController, BarController, ArcElement, BarElement, CategoryScale, LinearScale, Tooltip, Legend)

const props = defineProps({
    stats: Object,
    recentProjects: Array,
    myTasks: Array,
    tasksByStatus: Object,
    tasksByPriority: Object,
})

const statusChartRef   = ref(null)
const priorityChartRef = ref(null)

onMounted(() => {
    const isDark    = document.documentElement.classList.contains('dark')
    const textColor = isDark ? '#94a3b8' : '#64748b'
    const gridColor = isDark ? 'rgba(148,163,184,0.1)' : 'rgba(0,0,0,0.06)'

    new Chart(statusChartRef.value, {
        type: 'doughnut',
        data: {
            labels: ['Todo', 'In Progress', 'Review', 'Done'],
            datasets: [{
                data: [
                    props.tasksByStatus?.todo        ?? 0,
                    props.tasksByStatus?.in_progress ?? 0,
                    props.tasksByStatus?.review      ?? 0,
                    props.tasksByStatus?.done        ?? 0,
                ],
                backgroundColor: ['#94a3b8', '#6ee7b7', '#c4b5fd', '#34d399'],
                borderWidth: 0,
                hoverOffset: 6,
            }],
        },
        options: {
            cutout: '70%',
            plugins: {
                legend: { position: 'bottom', labels: { color: textColor, padding: 16, font: { size: 12 } } },
                tooltip: { callbacks: { label: ctx => ` ${ctx.label}: ${ctx.raw} task` } },
            },
        },
    })

    new Chart(priorityChartRef.value, {
        type: 'bar',
        data: {
            labels: ['Low', 'Medium', 'High'],
            datasets: [{
                label: 'Jumlah Task',
                data: [
                    props.tasksByPriority?.low    ?? 0,
                    props.tasksByPriority?.medium ?? 0,
                    props.tasksByPriority?.high   ?? 0,
                ],
                backgroundColor: ['#6ee7b7', '#fcd34d', '#f87171'],
                borderRadius: 8,
                borderSkipped: false,
            }],
        },
        options: {
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: ctx => ` ${ctx.raw} task` } },
            },
            scales: {
                x: { grid: { display: false }, ticks: { color: textColor } },
                y: { grid: { color: gridColor }, ticks: { color: textColor, precision: 0 }, beginAtZero: true },
            },
        },
    })
})

</script>
