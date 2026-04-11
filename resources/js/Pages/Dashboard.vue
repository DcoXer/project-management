<template>
    <AppLayout>
        <div class="space-y-6">

            <!-- Page Header -->
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
                <p class="text-sm text-gray-500 mt-1">Selamat datang, {{ $page.props.auth.user.name }}</p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Projects</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ stats.total_projects }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Tasks</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ stats.total_tasks }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Users</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ stats.total_users }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Task Saya</p>
                    <p class="text-3xl font-bold text-blue-600 mt-1">{{ stats.my_tasks }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- Recent Projects -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                        <h2 class="font-semibold text-gray-800">Project Terbaru</h2>
                        <Link href="/projects" class="text-sm text-blue-600 hover:underline">Lihat semua</Link>
                    </div>
                    <div class="divide-y divide-gray-50">
                        <div v-if="recentProjects.length === 0" class="px-5 py-8 text-center text-sm text-gray-400">
                            Belum ada project.
                        </div>
                        <Link
                            v-for="project in recentProjects"
                            :key="project.id"
                            :href="`/projects/${project.id}`"
                            class="block px-5 py-4 hover:bg-gray-50 transition"
                        >
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-800 truncate">{{ project.name }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">oleh {{ project.creator.name }}</p>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <span :class="priorityClass(project.priority)" class="text-xs px-2 py-0.5 rounded-full font-medium">
                                        {{ project.priority }}
                                    </span>
                                    <span :class="statusClass(project.status)" class="text-xs px-2 py-0.5 rounded-full font-medium">
                                        {{ statusLabel(project.status) }}
                                    </span>
                                </div>
                            </div>
                            <!-- Progress bar -->
                            <div class="mt-3">
                                <div class="flex justify-between text-xs text-gray-400 mb-1">
                                    <span>{{ project.tasks_done_count }}/{{ project.tasks_count }} task</span>
                                    <span>{{ project.progress }}%</span>
                                </div>
                                <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                    <div
                                        class="h-full bg-blue-500 rounded-full transition-all"
                                        :style="{ width: project.progress + '%' }"
                                    ></div>
                                </div>
                            </div>
                        </Link>
                    </div>
                </div>

                <!-- My Tasks -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                        <h2 class="font-semibold text-gray-800">Task Saya</h2>
                        <Link href="/tasks" class="text-sm text-blue-600 hover:underline">Lihat semua</Link>
                    </div>
                    <div class="divide-y divide-gray-50">
                        <div v-if="myTasks.length === 0" class="px-5 py-8 text-center text-sm text-gray-400">
                            Tidak ada task yang di-assign ke kamu.
                        </div>
                        <Link
                            v-for="task in myTasks"
                            :key="task.id"
                            :href="`/tasks/${task.id}`"
                            class="block px-5 py-4 hover:bg-gray-50 transition"
                        >
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-800 truncate">{{ task.title }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ task.project.name }}</p>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <span :class="priorityClass(task.priority)" class="text-xs px-2 py-0.5 rounded-full font-medium">
                                        {{ task.priority }}
                                    </span>
                                    <span :class="taskStatusClass(task.status)" class="text-xs px-2 py-0.5 rounded-full font-medium">
                                        {{ taskStatusLabel(task.status) }}
                                    </span>
                                </div>
                            </div>
                            <p v-if="task.due_date" class="text-xs text-gray-400 mt-1">
                                Due: {{ formatDate(task.due_date) }}
                            </p>
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

const props = defineProps({
    stats: Object,
    recentProjects: Array,
    myTasks: Array,
})

const statusLabel = (s) => ({
    planning: 'Planning',
    in_progress: 'In Progress',
    on_hold: 'On Hold',
    completed: 'Completed',
}[s] ?? s)

const statusClass = (s) => ({
    planning: 'bg-gray-100 text-gray-600',
    in_progress: 'bg-blue-100 text-blue-600',
    on_hold: 'bg-yellow-100 text-yellow-600',
    completed: 'bg-green-100 text-green-600',
}[s] ?? 'bg-gray-100 text-gray-600')

const taskStatusLabel = (s) => ({
    todo: 'Todo',
    in_progress: 'In Progress',
    review: 'Review',
    done: 'Done',
}[s] ?? s)

const taskStatusClass = (s) => ({
    todo: 'bg-gray-100 text-gray-600',
    in_progress: 'bg-blue-100 text-blue-600',
    review: 'bg-purple-100 text-purple-600',
    done: 'bg-green-100 text-green-600',
}[s] ?? 'bg-gray-100 text-gray-600')

const priorityClass = (p) => ({
    low: 'bg-green-100 text-green-600',
    medium: 'bg-yellow-100 text-yellow-600',
    high: 'bg-red-100 text-red-600',
}[p] ?? 'bg-gray-100 text-gray-600')

const formatDate = (d) => {
    if (!d) return '-'
    return new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}
</script>
