<template>
    <AppLayout>
        <div class="space-y-5">

            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold text-ink-900 dark:text-white tracking-tight">{{ is_pm ? 'Team Tasks' : 'My Tasks' }}</h1>
                    <p class="text-sm text-ink-500 dark:text-white mt-1">{{ is_pm ? 'Semua task dari project yang kamu pimpin' : 'Task yang di-assign ke kamu' }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <button v-if="is_pm" @click="showCreateModal = true"
                        class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium bg-ink-900 dark:bg-sage-300 text-white dark:text-ink-900 hover:opacity-90 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Buat Task
                    </button>
                    <!-- <a :href="exportUrl"
                        class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium border border-ink-200 dark:border-ink-600 text-ink-600 dark:text-white hover:bg-ink-100 dark:hover:bg-ink-700 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        Export PDF
                    </a> -->
                    <Link href="/tasks/kanban"
                        class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium border border-ink-200 dark:border-ink-600 text-ink-600 dark:text-white hover:bg-ink-100 dark:hover:bg-ink-700 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 4.5v15m6-15v15m-10.875 0h15.75c.621 0 1.125-.504 1.125-1.125V5.625c0-.621-.504-1.125-1.125-1.125H4.125C3.504 4.5 3 5.004 3 5.625v12.75c0 .621.504 1.125 1.125 1.125z" />
                        </svg>
                        Kanban View
                    </Link>
                </div>
            </div>

            <!-- Modal Buat Task -->
            <Teleport to="body">
                <Transition
                    enter-active-class="transition duration-200 ease-out"
                    enter-from-class="opacity-0"
                    enter-to-class="opacity-100"
                    leave-active-class="transition duration-150 ease-in"
                    leave-from-class="opacity-100"
                    leave-to-class="opacity-0"
                >
                    <div v-if="showCreateModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" @mousedown.self="closeModal">
                        <Transition
                            enter-active-class="transition duration-200 ease-out"
                            enter-from-class="opacity-0 scale-95"
                            enter-to-class="opacity-100 scale-100"
                            leave-active-class="transition duration-150 ease-in"
                            leave-from-class="opacity-100 scale-100"
                            leave-to-class="opacity-0 scale-95"
                        >
                            <div v-if="showCreateModal" class="bg-white dark:bg-ink-800 rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">

                                <!-- Header -->
                                <div class="flex items-center justify-between px-6 py-4 border-b border-ink-100 dark:border-sage-300/8">
                                    <h2 class="text-base font-semibold text-ink-900 dark:text-white">Buat Task Baru</h2>
                                    <button @click="closeModal" class="p-1.5 rounded-lg text-ink-400 dark:text-white hover:bg-ink-100 dark:hover:bg-sage-300/8 transition">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>

                                <!-- Form -->
                                <form @submit.prevent="submitCreate" class="px-6 py-5 space-y-4">

                                    <!-- Project -->
                                    <div>
                                        <label class="block text-sm font-medium text-ink-700 dark:text-white mb-1.5">Project <span class="text-red-500">*</span></label>
                                        <select v-model="createForm.project_id" class="w-full border rounded-lg px-3 py-2 text-sm bg-white dark:bg-ink-850 text-ink-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-ink-900/15 dark:focus:ring-sage-300/20 transition"
                                            :class="createForm.errors.project_id ? 'border-red-400' : 'border-ink-200 dark:border-ink-600'">
                                            <option value="">Pilih project...</option>
                                            <option v-for="p in managed_projects" :key="p.id" :value="p.id">{{ p.name }}</option>
                                        </select>
                                        <p v-if="createForm.errors.project_id" class="mt-1 text-xs text-red-500">{{ createForm.errors.project_id }}</p>
                                    </div>

                                    <!-- Title -->
                                    <div>
                                        <label class="block text-sm font-medium text-ink-700 dark:text-white mb-1.5">Judul Task <span class="text-red-500">*</span></label>
                                        <input v-model="createForm.title" type="text" placeholder="Masukkan judul task..." class="w-full border rounded-lg px-3 py-2 text-sm bg-white dark:bg-ink-850 text-ink-900 dark:text-white placeholder-ink-300 dark:placeholder-sage-600 focus:outline-none focus:ring-2 focus:ring-ink-900/15 dark:focus:ring-sage-300/20 transition"
                                            :class="createForm.errors.title ? 'border-red-400' : 'border-ink-200 dark:border-ink-600'" />
                                        <p v-if="createForm.errors.title" class="mt-1 text-xs text-red-500">{{ createForm.errors.title }}</p>
                                    </div>

                                    <!-- Description -->
                                    <div>
                                        <label class="block text-sm font-medium text-ink-700 dark:text-white mb-1.5">Deskripsi</label>
                                        <textarea v-model="createForm.description" rows="3" placeholder="Deskripsi task (opsional)..." class="w-full border border-ink-200 dark:border-ink-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-ink-850 text-ink-900 dark:text-white placeholder-ink-300 dark:placeholder-sage-600 focus:outline-none focus:ring-2 focus:ring-ink-900/15 dark:focus:ring-sage-300/20 transition resize-none"></textarea>
                                    </div>

                                    <!-- Specialization -->
                                    <div>
                                        <label class="block text-sm font-medium text-ink-700 dark:text-white mb-1.5">Specialization <span class="text-red-500">*</span></label>
                                        <select v-model="selectedSpecialization" @change="createForm.assigned_to = ''" class="w-full border border-ink-200 dark:border-ink-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-ink-850 text-ink-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-ink-900/15 dark:focus:ring-sage-300/20 transition">
                                            <option value="">Pilih specialization...</option>
                                            <option v-for="s in specializations" :key="s.value" :value="s.value">{{ s.label }}</option>
                                        </select>
                                    </div>

                                    <!-- Assignee -->
                                    <div>
                                        <label class="block text-sm font-medium text-ink-700 dark:text-white mb-1.5">Assignee <span class="text-red-500">*</span></label>
                                        <select v-model="createForm.assigned_to" :disabled="!selectedSpecialization" class="w-full border rounded-lg px-3 py-2 text-sm bg-white dark:bg-ink-850 text-ink-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-ink-900/15 dark:focus:ring-sage-300/20 transition disabled:opacity-50 disabled:cursor-not-allowed"
                                            :class="createForm.errors.assigned_to ? 'border-red-400' : 'border-ink-200 dark:border-ink-600'">
                                            <option value="">{{ selectedSpecialization ? (filteredDevelopers.length ? 'Pilih developer...' : 'Tidak ada developer tersedia') : 'Pilih specialization dulu...' }}</option>
                                            <option v-for="d in filteredDevelopers" :key="d.id" :value="d.id">{{ d.name }}</option>
                                        </select>
                                        <p v-if="createForm.errors.assigned_to" class="mt-1 text-xs text-red-500">{{ createForm.errors.assigned_to }}</p>
                                    </div>

                                    <!-- Priority & Deadline -->
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-sm font-medium text-ink-700 dark:text-white mb-1.5">Priority <span class="text-red-500">*</span></label>
                                            <select v-model="createForm.priority" class="w-full border rounded-lg px-3 py-2 text-sm bg-white dark:bg-ink-850 text-ink-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-ink-900/15 dark:focus:ring-sage-300/20 transition"
                                                :class="createForm.errors.priority ? 'border-red-400' : 'border-ink-200 dark:border-ink-600'">
                                                <option value="low">Low</option>
                                                <option value="medium">Medium</option>
                                                <option value="high">High</option>
                                            </select>
                                            <p v-if="createForm.errors.priority" class="mt-1 text-xs text-red-500">{{ createForm.errors.priority }}</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-ink-700 dark:text-white mb-1.5">Deadline</label>
                                            <input v-model="createForm.due_date" type="date" class="w-full border border-ink-200 dark:border-ink-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-ink-850 text-ink-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-ink-900/15 dark:focus:ring-sage-300/20 transition" />
                                        </div>
                                    </div>

                                    <!-- Start Date & End Date -->
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-sm font-medium text-ink-700 dark:text-white mb-1.5">Start Date</label>
                                            <input v-model="createForm.start_date" type="date" class="w-full border border-ink-200 dark:border-ink-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-ink-850 text-ink-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-ink-900/15 dark:focus:ring-sage-300/20 transition" />
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-ink-700 dark:text-white mb-1.5">End Date</label>
                                            <input v-model="createForm.end_date" type="date" class="w-full border border-ink-200 dark:border-ink-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-ink-850 text-ink-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-ink-900/15 dark:focus:ring-sage-300/20 transition" />
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-ink-100 dark:border-sage-300/8">
                                        <button type="button" @click="closeModal" class="px-4 py-2 text-sm font-medium text-ink-600 dark:text-white hover:bg-ink-100 dark:hover:bg-sage-300/8 rounded-lg transition">
                                            Batal
                                        </button>
                                        <button type="submit" :disabled="createForm.processing" class="px-4 py-2 text-sm font-medium bg-ink-900 dark:bg-sage-300 text-white dark:text-ink-900 rounded-lg hover:opacity-90 transition disabled:opacity-60">
                                            {{ createForm.processing ? 'Menyimpan...' : 'Buat Task' }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </Transition>
                    </div>
                </Transition>
            </Teleport>

            <!-- Filters -->
            <div class="bg-white dark:bg-ink-800 rounded-xl border border-ink-900/8 dark:border-sage-300/8 p-4 shadow-sm">
                <div class="flex flex-wrap gap-3 items-center">
                    <select v-model="form.status" @change="applyFilter" class="border border-ink-200 dark:border-ink-600 bg-sage-50 dark:bg-ink-850 text-ink-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ink-900/15 dark:focus:ring-sage-300/20 transition">
                        <option value="">Semua Status</option>
                        <option value="todo">Todo</option>
                        <option value="in_progress">In Progress</option>
                        <option value="review">Review</option>
                        <option value="done">Done</option>
                    </select>
                    <select v-model="form.priority" @change="applyFilter" class="border border-ink-200 dark:border-ink-600 bg-sage-50 dark:bg-ink-850 text-ink-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ink-900/15 dark:focus:ring-sage-300/20 transition">
                        <option value="">Semua Priority</option>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                    </select>
                    <select v-model="form.project_id" @change="applyFilter" class="border border-ink-200 dark:border-ink-600 bg-sage-50 dark:bg-ink-850 text-ink-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ink-900/15 dark:focus:ring-sage-300/20 transition">
                        <option value="">Semua Project</option>
                        <option v-for="p in projects" :key="p.id" :value="p.id">{{ p.name }}</option>
                    </select>
                    <!-- Date range filter (compact, satu container) -->
                    <div class="flex items-center gap-2 border border-ink-200 dark:border-ink-600 bg-sage-50 dark:bg-ink-850 rounded-lg px-3 py-2">
                        <span class="text-xs text-ink-400 dark:text-white shrink-0">Start</span>
                        <input v-model="form.start_date" type="date"
                            class="w-[118px] bg-transparent text-xs text-ink-900 dark:text-white focus:outline-none dark:[color-scheme:dark]" />
                        <span class="text-ink-300 dark:text-white text-xs shrink-0">–</span>
                        <span class="text-xs text-ink-400 dark:text-white shrink-0">End</span>
                        <input v-model="form.end_date" type="date"
                            class="w-[118px] bg-transparent text-xs text-ink-900 dark:text-white focus:outline-none dark:[color-scheme:dark]" />
                        <button @click="applyDateFilter" title="Terapkan filter tanggal"
                            class="shrink-0 text-ink-400 dark:text-white hover:text-ink-700 dark:hover:text-white transition ml-1">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Task list -->
            <div class="bg-white dark:bg-ink-800 rounded-xl border border-ink-900/8 dark:border-sage-300/8 shadow-sm overflow-hidden">
                <div v-if="tasks.data.length === 0" class="px-5 py-12 text-center text-sm text-ink-300 dark:text-white">Tidak ada task ditemukan.</div>
                <template v-else>

                    <!-- ── Mobile: card list (< md) ────────────────────── -->
                    <div class="divide-y divide-ink-50 dark:divide-sage-300/5 md:hidden">
                        <div v-for="task in tasks.data" :key="task.id"
                            class="px-4 py-3.5 hover:bg-sage-50 dark:hover:bg-sage-300/4 transition cursor-pointer active:bg-sage-100 dark:active:bg-sage-300/8"
                            @click="router.visit(`/tasks/${task.id}`)">
                            <div class="flex items-start justify-between gap-2 mb-1.5">
                                <p class="font-medium text-ink-900 dark:text-white text-sm leading-snug flex-1">{{ task.title }}</p>
                                <TaskStatusBadge :status="task.status" sm />
                            </div>
                            <div class="flex flex-wrap items-center gap-x-2 gap-y-1 mt-1">
                                <span class="text-xs text-ink-400 dark:text-white truncate max-w-[140px]">{{ task.project?.name ?? '-' }}</span>
                                <span class="text-ink-200 dark:text-white">·</span>
                                <PriorityBadge :priority="task.priority" sm />
                                <template v-if="task.deadline_status === 'overdue'">
                                    <span class="text-ink-200 dark:text-white">·</span>
                                    <span class="text-xs text-red-600 dark:text-red-400 font-medium">{{ formatDate(task.due_date) }}</span>
                                    <span class="text-[10px] bg-red-100 dark:bg-red-400/15 text-red-600 dark:text-red-400 px-1.5 py-0.5 rounded font-semibold">Overdue</span>
                                </template>
                                <template v-else-if="task.deadline_status === 'warning'">
                                    <span class="text-ink-200 dark:text-white">·</span>
                                    <span class="text-xs text-amber-600 dark:text-amber-400">{{ formatDate(task.due_date) }}</span>
                                    <span class="text-[10px] bg-amber-100 dark:bg-amber-400/15 text-amber-600 dark:text-amber-400 px-1.5 py-0.5 rounded font-semibold">Due soon</span>
                                </template>
                                <template v-else-if="task.due_date">
                                    <span class="text-ink-200 dark:text-white">·</span>
                                    <span class="text-xs text-ink-400 dark:text-white">{{ formatDate(task.due_date) }}</span>
                                </template>
                            </div>
                            <p v-if="task.assignee" class="text-xs text-ink-400 dark:text-white mt-1">{{ task.assignee.name }}</p>
                        </div>
                    </div>

                    <!-- ── Desktop: table (≥ md) ───────────────────────── -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full text-sm min-w-[700px]">
                            <thead>
                                <tr class="border-b border-ink-100 dark:border-sage-300/8 bg-sage-50 dark:bg-ink-850">
                                    <th class="text-left px-5 py-3 text-xs font-semibold text-ink-400 dark:text-white uppercase tracking-wide">
                                        <button @click="setSort('title')" class="flex items-center gap-1 hover:text-ink-600 dark:hover:text-white transition">
                                            Task <span :class="form.sort_by === 'title' ? 'text-ink-700 dark:text-white' : 'opacity-40'">{{ sortIcon('title') }}</span>
                                        </button>
                                    </th>
                                    <th class="text-left px-4 py-3 text-xs font-semibold text-ink-400 dark:text-white uppercase tracking-wide">Project</th>
                                    <th class="text-left px-4 py-3 text-xs font-semibold text-ink-400 dark:text-white uppercase tracking-wide">Assignee</th>
                                    <th class="text-left px-4 py-3 text-xs font-semibold text-ink-400 dark:text-white uppercase tracking-wide">
                                        <button @click="setSort('priority')" class="flex items-center gap-1 hover:text-ink-600 dark:hover:text-white transition">
                                            Priority <span :class="form.sort_by === 'priority' ? 'text-ink-700 dark:text-white' : 'opacity-40'">{{ sortIcon('priority') }}</span>
                                        </button>
                                    </th>
                                    <th class="text-left px-4 py-3 text-xs font-semibold text-ink-400 dark:text-white uppercase tracking-wide">
                                        <button @click="setSort('status')" class="flex items-center gap-1 hover:text-ink-600 dark:hover:text-white transition">
                                            Status <span :class="form.sort_by === 'status' ? 'text-ink-700 dark:text-white' : 'opacity-40'">{{ sortIcon('status') }}</span>
                                        </button>
                                    </th>
                                    <th class="text-left px-4 py-3 text-xs font-semibold text-ink-400 dark:text-white uppercase tracking-wide">
                                        <button @click="setSort('due_date')" class="flex items-center gap-1 hover:text-ink-600 dark:hover:text-white transition">
                                            Deadline <span :class="form.sort_by === 'due_date' ? 'text-ink-700 dark:text-white' : 'opacity-40'">{{ sortIcon('due_date') }}</span>
                                        </button>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-ink-50 dark:divide-sage-300/5">
                                <tr v-for="task in tasks.data" :key="task.id" class="hover:bg-sage-50 dark:hover:bg-sage-300/4 transition cursor-pointer" @click="router.visit(`/tasks/${task.id}`)">
                                    <td class="px-5 py-3.5 font-medium text-ink-900 dark:text-white max-w-xs"><span class="block truncate">{{ task.title }}</span></td>
                                    <td class="px-4 py-3.5 text-ink-500 dark:text-white max-w-[140px]"><span class="block truncate">{{ task.project?.name ?? '-' }}</span></td>
                                    <td class="px-4 py-3.5 text-ink-500 dark:text-white">{{ task.assignee?.name ?? '-' }}</td>
                                    <td class="px-4 py-3.5"><PriorityBadge :priority="task.priority" sm /></td>
                                    <td class="px-4 py-3.5"><TaskStatusBadge :status="task.status" sm /></td>
                                    <td class="px-4 py-3.5">
                                        <template v-if="task.deadline_status === 'overdue'">
                                            <span class="text-xs text-red-600 dark:text-red-400 font-medium">{{ formatDate(task.due_date) }}</span>
                                            <span class="ml-1.5 text-[10px] bg-red-100 dark:bg-red-400/15 text-red-600 dark:text-red-400 px-1.5 py-0.5 rounded font-semibold">Overdue</span>
                                        </template>
                                        <template v-else-if="task.deadline_status === 'warning'">
                                            <span class="text-xs text-amber-600 dark:text-amber-400 font-medium">{{ formatDate(task.due_date) }}</span>
                                            <span class="ml-1.5 text-[10px] bg-amber-100 dark:bg-amber-400/15 text-amber-600 dark:text-amber-400 px-1.5 py-0.5 rounded font-semibold">Due soon</span>
                                        </template>
                                        <span v-else class="text-xs text-ink-400 dark:text-white">{{ formatDate(task.due_date) }}</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </template>
            </div>

            <!-- Pagination -->
            <div v-if="tasks.last_page > 1" class="flex items-center justify-center gap-1">
                <Link
                    v-for="link in tasks.links" :key="link.label"
                    :href="link.url ?? '#'" v-html="link.label"
                    class="px-3 py-1.5 rounded-lg text-sm transition"
                    :class="link.active ? 'bg-ink-900 dark:bg-sage-300 text-sage-200 dark:text-ink-900 font-semibold' : link.url ? 'text-ink-600 dark:text-white hover:bg-ink-100 dark:hover:bg-sage-300/10' : 'text-ink-200 dark:text-ink-600 cursor-default'"
                />
            </div>

        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link, router, useForm } from '@inertiajs/vue3'
import { reactive, computed, ref } from 'vue'
import { formatDate } from '@/composables/useFormatters'
import TaskStatusBadge from '@/Components/TaskStatusBadge.vue'
import PriorityBadge from '@/Components/PriorityBadge.vue'

const props = defineProps({
    tasks: Object,
    projects: Array,
    managed_projects: { type: Array, default: () => [] },
    developers: { type: Array, default: () => [] },
    filters: Object,
    is_pm: Boolean,
})

const form = reactive({
    status:     props.filters?.status     ?? '',
    priority:   props.filters?.priority   ?? '',
    project_id: props.filters?.project_id ?? '',
    start_date: props.filters?.start_date ?? '',
    end_date:   props.filters?.end_date   ?? '',
    sort_by:    props.filters?.sort_by    ?? '',
    sort_dir:   props.filters?.sort_dir   ?? '',
})
const hasFilter = computed(() => form.status || form.priority || form.project_id || form.start_date || form.end_date || form.sort_by)

const exportUrl = computed(() => {
    const params = new URLSearchParams()
    if (form.status)     params.set('status', form.status)
    if (form.priority)   params.set('priority', form.priority)
    if (form.project_id) params.set('project_id', form.project_id)
    const qs = params.toString()
    return '/tasks/export-pdf' + (qs ? '?' + qs : '')
})

const applyFilter = () => router.get('/tasks', {
    status:     form.status     || undefined,
    priority:   form.priority   || undefined,
    project_id: form.project_id || undefined,
    start_date: form.start_date || undefined,
    end_date:   form.end_date   || undefined,
    sort_by:    form.sort_by    || undefined,
    sort_dir:   form.sort_dir   || undefined,
}, { preserveState: true, replace: true })

const applyDateFilter = () => router.get('/tasks', {
    status:     form.status     || undefined,
    priority:   form.priority   || undefined,
    project_id: form.project_id || undefined,
    start_date: form.start_date || undefined,
    end_date:   form.end_date   || undefined,
    sort_by:    form.sort_by    || undefined,
    sort_dir:   form.sort_dir   || undefined,
}, { preserveState: false, replace: true })

const resetFilter = () => {
    form.status = ''; form.priority = ''; form.project_id = ''
    form.start_date = ''; form.end_date = ''
    form.sort_by = ''; form.sort_dir = ''
    router.get('/tasks', {}, { preserveState: false, replace: true })
}

const setSort = (col) => {
    if (form.sort_by === col) {
        form.sort_dir = form.sort_dir === 'asc' ? 'desc' : 'asc'
    } else {
        form.sort_by  = col
        form.sort_dir = 'asc'
    }
    applyFilter()
}

const sortIcon = (col) => {
    if (form.sort_by !== col) return '↕'
    return form.sort_dir === 'asc' ? '↑' : '↓'
}

// ── Create Task Modal ───────────────────────────────────────────────────
const showCreateModal = ref(false)

const createForm = useForm({
    project_id:  '',
    title:       '',
    description: '',
    assigned_to: '',
    priority:    'medium',
    due_date:    '',
    start_date:  '',
    end_date:    '',
})

const selectedSpecialization = ref('')

const specializations = [
    { value: 'backend',  label: 'Backend' },
    { value: 'frontend', label: 'Frontend' },
    { value: 'ui/ux',    label: 'UI/UX' },
]

const filteredDevelopers = computed(() => {
    if (!selectedSpecialization.value) return []
    return props.developers.filter(d => d.specialization === selectedSpecialization.value)
})

const closeModal = () => {
    showCreateModal.value = false
    selectedSpecialization.value = ''
    createForm.reset()
    createForm.clearErrors()
}

const submitCreate = () => {
    createForm.post('/tasks', {
        onSuccess: () => closeModal(),
    })
}
</script>
