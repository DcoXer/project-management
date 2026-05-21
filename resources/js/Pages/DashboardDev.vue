<template>
    <AppLayout>
        <div class="space-y-6">

            <!-- Header -->
            <div class="section-header">
                <h1 class="text-2xl font-bold text-ink-900 dark:text-white tracking-tight">
                    {{ greeting }}, {{ $page.props.auth.user.name }}
                </h1>
                <p class="text-sm text-ink-400 dark:text-white mt-1">{{ currentDate }}</p>
            </div>

            <!-- Stat Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div
                    v-for="(card, i) in statCards" :key="card.label"
                    class="stat-card bg-white dark:bg-ink-800 rounded-xl border border-ink-900/8 dark:border-sage-300/8 p-5 shadow-sm overflow-hidden cursor-default"
                    :style="{ '--i': i }">
                    <div class="flex items-start justify-between">
                        <div :class="['icon-pulse w-10 h-10 rounded-xl flex items-center justify-center', card.iconBg]">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" v-html="card.icon"></svg>
                        </div>
                        <svg class="w-20 h-8" viewBox="0 0 80 32" fill="none" preserveAspectRatio="none">
                            <polyline
                                class="sparkline"
                                :style="{ '--i': i }"
                                :points="card.sparkline"
                                :stroke="card.accentColor"
                                stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </div>
                    <p class="text-xs font-semibold text-ink-400 dark:text-white uppercase tracking-widest mt-4">{{ card.label }}</p>
                    <p :class="['text-3xl font-bold mt-0.5 tabular-nums', card.valueClass]">{{ displayedValues[i] }}</p>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 section-charts">

                <div class="lg:col-span-2 bg-white dark:bg-ink-800 rounded-xl border border-ink-900/8 dark:border-sage-300/8 p-5 shadow-sm">
                    <h2 class="font-semibold text-ink-900 dark:text-white mb-4">Task by Priority</h2>
                    <div style="height:220px"><canvas ref="priorityChartRef"></canvas></div>
                </div>

                <div class="bg-white dark:bg-ink-800 rounded-xl border border-ink-900/8 dark:border-sage-300/8 p-5 shadow-sm">
                    <h2 class="font-semibold text-ink-900 dark:text-white mb-1">Status Task</h2>
                    <div class="flex justify-center" style="height:160px">
                        <canvas ref="statusChartRef"></canvas>
                    </div>
                    <div class="mt-4 space-y-2.5">
                        <div v-for="(item, li) in statusLegend" :key="item.label"
                             class="legend-item flex items-center justify-between"
                             :style="{ '--i': li }">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full shrink-0" :style="{ background: item.color }"></span>
                                <span class="text-xs text-ink-500 dark:text-white">{{ item.label }}</span>
                            </div>
                            <span class="text-xs font-semibold text-ink-700 dark:text-white tabular-nums">{{ item.value }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 section-bottom">

                <div class="bg-white dark:bg-ink-800 rounded-xl border border-ink-900/8 dark:border-sage-300/8 shadow-sm">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-ink-100 dark:border-sage-300/8">
                        <h2 class="font-semibold text-ink-900 dark:text-white">Project Terbaru</h2>
                        <Link href="/projects" class="text-xs font-medium text-sage-600 dark:text-white hover:opacity-70 transition">Lihat semua →</Link>
                    </div>
                    <div class="divide-y divide-ink-50 dark:divide-sage-300/5">
                        <div v-if="!recentProjects.length" class="px-5 py-8 text-center text-sm text-ink-300 dark:text-white">Belum ada project.</div>
                        <Link v-for="project in recentProjects" :key="project.id" :href="`/projects/${project.id}`"
                              class="block px-5 py-4 hover:bg-sage-50 dark:hover:bg-sage-300/4 transition">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-ink-900 dark:text-white truncate">{{ project.name }}</p>
                                    <p class="text-xs text-ink-400 dark:text-white mt-0.5">oleh {{ project.creator.name }}</p>
                                </div>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <PriorityBadge :priority="project.priority" sm />
                                    <ProjectStatusBadge :status="project.status" sm />
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="flex justify-between text-xs text-ink-400 dark:text-white mb-1.5">
                                    <span>{{ project.tasks_done_count }}/{{ project.tasks_count }} task</span>
                                    <span class="font-medium">{{ project.progress }}%</span>
                                </div>
                                <div class="h-1.5 bg-ink-100 dark:bg-ink-700 rounded-full overflow-hidden">
                                    <div class="progress-bar h-full bg-gradient-to-r from-violet-500 to-indigo-400 rounded-full"
                                         :style="{ width: animateProgress ? project.progress + '%' : '0%' }"></div>
                                </div>
                            </div>
                        </Link>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="bg-white dark:bg-ink-800 rounded-xl border border-ink-900/8 dark:border-sage-300/8 shadow-sm">
                        <div class="flex items-center justify-between px-5 py-4 border-b border-ink-100 dark:border-sage-300/8">
                            <h2 class="font-semibold text-ink-900 dark:text-white">Task Saya</h2>
                            <Link href="/tasks" class="text-xs font-medium text-sage-600 dark:text-white hover:opacity-70 transition">Lihat semua →</Link>
                        </div>
                        <div class="divide-y divide-ink-50 dark:divide-sage-300/5">
                            <div v-if="!myTasks.length" class="px-5 py-6 text-center text-sm text-ink-300 dark:text-white">Tidak ada task aktif.</div>
                            <Link v-for="task in myTasks.slice(0, 3)" :key="task.id" :href="`/tasks/${task.id}`"
                                  class="flex items-center justify-between gap-3 px-5 py-3 hover:bg-sage-50 dark:hover:bg-sage-300/4 transition">
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-ink-900 dark:text-white truncate">{{ task.title }}</p>
                                    <p class="text-xs text-ink-400 dark:text-white">{{ task.project.name }}</p>
                                </div>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <PriorityBadge :priority="task.priority" sm />
                                    <TaskStatusBadge :status="task.status" sm />
                                </div>
                            </Link>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-ink-800 rounded-xl border border-ink-900/8 dark:border-sage-300/8 shadow-sm">
                        <div class="px-5 py-4 border-b border-ink-100 dark:border-sage-300/8">
                            <h2 class="font-semibold text-ink-900 dark:text-white">Deadline Mendekat</h2>
                        </div>
                        <div class="divide-y divide-ink-50 dark:divide-sage-300/5">
                            <div v-if="!upcomingDeadlines.length" class="px-5 py-6 text-center text-sm text-ink-300 dark:text-white">Tidak ada deadline mendekat.</div>
                            <Link v-for="task in upcomingDeadlines.slice(0, 4)" :key="task.id" :href="`/tasks/${task.id}`"
                                  class="flex items-center justify-between gap-2 px-5 py-3 hover:bg-sage-50 dark:hover:bg-sage-300/4 transition">
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-ink-900 dark:text-white truncate">{{ task.title }}</p>
                                    <p class="text-xs text-ink-400 dark:text-white">{{ task.project?.name }} · {{ formatDate(task.due_date) }}</p>
                                </div>
                                <span :class="['shrink-0 text-xs font-medium px-2 py-0.5 rounded-full', deadlineUrgency(task.due_date).class]">
                                    {{ deadlineUrgency(task.due_date).label }}
                                </span>
                            </Link>
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
import { onMounted, ref, computed, nextTick } from 'vue'
import {
    Chart, BarController, BarElement, CategoryScale, LinearScale,
    Tooltip, Legend, DoughnutController, ArcElement
} from 'chart.js'
import { formatDate } from '@/composables/useFormatters'
import ProjectStatusBadge from '@/Components/ProjectStatusBadge.vue'
import TaskStatusBadge from '@/Components/TaskStatusBadge.vue'
import PriorityBadge from '@/Components/PriorityBadge.vue'

Chart.register(BarController, BarElement, CategoryScale, LinearScale, Tooltip, Legend, DoughnutController, ArcElement)

const props = defineProps({
    stats: Object,
    recentProjects: Array,
    myTasks: Array,
    tasksByStatus: Object,
    tasksByPriority: Object,
    upcomingDeadlines: Array,
})

const priorityChartRef = ref(null)
const statusChartRef   = ref(null)
const displayedValues  = ref([0, 0, 0, 0])
const animateProgress  = ref(false)

const greeting = computed(() => {
    const h = new Date().getHours()
    if (h < 12) return 'Selamat pagi'
    if (h < 17) return 'Selamat siang'
    if (h < 20) return 'Selamat sore'
    return 'Selamat malam'
})

const currentDate = computed(() =>
    new Intl.DateTimeFormat('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }).format(new Date())
)

const SPARKLINES = [
    '5,28 15,20 25,24 35,14 45,18 55,10 65,14 75,6',
    '5,24 15,28 25,16 35,22 45,12 55,18 65,8 75,14',
    '5,20 15,26 25,18 35,24 45,14 55,20 65,10 75,16',
    '5,26 15,18 25,22 35,12 45,16 55,8 65,12 75,4',
]

const ICONS = {
    folder:    '<path stroke-linecap="round" stroke-linejoin="round" d="M20 6H12L10 4H4a2 2 0 00-2 2v12a2 2 0 002 2h16a2 2 0 002-2V8a2 2 0 00-2-2z"/>',
    clipboard: '<path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>',
    zap:       '<path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>',
    check:     '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
}

const statCards = computed(() => [
    { label: 'Projects Saya', value: props.stats?.my_projects  ?? 0, icon: ICONS.folder,    iconBg: 'bg-violet-100 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400',     valueClass: 'text-ink-900 dark:text-white',           accentColor: '#8b5cf6', sparkline: SPARKLINES[0] },
    { label: 'Total Task',    value: props.stats?.my_tasks     ?? 0, icon: ICONS.clipboard, iconBg: 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400',             valueClass: 'text-ink-900 dark:text-white',           accentColor: '#3b82f6', sparkline: SPARKLINES[1] },
    { label: 'In Progress',   value: props.stats?.in_progress  ?? 0, icon: ICONS.zap,       iconBg: 'bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400',         valueClass: 'text-amber-600 dark:text-amber-400',     accentColor: '#f59e0b', sparkline: SPARKLINES[2] },
    { label: 'Done',          value: props.stats?.done         ?? 0, icon: ICONS.check,     iconBg: 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400', valueClass: 'text-emerald-600 dark:text-emerald-400', accentColor: '#10b981', sparkline: SPARKLINES[3] },
])

const statusLegend = computed(() => [
    { label: 'Todo',        value: props.stats?.todo        ?? 0, color: '#94a3b8' },
    { label: 'In Progress', value: props.stats?.in_progress ?? 0, color: '#f59e0b' },
    { label: 'Review',      value: props.stats?.review      ?? 0, color: '#8b5cf6' },
    { label: 'Done',        value: props.stats?.done        ?? 0, color: '#10b981' },
])

const deadlineUrgency = dueDate => {
    const days = Math.ceil((new Date(dueDate) - Date.now()) / 86400000)
    if (days < 0)   return { label: 'Terlambat',        class: 'bg-red-100 text-red-700 dark:bg-red-300/15 dark:text-red-300' }
    if (days === 0) return { label: 'Hari ini',          class: 'bg-red-100 text-red-700 dark:bg-red-300/15 dark:text-red-300' }
    if (days === 1) return { label: 'Besok',             class: 'bg-red-100 text-red-700 dark:bg-red-300/15 dark:text-red-300' }
    if (days <= 6)  return { label: `${days} hari lagi`, class: 'bg-amber-100 text-amber-700 dark:bg-amber-300/15 dark:text-amber-300' }
    return              { label: `${days} hari lagi`,    class: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-300/15 dark:text-emerald-300' }
}

onMounted(() => {
    nextTick(() => {
        // Counter animation per card
        statCards.value.forEach((card, i) => {
            const target = card.value
            const duration = 1300
            setTimeout(() => {
                const start = performance.now()
                const tick = now => {
                    const t    = Math.min((now - start) / duration, 1)
                    const ease = 1 - Math.pow(1 - t, 3)
                    displayedValues.value[i] = Math.round(ease * target)
                    if (t < 1) requestAnimationFrame(tick)
                    else displayedValues.value[i] = target
                }
                requestAnimationFrame(tick)
            }, 280 + i * 90)
        })

        // Progress bars animate in
        setTimeout(() => { animateProgress.value = true }, 720)

        // Charts initialise after card entrance settles
        setTimeout(() => {
            const isDark    = document.documentElement.classList.contains('dark')
            const textColor = isDark ? '#ffffff' : '#64748b'
            const gridColor = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.05)'

            new Chart(priorityChartRef.value, {
                type: 'bar',
                data: {
                    labels: ['Low', 'Medium', 'High'],
                    datasets: [{
                        label: 'Jumlah Task',
                        data: [props.tasksByPriority?.low ?? 0, props.tasksByPriority?.medium ?? 0, props.tasksByPriority?.high ?? 0],
                        backgroundColor: ['rgba(139,92,246,0.45)', 'rgba(139,92,246,0.72)', 'rgba(139,92,246,1)'],
                        borderRadius: 10,
                        borderSkipped: false,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: { duration: 900 },
                    animations: {
                        y: {
                            duration: 800,
                            easing: 'easeOutQuart',
                            delay: ctx => ctx.type === 'data' ? ctx.dataIndex * 300 : 0,
                        },
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: { callbacks: { label: ctx => ` ${ctx.raw} task` } },
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { color: textColor, font: { size: 12 } } },
                        y: { grid: { color: gridColor }, ticks: { color: textColor, precision: 0, font: { size: 12 } }, beginAtZero: true },
                    },
                },
            })

            new Chart(statusChartRef.value, {
                type: 'doughnut',
                data: {
                    labels: ['Todo', 'In Progress', 'Review', 'Done'],
                    datasets: [{
                        data: [props.stats?.todo ?? 0, props.stats?.in_progress ?? 0, props.stats?.review ?? 0, props.stats?.done ?? 0],
                        backgroundColor: ['#94a3b8', '#f59e0b', '#8b5cf6', '#10b981'],
                        borderWidth: 0,
                        hoverOffset: 8,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    rotation: -90,
                    animation: {
                        animateRotate: true,
                        animateScale: false,
                        duration: 1800,
                        easing: 'easeOutCubic',
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: { callbacks: { label: ctx => ` ${ctx.raw} task` } },
                    },
                    cutout: '68%',
                },
            })
        }, 420)
    })
})
</script>

<style scoped>
.section-header {
    animation: slideDown 0.5s cubic-bezier(0.16, 1, 0.3, 1) both;
}
.stat-card {
    animation: cardEntrance 0.6s cubic-bezier(0.16, 1, 0.3, 1) both;
    animation-delay: calc(var(--i) * 80ms);
}
.section-charts {
    animation: fadeInUp 0.65s cubic-bezier(0.16, 1, 0.3, 1) both;
    animation-delay: 340ms;
}
.section-bottom {
    animation: fadeInUp 0.65s cubic-bezier(0.16, 1, 0.3, 1) both;
    animation-delay: 460ms;
}
.legend-item {
    animation: fadeInLeft 0.4s cubic-bezier(0.16, 1, 0.3, 1) both;
    animation-delay: calc(1100ms + var(--i) * 80ms);
}

/* Sparkline drawing */
.sparkline {
    stroke-dasharray: 300;
    stroke-dashoffset: 300;
    animation: drawLine 1.1s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    animation-delay: calc(var(--i) * 90ms + 350ms);
}

/* Shimmer sweep on hover */
.stat-card::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(105deg, transparent 35%, rgba(255,255,255,0.13) 50%, transparent 65%);
    transform: translateX(-120%);
    transition: transform 0.55s ease;
    pointer-events: none;
}
.stat-card:hover::after {
    transform: translateX(120%);
}

/* Floating icon */
.icon-pulse {
    animation: iconFloat 3.5s ease-in-out infinite;
}

/* Animated progress bar */
.progress-bar {
    transition: width 1s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes slideDown {
    from { opacity: 0; transform: translateY(-20px); }
    to   { opacity: 1; transform: translateY(0); }
}
@keyframes cardEntrance {
    from { opacity: 0; transform: translateY(30px) scale(0.94); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
}
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(26px); }
    to   { opacity: 1; transform: translateY(0); }
}
@keyframes fadeInLeft {
    from { opacity: 0; transform: translateX(16px); }
    to   { opacity: 1; transform: translateX(0); }
}
@keyframes drawLine {
    to { stroke-dashoffset: 0; }
}
@keyframes iconFloat {
    0%, 100% { transform: translateY(0px); }
    50%       { transform: translateY(-4px); }
}
</style>
