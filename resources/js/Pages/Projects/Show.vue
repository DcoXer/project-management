<template>
    <AppLayout>
        <div class="space-y-6">

            <!-- Back + Header -->
            <div>
                <Link href="/projects" class="text-sm text-gray-400 hover:text-gray-600 transition">&larr; Kembali ke Projects</Link>
                <div class="flex flex-wrap items-start justify-between gap-3 mt-3">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">{{ project.name }}</h1>
                        <p class="text-sm text-gray-500 mt-1">Dibuat oleh {{ project.creator.name }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span :class="priorityClass(project.priority)" class="text-xs px-3 py-1 rounded-full font-medium">
                            {{ project.priority }}
                        </span>
                        <span :class="statusClass(project.status)" class="text-xs px-3 py-1 rounded-full font-medium">
                            {{ statusLabel(project.status) }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Left: Project info + Tasks -->
                <div class="lg:col-span-2 space-y-5">

                    <!-- Description -->
                    <div v-if="project.description" class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                        <h2 class="text-sm font-semibold text-gray-700 mb-2">Deskripsi</h2>
                        <p class="text-sm text-gray-600 leading-relaxed">{{ project.description }}</p>
                    </div>

                    <!-- Progress -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                        <div class="flex justify-between text-sm mb-2">
                            <span class="font-semibold text-gray-700">Progress</span>
                            <span class="text-gray-500">{{ project.tasks_done_count }}/{{ project.tasks_count }} task — {{ project.progress }}%</span>
                        </div>
                        <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                            <div
                                class="h-full bg-blue-500 rounded-full transition-all"
                                :style="{ width: project.progress + '%' }"
                            ></div>
                        </div>
                    </div>

                    <!-- Tasks -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                            <h2 class="font-semibold text-gray-800">Tasks</h2>
                            <span class="text-xs text-gray-400">{{ project.tasks?.length ?? 0 }} task</span>
                        </div>
                        <div class="divide-y divide-gray-50">
                            <div v-if="!project.tasks?.length" class="px-5 py-8 text-center text-sm text-gray-400">
                                Belum ada task.
                            </div>
                            <Link
                                v-for="task in project.tasks"
                                :key="task.id"
                                :href="`/tasks/${task.id}`"
                                class="flex items-center justify-between px-5 py-3.5 hover:bg-gray-50 transition"
                            >
                                <div class="min-w-0">
                                    <p class="text-sm text-gray-800 font-medium truncate">{{ task.title }}</p>
                                    <p v-if="task.assignee" class="text-xs text-gray-400 mt-0.5">{{ task.assignee.name }}</p>
                                </div>
                                <div class="flex items-center gap-2 shrink-0 ml-3">
                                    <span :class="priorityClass(task.priority)" class="text-xs px-2 py-0.5 rounded-full font-medium">
                                        {{ task.priority }}
                                    </span>
                                    <span :class="taskStatusClass(task.status)" class="text-xs px-2 py-0.5 rounded-full font-medium">
                                        {{ taskStatusLabel(task.status) }}
                                    </span>
                                </div>
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Right: Meta + Members -->
                <div class="space-y-5">

                    <!-- Meta -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 space-y-3">
                        <h2 class="text-sm font-semibold text-gray-700">Detail</h2>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-400">Mulai</span>
                                <span class="text-gray-700">{{ formatDate(project.start_date) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Selesai</span>
                                <span class="text-gray-700">{{ formatDate(project.end_date) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Members -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                        <h2 class="text-sm font-semibold text-gray-700 mb-3">Members</h2>
                        <div v-if="!project.members?.length" class="text-sm text-gray-400">Belum ada member.</div>
                        <div class="space-y-2">
                            <div
                                v-for="member in project.members"
                                :key="member.id"
                                class="flex items-center gap-3"
                            >
                                <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 text-sm font-medium flex items-center justify-center shrink-0">
                                    {{ member.name.charAt(0).toUpperCase() }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-700">{{ member.name }}</p>
                                    <p class="text-xs text-gray-400">{{ member.pivot?.role ?? '-' }}</p>
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

const props = defineProps({
    project: Object,
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
