<template>
    <AppLayout>
        <div class="space-y-5">

            <div>
                <h1 class="text-2xl font-bold text-ink-900 dark:text-sage-200 tracking-tight">Tasks</h1>
                <p class="text-sm text-ink-500 dark:text-sage-500 mt-1">Semua task yang tersedia</p>
            </div>

            <!-- Filters -->
            <div class="bg-white dark:bg-ink-800 rounded-xl border border-ink-900/8 dark:border-sage-300/8 p-4 shadow-sm">
                <div class="flex flex-wrap gap-3">
                    <select v-model="form.status" @change="applyFilter" class="border border-ink-200 dark:border-ink-600 bg-sage-50 dark:bg-ink-850 text-ink-900 dark:text-sage-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ink-900/15 dark:focus:ring-sage-300/20 transition">
                        <option value="">Semua Status</option>
                        <option value="todo">Todo</option>
                        <option value="in_progress">In Progress</option>
                        <option value="review">Review</option>
                        <option value="done">Done</option>
                    </select>
                    <select v-model="form.priority" @change="applyFilter" class="border border-ink-200 dark:border-ink-600 bg-sage-50 dark:bg-ink-850 text-ink-900 dark:text-sage-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ink-900/15 dark:focus:ring-sage-300/20 transition">
                        <option value="">Semua Priority</option>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                    </select>
                    <select v-model="form.project_id" @change="applyFilter" class="border border-ink-200 dark:border-ink-600 bg-sage-50 dark:bg-ink-850 text-ink-900 dark:text-sage-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ink-900/15 dark:focus:ring-sage-300/20 transition">
                        <option value="">Semua Project</option>
                        <option v-for="p in projects" :key="p.id" :value="p.id">{{ p.name }}</option>
                    </select>
                    <button v-if="hasFilter" @click="resetFilter" class="text-sm text-ink-500 dark:text-sage-500 hover:text-ink-800 dark:hover:text-sage-300 px-3 py-2 rounded-lg hover:bg-sage-100 dark:hover:bg-sage-300/8 transition">Reset</button>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white dark:bg-ink-800 rounded-xl border border-ink-900/8 dark:border-sage-300/8 overflow-hidden shadow-sm">
                <div v-if="tasks.data.length === 0" class="px-5 py-12 text-center text-sm text-ink-300 dark:text-sage-600">Tidak ada task ditemukan.</div>
                <table v-else class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-ink-100 dark:border-sage-300/8 bg-sage-50 dark:bg-ink-850">
                            <th class="text-left px-5 py-3 text-xs font-semibold text-ink-400 dark:text-sage-500 uppercase tracking-wide">Task</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-ink-400 dark:text-sage-500 uppercase tracking-wide">Project</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-ink-400 dark:text-sage-500 uppercase tracking-wide">Assignee</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-ink-400 dark:text-sage-500 uppercase tracking-wide">Priority</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-ink-400 dark:text-sage-500 uppercase tracking-wide">Status</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-ink-400 dark:text-sage-500 uppercase tracking-wide">Due</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ink-50 dark:divide-sage-300/5">
                        <tr v-for="task in tasks.data" :key="task.id" class="hover:bg-sage-50 dark:hover:bg-sage-300/4 transition cursor-pointer" @click="router.visit(`/tasks/${task.id}`)">
                            <td class="px-5 py-3.5 font-medium text-ink-900 dark:text-sage-200 max-w-xs"><span class="block truncate">{{ task.title }}</span></td>
                            <td class="px-4 py-3.5 text-ink-500 dark:text-sage-400 max-w-[140px]"><span class="block truncate">{{ task.project?.name ?? '-' }}</span></td>
                            <td class="px-4 py-3.5 text-ink-500 dark:text-sage-400">{{ task.assignee?.name ?? '-' }}</td>
                            <td class="px-4 py-3.5"><span :class="priorityClass(task.priority)" class="text-xs px-2 py-0.5 rounded-full font-medium">{{ task.priority }}</span></td>
                            <td class="px-4 py-3.5"><span :class="taskStatusClass(task.status)" class="text-xs px-2 py-0.5 rounded-full font-medium">{{ taskStatusLabel(task.status) }}</span></td>
                            <td class="px-4 py-3.5 text-ink-400 dark:text-sage-500">{{ task.due_date ? formatDate(task.due_date) : '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="tasks.last_page > 1" class="flex items-center justify-center gap-1">
                <Link
                    v-for="link in tasks.links" :key="link.label"
                    :href="link.url ?? '#'" v-html="link.label"
                    class="px-3 py-1.5 rounded-lg text-sm transition"
                    :class="link.active ? 'bg-ink-900 dark:bg-sage-300 text-sage-200 dark:text-ink-900 font-semibold' : link.url ? 'text-ink-600 dark:text-sage-400 hover:bg-ink-100 dark:hover:bg-sage-300/10' : 'text-ink-200 dark:text-ink-600 cursor-default'"
                />
            </div>

        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link, router } from '@inertiajs/vue3'
import { reactive, computed } from 'vue'

const props = defineProps({ tasks: Object, projects: Array, filters: Object })

const form = reactive({ status: props.filters?.status ?? '', priority: props.filters?.priority ?? '', project_id: props.filters?.project_id ?? '' })
const hasFilter = computed(() => form.status || form.priority || form.project_id)

const applyFilter = () => router.get('/tasks', { status: form.status||undefined, priority: form.priority||undefined, project_id: form.project_id||undefined }, { preserveState: true, replace: true })
const resetFilter = () => { form.status=''; form.priority=''; form.project_id=''; router.get('/tasks', {}, { preserveState: false, replace: true }) }

const taskStatusLabel = s => ({ todo:'Todo', in_progress:'In Progress', review:'Review', done:'Done' }[s] ?? s)
const taskStatusClass = s => ({ todo:'bg-ink-100 text-ink-600 dark:bg-ink-700/50 dark:text-ink-300', in_progress:'bg-sage-100 text-sage-700 dark:bg-sage-300/15 dark:text-sage-300', review:'bg-violet-100 text-violet-700 dark:bg-violet-300/15 dark:text-violet-300', done:'bg-emerald-100 text-emerald-700 dark:bg-emerald-300/15 dark:text-emerald-300' }[s] ?? 'bg-ink-100 text-ink-500')
const priorityClass   = p => ({ low:'bg-sage-100 text-sage-700 dark:bg-sage-300/15 dark:text-sage-300', medium:'bg-amber-100 text-amber-700 dark:bg-amber-300/15 dark:text-amber-300', high:'bg-red-100 text-red-700 dark:bg-red-300/15 dark:text-red-300' }[p] ?? 'bg-ink-100 text-ink-500')
const formatDate      = d => d ? new Date(d).toLocaleDateString('id-ID', { day:'numeric', month:'short', year:'numeric' }) : '-'
</script>
