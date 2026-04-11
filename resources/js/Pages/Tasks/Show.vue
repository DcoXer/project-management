<template>
    <AppLayout>
        <div class="space-y-6">

            <div>
                <Link href="/tasks" class="text-sm text-ink-400 dark:text-sage-500 hover:text-ink-700 dark:hover:text-sage-300 transition">&larr; Kembali ke Tasks</Link>
                <div class="flex flex-wrap items-start justify-between gap-3 mt-3">
                    <div>
                        <h1 class="text-2xl font-bold text-ink-900 dark:text-sage-200 tracking-tight">{{ task.title }}</h1>
                        <Link :href="`/projects/${task.project.id}`" class="text-sm text-sage-600 dark:text-sage-400 hover:underline mt-1 inline-block">{{ task.project.name }}</Link>
                    </div>
                    <div class="flex items-center gap-2">
                        <span :class="priorityClass(task.priority)" class="text-xs px-3 py-1 rounded-full font-medium">{{ task.priority }}</span>
                        <span :class="taskStatusClass(task.status)" class="text-xs px-3 py-1 rounded-full font-medium">{{ taskStatusLabel(task.status) }}</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Left -->
                <div class="lg:col-span-2 space-y-5">

                    <!-- Description -->
                    <div class="bg-white dark:bg-ink-800 rounded-xl border border-ink-900/8 dark:border-sage-300/8 p-5 shadow-sm">
                        <h2 class="text-sm font-semibold text-ink-700 dark:text-sage-300 mb-2">Deskripsi</h2>
                        <p v-if="task.description" class="text-sm text-ink-600 dark:text-sage-400 leading-relaxed whitespace-pre-wrap">{{ task.description }}</p>
                        <p v-else class="text-sm text-ink-300 dark:text-sage-600 italic">Tidak ada deskripsi.</p>
                    </div>

                    <!-- Update Status -->
                    <div class="bg-white dark:bg-ink-800 rounded-xl border border-ink-900/8 dark:border-sage-300/8 p-5 shadow-sm">
                        <h2 class="text-sm font-semibold text-ink-700 dark:text-sage-300 mb-3">Update Status</h2>
                        <div class="flex flex-wrap gap-2">
                            <button
                                v-for="s in ['todo','in_progress','review','done']" :key="s"
                                @click="updateStatus(s)"
                                :disabled="statusForm.processing || task.status === s"
                                class="text-xs px-3 py-1.5 rounded-lg border font-medium transition"
                                :class="task.status === s
                                    ? taskStatusClassSolid(s)
                                    : 'border-ink-200 dark:border-ink-600 text-ink-500 dark:text-sage-500 hover:border-ink-900 dark:hover:border-sage-400 hover:text-ink-900 dark:hover:text-sage-300 disabled:opacity-40'"
                            >{{ taskStatusLabel(s) }}</button>
                        </div>
                    </div>

                    <!-- Comments -->
                    <div class="bg-white dark:bg-ink-800 rounded-xl border border-ink-900/8 dark:border-sage-300/8 shadow-sm">
                        <div class="px-5 py-4 border-b border-ink-100 dark:border-sage-300/8">
                            <h2 class="font-semibold text-ink-900 dark:text-sage-200">Komentar</h2>
                        </div>
                        <div class="divide-y divide-ink-50 dark:divide-sage-300/5">
                            <div v-if="!task.comments?.length" class="px-5 py-6 text-center text-sm text-ink-300 dark:text-sage-600">Belum ada komentar.</div>
                            <div v-for="comment in task.comments" :key="comment.id" class="px-5 py-4">
                                <div class="flex items-start gap-3">
                                    <div class="w-8 h-8 rounded-full bg-sage-200 dark:bg-sage-300/20 text-sage-700 dark:text-sage-300 text-sm font-semibold flex items-center justify-center shrink-0">{{ comment.user.name.charAt(0).toUpperCase() }}</div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="text-sm font-semibold text-ink-800 dark:text-sage-200">{{ comment.user.name }}</span>
                                            <span class="text-xs text-ink-300 dark:text-sage-600">{{ formatDateTime(comment.created_at) }}</span>
                                        </div>
                                        <p class="text-sm text-ink-600 dark:text-sage-400 mt-1 leading-relaxed">{{ comment.body }}</p>
                                    </div>
                                    <button
                                        v-if="comment.user.id === $page.props.auth.user.id || $page.props.auth.user.role === 'admin'"
                                        @click="deleteComment(comment.id)"
                                        class="text-xs text-red-400 hover:text-red-600 transition shrink-0"
                                    >hapus</button>
                                </div>
                            </div>
                        </div>
                        <div class="px-5 py-4 border-t border-ink-100 dark:border-sage-300/8">
                            <form @submit.prevent="submitComment">
                                <textarea
                                    v-model="commentForm.body" rows="3" placeholder="Tulis komentar..."
                                    class="w-full bg-sage-50 dark:bg-ink-850 border border-ink-200 dark:border-ink-600 rounded-lg px-3 py-2 text-sm text-ink-900 dark:text-sage-200 placeholder-ink-300 dark:placeholder-ink-500 focus:outline-none focus:ring-2 focus:ring-ink-900/15 dark:focus:ring-sage-300/20 resize-none transition"
                                    :class="{ 'border-red-400': commentForm.errors.body }"
                                ></textarea>
                                <p v-if="commentForm.errors.body" class="text-xs text-red-500 mt-1">{{ commentForm.errors.body }}</p>
                                <div class="flex justify-end mt-2">
                                    <button type="submit" :disabled="commentForm.processing || !commentForm.body.trim()" class="bg-ink-900 dark:bg-sage-300 text-sage-200 dark:text-ink-900 text-sm px-4 py-2 rounded-lg font-semibold transition hover:bg-ink-800 dark:hover:bg-sage-200 disabled:opacity-50">Kirim</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Activity -->
                    <div v-if="task.activities?.length" class="bg-white dark:bg-ink-800 rounded-xl border border-ink-900/8 dark:border-sage-300/8 shadow-sm">
                        <div class="px-5 py-4 border-b border-ink-100 dark:border-sage-300/8">
                            <h2 class="font-semibold text-ink-900 dark:text-sage-200">Aktivitas</h2>
                        </div>
                        <div class="divide-y divide-ink-50 dark:divide-sage-300/5">
                            <div v-for="activity in task.activities" :key="activity.id" class="px-5 py-3">
                                <div class="flex items-start gap-3">
                                    <div class="w-7 h-7 rounded-full bg-ink-100 dark:bg-ink-700 text-ink-500 dark:text-sage-400 text-xs font-semibold flex items-center justify-center shrink-0 mt-0.5">{{ activity.user.name.charAt(0).toUpperCase() }}</div>
                                    <div>
                                        <p class="text-sm text-ink-600 dark:text-sage-400"><span class="font-semibold text-ink-800 dark:text-sage-200">{{ activity.user.name }}</span> {{ activity.description }}</p>
                                        <p class="text-xs text-ink-300 dark:text-sage-600 mt-0.5">{{ formatDateTime(activity.created_at) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right -->
                <div class="space-y-5">
                    <div class="bg-white dark:bg-ink-800 rounded-xl border border-ink-900/8 dark:border-sage-300/8 p-5 shadow-sm space-y-3">
                        <h2 class="text-sm font-semibold text-ink-700 dark:text-sage-300">Detail Task</h2>
                        <div class="space-y-2.5 text-sm">
                            <div class="flex justify-between"><span class="text-ink-400 dark:text-sage-500">Assignee</span><span class="font-medium text-ink-800 dark:text-sage-200">{{ task.assignee?.name ?? '-' }}</span></div>
                            <div class="flex justify-between"><span class="text-ink-400 dark:text-sage-500">Creator</span><span class="text-ink-700 dark:text-sage-300">{{ task.creator?.name ?? '-' }}</span></div>
                            <div class="flex justify-between"><span class="text-ink-400 dark:text-sage-500">Due Date</span><span class="text-ink-700 dark:text-sage-300">{{ task.due_date ? formatDate(task.due_date) : '-' }}</span></div>
                            <div class="flex justify-between items-center"><span class="text-ink-400 dark:text-sage-500">Project</span><Link :href="`/projects/${task.project.id}`" class="text-sage-600 dark:text-sage-400 hover:underline truncate max-w-[140px]">{{ task.project.name }}</Link></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link, useForm, router, usePage } from '@inertiajs/vue3'

const props = defineProps({ task: Object })

const commentForm = useForm({ body: '' })
const statusForm  = useForm({ status: '' })

const submitComment  = () => commentForm.post(`/tasks/${props.task.id}/comments`, { onSuccess: () => commentForm.reset() })
const deleteComment  = id => router.delete(`/comments/${id}`)
const updateStatus   = s => { statusForm.status = s; statusForm.patch(`/tasks/${props.task.id}/status`) }

const taskStatusLabel    = s => ({ todo:'Todo', in_progress:'In Progress', review:'Review', done:'Done' }[s] ?? s)
const taskStatusClass    = s => ({ todo:'bg-ink-100 text-ink-600 dark:bg-ink-700/50 dark:text-ink-300', in_progress:'bg-sage-100 text-sage-700 dark:bg-sage-300/15 dark:text-sage-300', review:'bg-violet-100 text-violet-700 dark:bg-violet-300/15 dark:text-violet-300', done:'bg-emerald-100 text-emerald-700 dark:bg-emerald-300/15 dark:text-emerald-300' }[s] ?? 'bg-ink-100 text-ink-500')
const taskStatusClassSolid = s => ({ todo:'bg-ink-700 text-white border-ink-700', in_progress:'bg-sage-600 text-white border-sage-600', review:'bg-violet-600 text-white border-violet-600', done:'bg-emerald-600 text-white border-emerald-600' }[s] ?? 'bg-ink-700 text-white border-ink-700')
const priorityClass      = p => ({ low:'bg-sage-100 text-sage-700 dark:bg-sage-300/15 dark:text-sage-300', medium:'bg-amber-100 text-amber-700 dark:bg-amber-300/15 dark:text-amber-300', high:'bg-red-100 text-red-700 dark:bg-red-300/15 dark:text-red-300' }[p] ?? 'bg-ink-100 text-ink-500')
const formatDate         = d => d ? new Date(d).toLocaleDateString('id-ID', { day:'numeric', month:'short', year:'numeric' }) : '-'
const formatDateTime     = d => d ? new Date(d).toLocaleString('id-ID', { day:'numeric', month:'short', year:'numeric', hour:'2-digit', minute:'2-digit' }) : '-'
</script>
