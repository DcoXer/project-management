<template>
    <AppLayout>
        <div class="space-y-5">

            <!-- Header -->
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold text-ink-900 dark:text-sage-200 tracking-tight">Kanban Board</h1>
                    <p class="text-sm text-ink-500 dark:text-sage-500 mt-1">Visual status task per kolom</p>
                </div>
                <div class="flex items-center gap-2">
                    <select v-model="selectedProject" @change="applyFilter"
                        class="border border-ink-200 dark:border-ink-600 bg-sage-50 dark:bg-ink-850 text-ink-900 dark:text-sage-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ink-900/15 dark:focus:ring-sage-300/20 transition">
                        <option value="">Semua Project</option>
                        <option v-for="p in projects" :key="p.id" :value="p.id">{{ p.name }}</option>
                    </select>
                    <Link href="/tasks"
                        class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium border border-ink-200 dark:border-ink-600 text-ink-600 dark:text-sage-400 hover:bg-ink-100 dark:hover:bg-ink-700 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                        </svg>
                        List View
                    </Link>
                </div>
            </div>

            <!-- Legend workflow -->
            <div class="flex flex-wrap items-center gap-x-5 gap-y-1.5 text-xs text-ink-400 dark:text-sage-500 px-0.5">
                <span class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-sage-500 inline-block"></span>
                    Developer: drag <strong class="text-ink-600 dark:text-sage-400">Todo → In Progress</strong>
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-violet-500 inline-block"></span>
                    Submit ke Review: lewat halaman detail task (wajib lampirkan bukti)
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span>
                    Approve/Reject: PM via halaman detail task
                </span>
            </div>

            <!-- Kanban Columns -->
            <div class="flex gap-4 overflow-x-auto pb-4" style="min-height: 70vh">
                <div
                    v-for="col in columnList" :key="col.key"
                    class="flex flex-col gap-3 min-w-[280px] w-[280px] shrink-0"
                >
                    <!-- Column Header -->
                    <div class="flex items-center justify-between px-1">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full" :class="col.dot"></span>
                            <span class="text-sm font-semibold text-ink-700 dark:text-sage-300">{{ col.label }}</span>
                        </div>
                        <span class="text-xs font-semibold bg-ink-100 dark:bg-ink-700 text-ink-500 dark:text-sage-400 rounded-full px-2 py-0.5">
                            {{ localColumns[col.key].length }}
                        </span>
                    </div>

                    <!-- Drop Zone -->
                    <div
                        class="flex-1 rounded-xl border-2 transition-all p-2 space-y-2"
                        :class="dragOverCol === col.key
                            ? 'border-dashed ' + col.borderActive + ' bg-white dark:bg-ink-800'
                            : 'border-transparent bg-ink-100/60 dark:bg-ink-800/40'"
                        @dragover.prevent="dragOverCol = col.key"
                        @dragleave="dragOverCol = null"
                        @drop.prevent="onDrop(col.key)"
                    >
                        <!-- Empty state -->
                        <div v-if="!localColumns[col.key].length"
                            class="flex items-center justify-center h-20 rounded-lg border border-dashed border-ink-200 dark:border-ink-600">
                            <span class="text-xs text-ink-300 dark:text-sage-600">Tidak ada task</span>
                        </div>

                        <!-- Task Cards -->
                        <div
                            v-for="task in localColumns[col.key]" :key="task.id"
                            :draggable="isDraggable(task, col.key)"
                            @dragstart="isDraggable(task, col.key) ? onDragStart(task, col.key) : $event.preventDefault()"
                            @dragend="dragOverCol = null"
                            class="bg-white dark:bg-ink-800 rounded-xl border border-ink-900/8 dark:border-sage-300/8 p-3.5 shadow-sm transition-all select-none"
                            :class="[
                                isDraggable(task, col.key)
                                    ? 'cursor-grab active:cursor-grabbing active:opacity-60 active:scale-95 hover:shadow-md hover:border-ink-900/15 dark:hover:border-sage-300/15'
                                    : 'cursor-default opacity-90',
                                dragging?.id === task.id ? 'opacity-40' : ''
                            ]"
                        >
                            <!-- Lock badge untuk task yang tidak bisa di-drag -->
                            <div class="flex items-start justify-between gap-2 mb-2">
                                <Link :href="`/tasks/${task.id}`"
                                    class="block text-sm font-semibold text-ink-900 dark:text-sage-200 hover:text-sage-700 dark:hover:text-sage-300 leading-snug flex-1"
                                    @click.stop>
                                    {{ task.title }}
                                </Link>
                                <!-- Indikator status lock -->
                                <span v-if="col.key === 'done'" title="Task selesai, terkunci"
                                    class="shrink-0 text-emerald-500 dark:text-emerald-400">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12 1.5a5.25 5.25 0 00-5.25 5.25v3a3 3 0 00-3 3v6.75a3 3 0 003 3h10.5a3 3 0 003-3v-6.75a3 3 0 00-3-3v-3c0-2.9-2.35-5.25-5.25-5.25zm3.75 8.25v-3a3.75 3.75 0 10-7.5 0v3h7.5z" clip-rule="evenodd" /></svg>
                                </span>
                                <span v-else-if="col.key === 'review'" title="Sedang review, menunggu PM"
                                    class="shrink-0 text-violet-500 dark:text-violet-400">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                </span>
                                <span v-else-if="col.key === 'todo' && task.project?.status === 'planning'" title="Menunggu PM memulai project"
                                    class="shrink-0 text-amber-400 dark:text-amber-500">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003zM12 8.25a.75.75 0 01.75.75v3.75a.75.75 0 01-1.5 0V9a.75.75 0 01.75-.75zm0 8.25a.75.75 0 100-1.5.75.75 0 000 1.5z" clip-rule="evenodd" /></svg>
                                </span>
                                <span v-else-if="!isDraggable(task, col.key)" title="Buka detail task untuk mengubah status"
                                    class="shrink-0 text-ink-300 dark:text-sage-600">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12 1.5a5.25 5.25 0 00-5.25 5.25v3a3 3 0 00-3 3v6.75a3 3 0 003 3h10.5a3 3 0 003-3v-6.75a3 3 0 00-3-3v-3c0-2.9-2.35-5.25-5.25-5.25zm3.75 8.25v-3a3.75 3.75 0 10-7.5 0v3h7.5z" clip-rule="evenodd" /></svg>
                                </span>
                            </div>

                            <!-- Project -->
                            <p v-if="task.project" class="text-xs text-ink-400 dark:text-sage-500 mb-2.5 flex items-center gap-1">
                                <svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" />
                                </svg>
                                <span class="truncate">{{ task.project.name }}</span>
                            </p>

                            <!-- Footer -->
                            <div class="flex items-center justify-between gap-2">
                                <PriorityBadge :priority="task.priority" sm />
                                <div class="flex items-center gap-1.5">
                                    <span v-if="task.due_date" class="text-[11px] text-ink-400 dark:text-sage-500"
                                        :class="{ 'text-red-500 dark:text-red-400 font-medium': isOverdue(task) }">
                                        {{ formatDateShort(task.due_date) }}
                                    </span>
                                    <UserAvatar v-if="task.assignee" :name="task.assignee.name" size="sm"
                                        :title="task.assignee.name"
                                        class="bg-sage-300 text-ink-900" />
                                </div>
                            </div>

                            <!-- Hint untuk task yang bisa di-drag -->
                            <p v-if="isDraggable(task, col.key)"
                                class="text-[10px] text-ink-300 dark:text-sage-600 mt-2 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9h16.5m-16.5 6.75h16.5" /></svg>
                                Drag ke In Progress
                            </p>
                            <p v-else-if="col.key === 'todo' && task.project?.status === 'planning' && task.assigned_to === authUser.id"
                                class="text-[10px] text-amber-500 dark:text-amber-400 mt-2 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                Menunggu PM memulai project
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import { reactive, ref } from 'vue'
import axios from 'axios'
import { priorityClass, formatDateShort, isOverdue } from '@/composables/useFormatters'
import { useToast } from '@/composables/useToast'
import PriorityBadge from '@/Components/PriorityBadge.vue'
import UserAvatar from '@/Components/UserAvatar.vue'

const props = defineProps({ columns: Object, projects: Array, filters: Object })

const authUser = usePage().props.auth.user

// Local mutable state for optimistic UI
const localColumns = reactive({
    todo:        [...(props.columns.todo        ?? [])],
    in_progress: [...(props.columns.in_progress ?? [])],
    review:      [...(props.columns.review      ?? [])],
    done:        [...(props.columns.done        ?? [])],
})

const columnList = [
    { key: 'todo',        label: 'Todo',        dot: 'bg-ink-400',     borderActive: 'border-ink-400'     },
    { key: 'in_progress', label: 'In Progress', dot: 'bg-sage-500',    borderActive: 'border-sage-500'    },
    { key: 'review',      label: 'Review',      dot: 'bg-violet-500',  borderActive: 'border-violet-500'  },
    { key: 'done',        label: 'Done',        dot: 'bg-emerald-500', borderActive: 'border-emerald-500' },
]

const dragging    = ref(null)
const dragFromCol = ref(null)
const dragOverCol = ref(null)
const selectedProject = ref(props.filters?.project_id ?? '')
const { success: toastSuccess, error: toastError } = useToast()

// Hanya task Todo milik user sendiri (sebagai assignee) yang bisa di-drag ke In Progress
// dan project harus sudah dimulai (bukan planning)
const isDraggable = (task, colKey) =>
    colKey === 'todo' &&
    task.assigned_to === authUser.id &&
    task.project?.status !== 'planning'

const onDragStart = (task, colKey) => {
    dragging.value    = task
    dragFromCol.value = colKey
}

const onDrop = async (toCol) => {
    dragOverCol.value = null
    if (!dragging.value || dragFromCol.value === toCol) {
        dragging.value = null
        return
    }

    const task    = dragging.value
    const fromCol = dragFromCol.value
    dragging.value = null

    // Validasi: satu-satunya drag yang valid adalah todo → in_progress oleh assignee
    if (fromCol !== 'todo' || toCol !== 'in_progress') {
        const messages = {
            review: 'Submit ke Review wajib lewat halaman detail task dan lampirkan bukti pekerjaan.',
            done:   'Status Done hanya bisa ditetapkan oleh Project Manager setelah approve review.',
        }
        toastError(messages[toCol] ?? 'Perubahan status ini harus dilakukan lewat halaman detail task.')
        return
    }

    if (task.assigned_to !== authUser.id) {
        toastError('Kamu bukan assignee task ini, tidak bisa mengubah statusnya.')
        return
    }

    // Optimistic update
    localColumns[fromCol] = localColumns[fromCol].filter(t => t.id !== task.id)
    localColumns[toCol].unshift({ ...task, status: toCol })

    try {
        await axios.patch(`/tasks/${task.id}/status`, { status: toCol })
        toastSuccess('Task dimulai, status berubah ke In Progress.')
    } catch (err) {
        // Rollback on error
        localColumns[toCol] = localColumns[toCol].filter(t => t.id !== task.id)
        localColumns[fromCol].unshift(task)
        const msg = err.response?.status === 403
            ? 'Kamu tidak punya akses untuk mengubah status task ini.'
            : 'Gagal mengubah status, coba lagi.'
        toastError(msg)
    }
}

const applyFilter = () => {
    router.get('/tasks/kanban',
        { project_id: selectedProject.value || undefined },
        { preserveState: false, replace: true }
    )
}

</script>
