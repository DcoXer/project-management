<template>
    <AppLayout>
        <div class="space-y-5">

            <!-- Header -->
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Tasks</h1>
                <p class="text-sm text-gray-500 mt-1">Semua task yang tersedia</p>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <div class="flex flex-wrap gap-3">
                    <select
                        v-model="form.status"
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        @change="applyFilter"
                    >
                        <option value="">Semua Status</option>
                        <option value="todo">Todo</option>
                        <option value="in_progress">In Progress</option>
                        <option value="review">Review</option>
                        <option value="done">Done</option>
                    </select>
                    <select
                        v-model="form.priority"
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        @change="applyFilter"
                    >
                        <option value="">Semua Priority</option>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                    </select>
                    <select
                        v-model="form.project_id"
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        @change="applyFilter"
                    >
                        <option value="">Semua Project</option>
                        <option v-for="p in projects" :key="p.id" :value="p.id">{{ p.name }}</option>
                    </select>
                    <button
                        v-if="hasFilter"
                        @click="resetFilter"
                        class="text-sm text-gray-500 hover:text-gray-700 px-3 py-2 rounded-lg hover:bg-gray-50 transition"
                    >
                        Reset
                    </button>
                </div>
            </div>

            <!-- Tasks Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div v-if="tasks.data.length === 0" class="px-5 py-12 text-center text-sm text-gray-400">
                    Tidak ada task ditemukan.
                </div>
                <table v-else class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50">
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Task</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Project</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Assignee</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Priority</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Due</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr
                            v-for="task in tasks.data"
                            :key="task.id"
                            class="hover:bg-gray-50 transition cursor-pointer"
                            @click="goToTask(task.id)"
                        >
                            <td class="px-5 py-3.5 font-medium text-gray-800 max-w-xs">
                                <span class="block truncate">{{ task.title }}</span>
                            </td>
                            <td class="px-4 py-3.5 text-gray-500 max-w-[140px]">
                                <span class="block truncate">{{ task.project?.name ?? '-' }}</span>
                            </td>
                            <td class="px-4 py-3.5 text-gray-500">
                                {{ task.assignee?.name ?? '-' }}
                            </td>
                            <td class="px-4 py-3.5">
                                <span :class="priorityClass(task.priority)" class="text-xs px-2 py-0.5 rounded-full font-medium">
                                    {{ task.priority }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5">
                                <span :class="taskStatusClass(task.status)" class="text-xs px-2 py-0.5 rounded-full font-medium">
                                    {{ taskStatusLabel(task.status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-gray-500">
                                {{ task.due_date ? formatDate(task.due_date) : '-' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="tasks.last_page > 1" class="flex items-center justify-center gap-1">
                <Link
                    v-for="link in tasks.links"
                    :key="link.label"
                    :href="link.url ?? '#'"
                    v-html="link.label"
                    class="px-3 py-1.5 rounded-lg text-sm transition"
                    :class="link.active
                        ? 'bg-blue-600 text-white font-medium'
                        : link.url
                            ? 'text-gray-600 hover:bg-gray-100'
                            : 'text-gray-300 cursor-default'"
                />
            </div>

        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link, router } from '@inertiajs/vue3'
import { reactive, computed } from 'vue'

const props = defineProps({
    tasks: Object,
    projects: Array,
    filters: Object,
})

const form = reactive({
    status: props.filters?.status ?? '',
    priority: props.filters?.priority ?? '',
    project_id: props.filters?.project_id ?? '',
})

const hasFilter = computed(() => form.status || form.priority || form.project_id)

const applyFilter = () => {
    router.get('/tasks', {
        status: form.status || undefined,
        priority: form.priority || undefined,
        project_id: form.project_id || undefined,
    }, { preserveState: true, replace: true })
}

const resetFilter = () => {
    form.status = ''
    form.priority = ''
    form.project_id = ''
    router.get('/tasks', {}, { preserveState: false, replace: true })
}

const goToTask = (id) => {
    router.visit(`/tasks/${id}`)
}

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
