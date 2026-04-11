<template>
    <AppLayout>
        <div class="space-y-6">

            <!-- Back + Header -->
            <div>
                <Link href="/tasks" class="text-sm text-gray-400 hover:text-gray-600 transition">&larr; Kembali ke Tasks</Link>
                <div class="flex flex-wrap items-start justify-between gap-3 mt-3">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">{{ task.title }}</h1>
                        <p class="text-sm text-gray-500 mt-1">
                            <Link :href="`/projects/${task.project.id}`" class="hover:underline text-blue-500">{{ task.project.name }}</Link>
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span :class="priorityClass(task.priority)" class="text-xs px-3 py-1 rounded-full font-medium">
                            {{ task.priority }}
                        </span>
                        <span :class="taskStatusClass(task.status)" class="text-xs px-3 py-1 rounded-full font-medium">
                            {{ taskStatusLabel(task.status) }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Left: Description + Comments + Activity -->
                <div class="lg:col-span-2 space-y-5">

                    <!-- Description -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                        <h2 class="text-sm font-semibold text-gray-700 mb-2">Deskripsi</h2>
                        <p v-if="task.description" class="text-sm text-gray-600 leading-relaxed whitespace-pre-wrap">{{ task.description }}</p>
                        <p v-else class="text-sm text-gray-400 italic">Tidak ada deskripsi.</p>
                    </div>

                    <!-- Update Status -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                        <h2 class="text-sm font-semibold text-gray-700 mb-3">Update Status</h2>
                        <div class="flex flex-wrap gap-2">
                            <button
                                v-for="s in ['todo', 'in_progress', 'review', 'done']"
                                :key="s"
                                @click="updateStatus(s)"
                                :disabled="statusForm.processing || task.status === s"
                                class="text-xs px-3 py-1.5 rounded-lg border font-medium transition"
                                :class="task.status === s
                                    ? taskStatusClassSolid(s) + ' cursor-default'
                                    : 'border-gray-200 text-gray-600 hover:border-blue-300 hover:text-blue-600 disabled:opacity-40'"
                            >
                                {{ taskStatusLabel(s) }}
                            </button>
                        </div>
                    </div>

                    <!-- Comments -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                        <div class="px-5 py-4 border-b border-gray-100">
                            <h2 class="font-semibold text-gray-800">Komentar</h2>
                        </div>

                        <!-- Comment list -->
                        <div class="divide-y divide-gray-50">
                            <div v-if="!task.comments?.length" class="px-5 py-6 text-center text-sm text-gray-400">
                                Belum ada komentar.
                            </div>
                            <div v-for="comment in task.comments" :key="comment.id" class="px-5 py-4">
                                <div class="flex items-start gap-3">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 text-sm font-medium flex items-center justify-center shrink-0">
                                        {{ comment.user.name.charAt(0).toUpperCase() }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="text-sm font-medium text-gray-700">{{ comment.user.name }}</span>
                                            <span class="text-xs text-gray-400">{{ formatDateTime(comment.created_at) }}</span>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1 leading-relaxed">{{ comment.body }}</p>
                                    </div>
                                    <!-- Delete comment (own or admin) -->
                                    <button
                                        v-if="comment.user.id === $page.props.auth.user.id || $page.props.auth.user.role === 'admin'"
                                        @click="deleteComment(comment.id)"
                                        class="text-xs text-red-400 hover:text-red-600 transition shrink-0"
                                    >
                                        hapus
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Add comment form -->
                        <div class="px-5 py-4 border-t border-gray-100">
                            <form @submit.prevent="submitComment">
                                <textarea
                                    v-model="commentForm.body"
                                    placeholder="Tulis komentar..."
                                    rows="3"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
                                    :class="{ 'border-red-400': commentForm.errors.body }"
                                ></textarea>
                                <p v-if="commentForm.errors.body" class="text-xs text-red-500 mt-1">{{ commentForm.errors.body }}</p>
                                <div class="flex justify-end mt-2">
                                    <button
                                        type="submit"
                                        :disabled="commentForm.processing || !commentForm.body.trim()"
                                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg transition disabled:opacity-50"
                                    >
                                        Kirim
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Activity -->
                    <div v-if="task.activities?.length" class="bg-white rounded-xl shadow-sm border border-gray-100">
                        <div class="px-5 py-4 border-b border-gray-100">
                            <h2 class="font-semibold text-gray-800">Aktivitas</h2>
                        </div>
                        <div class="divide-y divide-gray-50">
                            <div v-for="activity in task.activities" :key="activity.id" class="px-5 py-3">
                                <div class="flex items-start gap-3">
                                    <div class="w-7 h-7 rounded-full bg-gray-100 text-gray-500 text-xs font-medium flex items-center justify-center shrink-0 mt-0.5">
                                        {{ activity.user.name.charAt(0).toUpperCase() }}
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">
                                            <span class="font-medium text-gray-700">{{ activity.user.name }}</span>
                                            {{ activity.description }}
                                        </p>
                                        <p class="text-xs text-gray-400 mt-0.5">{{ formatDateTime(activity.created_at) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Right: Meta -->
                <div class="space-y-5">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 space-y-3">
                        <h2 class="text-sm font-semibold text-gray-700">Detail Task</h2>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-400">Assignee</span>
                                <span class="text-gray-700 font-medium">{{ task.assignee?.name ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Creator</span>
                                <span class="text-gray-700">{{ task.creator?.name ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Due Date</span>
                                <span class="text-gray-700">{{ task.due_date ? formatDate(task.due_date) : '-' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Project</span>
                                <Link :href="`/projects/${task.project.id}`" class="text-blue-500 hover:underline">
                                    {{ task.project.name }}
                                </Link>
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
import { Link, useForm, router } from '@inertiajs/vue3'

const props = defineProps({
    task: Object,
})

const commentForm = useForm({ body: '' })
const statusForm = useForm({ status: '' })

const submitComment = () => {
    commentForm.post(`/tasks/${props.task.id}/comments`, {
        onSuccess: () => commentForm.reset(),
    })
}

const deleteComment = (id) => {
    router.delete(`/comments/${id}`)
}

const updateStatus = (status) => {
    statusForm.status = status
    statusForm.patch(`/tasks/${props.task.id}/status`)
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

const taskStatusClassSolid = (s) => ({
    todo: 'bg-gray-600 text-white border-gray-600',
    in_progress: 'bg-blue-600 text-white border-blue-600',
    review: 'bg-purple-600 text-white border-purple-600',
    done: 'bg-green-600 text-white border-green-600',
}[s] ?? 'bg-gray-600 text-white border-gray-600')

const priorityClass = (p) => ({
    low: 'bg-green-100 text-green-600',
    medium: 'bg-yellow-100 text-yellow-600',
    high: 'bg-red-100 text-red-600',
}[p] ?? 'bg-gray-100 text-gray-600')

const formatDate = (d) => {
    if (!d) return '-'
    return new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}

const formatDateTime = (d) => {
    if (!d) return '-'
    return new Date(d).toLocaleString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })
}
</script>
