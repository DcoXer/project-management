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

                    <!-- Status Action Panel: hanya tampil untuk assignee atau PM -->
                    <div v-if="is_assignee || is_pm" class="bg-white dark:bg-ink-800 rounded-xl border border-ink-900/8 dark:border-sage-300/8 p-5 shadow-sm">
                        <h2 class="text-sm font-semibold text-ink-700 dark:text-sage-300 mb-4">Status Task</h2>

                        <!-- DONE: terkunci -->
                        <div v-if="task.status === 'done'" class="flex items-center gap-2 text-emerald-600 dark:text-emerald-400">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <span class="text-sm font-semibold">Task selesai — status terkunci.</span>
                        </div>

                        <!-- PM: tampilkan approve/reject saat review -->
                        <template v-else-if="is_pm">
                            <div v-if="task.status === 'review'" class="space-y-4">
                                <p class="text-sm text-ink-500 dark:text-sage-500">Developer telah mengajukan review. Periksa bukti pekerjaan lalu setujui atau tolak.</p>

                                <!-- Bukti pekerjaan inline untuk PM -->
                                <div v-if="task.proof" class="rounded-xl border border-violet-200 dark:border-violet-400/20 bg-violet-50 dark:bg-violet-400/5 p-4 space-y-2">
                                    <p class="text-xs font-semibold text-violet-700 dark:text-violet-400 flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" /></svg>
                                        Bukti Pekerjaan dari Developer
                                    </p>
                                    <p class="text-sm text-ink-700 dark:text-sage-300 whitespace-pre-wrap leading-relaxed">{{ task.proof }}</p>
                                    <!-- Jika bukti berupa URL, tampilkan sebagai link -->
                                    <template v-if="isUrl(task.proof)">
                                        <a :href="task.proof" target="_blank" rel="noopener noreferrer"
                                            class="inline-flex items-center gap-1 text-xs text-violet-600 dark:text-violet-400 hover:underline mt-1">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" /></svg>
                                            Buka link
                                        </a>
                                    </template>
                                </div>
                                <div v-else class="rounded-xl border border-amber-200 dark:border-amber-400/20 bg-amber-50 dark:bg-amber-400/5 p-3">
                                    <p class="text-xs text-amber-700 dark:text-amber-400">Developer belum melampirkan bukti pekerjaan.</p>
                                </div>

                                <div class="flex gap-3 pt-1">
                                    <button @click="approveTask" :disabled="approveForm.processing"
                                        class="flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold bg-emerald-600 hover:bg-emerald-700 text-white transition disabled:opacity-50">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                        Setujui (Done)
                                    </button>
                                    <button @click="showRejectModal = true"
                                        class="flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold bg-red-600 hover:bg-red-700 text-white transition">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                        Tolak (Kembalikan)
                                    </button>
                                </div>
                            </div>
                            <p v-else class="text-sm text-ink-400 dark:text-sage-500 italic">Perubahan status task adalah tanggung jawab developer.</p>
                        </template>

                        <!-- DEVELOPER: tampilkan aksi sesuai status -->
                        <template v-else-if="is_assignee">
                            <!-- todo → in_progress -->
                            <div v-if="task.status === 'todo'">
                                <p class="text-sm text-ink-500 dark:text-sage-500 mb-3">Task belum dimulai. Klik tombol di bawah untuk mulai mengerjakan.</p>
                                <button @click="startTask" :disabled="statusForm.processing"
                                    class="flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold bg-sage-600 hover:bg-sage-700 text-white transition disabled:opacity-50">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 010 1.972l-11.54 6.347c-.75.412-1.667-.13-1.667-.986V5.653z" /></svg>
                                    Mulai Kerjakan
                                </button>
                            </div>

                            <!-- in_progress → review (wajib bukti) -->
                            <div v-else-if="task.status === 'in_progress'" class="space-y-3">
                                <p class="text-sm text-ink-500 dark:text-sage-500">Sudah selesai? Submit ke Review dengan melampirkan bukti pekerjaan.</p>
                                <div v-if="!showProofForm">
                                    <button @click="showProofForm = true"
                                        class="flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold bg-violet-600 hover:bg-violet-700 text-white transition">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" /></svg>
                                        Submit ke Review
                                    </button>
                                </div>
                                <div v-else class="space-y-3 border border-violet-200 dark:border-violet-400/20 rounded-xl p-4 bg-violet-50 dark:bg-violet-400/5">
                                    <label class="text-sm font-semibold text-ink-700 dark:text-sage-300">
                                        Bukti Pekerjaan <span class="text-red-500">*</span>
                                    </label>
                                    <p class="text-xs text-ink-400 dark:text-sage-500">Lampirkan URL (GitHub PR, deployment, test report) atau deskripsi singkat hasil pekerjaan.</p>
                                    <textarea v-model="proofForm.proof" rows="4"
                                        placeholder="Contoh: https://github.com/org/repo/pull/123&#10;atau: Fitur login sudah selesai diimplementasi, unit test lulus semua."
                                        class="w-full bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-lg px-3 py-2 text-sm text-ink-900 dark:text-sage-200 placeholder-ink-300 dark:placeholder-ink-500 focus:outline-none focus:ring-2 focus:ring-violet-500/30 resize-none transition"
                                        :class="{ 'border-red-400': proofForm.errors.proof }"
                                    ></textarea>
                                    <p v-if="proofForm.errors.proof" class="text-xs text-red-500">{{ proofForm.errors.proof }}</p>
                                    <div class="flex gap-2">
                                        <button @click="submitReview" :disabled="proofForm.processing || !proofForm.proof.trim()"
                                            class="px-4 py-2 rounded-lg text-sm font-semibold bg-violet-600 hover:bg-violet-700 text-white transition disabled:opacity-50">
                                            Kirim Review
                                        </button>
                                        <button @click="showProofForm = false; proofForm.reset()"
                                            class="px-4 py-2 rounded-lg text-sm font-medium text-ink-500 dark:text-sage-500 hover:bg-ink-100 dark:hover:bg-ink-700 transition">
                                            Batal
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- review: menunggu PM -->
                            <div v-else-if="task.status === 'review'" class="flex items-center gap-2 text-violet-600 dark:text-violet-400">
                                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                <span class="text-sm font-semibold">Menunggu persetujuan Project Manager.</span>
                            </div>

                            <!-- in_progress setelah ditolak: tampilkan alasan -->
                            <div v-if="task.status === 'in_progress' && task.rejection_reason"
                                class="mt-3 rounded-xl border border-red-200 dark:border-red-400/20 bg-red-50 dark:bg-red-400/5 p-4 space-y-1">
                                <p class="text-xs font-semibold text-red-700 dark:text-red-400 flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>
                                    Review Ditolak oleh Project Manager
                                </p>
                                <p class="text-sm text-red-700 dark:text-red-300 leading-relaxed">{{ task.rejection_reason }}</p>
                            </div>
                        </template>

                    </div>

                    <!-- Bukti Pekerjaan (tampil kalau ada) -->
                    <div v-if="task.proof" class="bg-white dark:bg-ink-800 rounded-xl border border-violet-200 dark:border-violet-400/20 p-5 shadow-sm">
                        <h2 class="text-sm font-semibold text-violet-700 dark:text-violet-400 mb-2 flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" /></svg>
                            Bukti Pekerjaan
                        </h2>
                        <p class="text-sm text-ink-600 dark:text-sage-400 whitespace-pre-wrap leading-relaxed">{{ task.proof }}</p>
                    </div>

                    <!-- Modal Reject -->
            <Teleport to="body">
                <div v-if="showRejectModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                    <div class="absolute inset-0 bg-black/40 dark:bg-black/60 backdrop-blur-sm" @click="closeRejectModal"></div>
                    <div class="relative bg-white dark:bg-ink-800 rounded-2xl shadow-xl w-full max-w-md border border-ink-900/8 dark:border-sage-300/8 p-6 space-y-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h3 class="text-base font-bold text-ink-900 dark:text-sage-200">Tolak Review</h3>
                                <p class="text-sm text-ink-500 dark:text-sage-500 mt-0.5">Berikan alasan penolakan agar developer bisa memperbaiki pekerjaan.</p>
                            </div>
                            <button @click="closeRejectModal" class="text-ink-400 hover:text-ink-600 dark:text-sage-500 dark:hover:text-sage-300 transition">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-ink-700 dark:text-sage-300">
                                Alasan Penolakan <span class="text-red-500">*</span>
                            </label>
                            <textarea v-model="rejectForm.rejection_reason" rows="4"
                                placeholder="Contoh: Implementasi belum sesuai spesifikasi, harap perbaiki validasi form login dan tambahkan error handling."
                                class="w-full bg-sage-50 dark:bg-ink-850 border border-ink-200 dark:border-ink-600 rounded-lg px-3 py-2 text-sm text-ink-900 dark:text-sage-200 placeholder-ink-300 dark:placeholder-ink-500 focus:outline-none focus:ring-2 focus:ring-red-500/30 resize-none transition"
                                :class="{ 'border-red-400': rejectForm.errors.rejection_reason }"
                            ></textarea>
                            <p v-if="rejectForm.errors.rejection_reason" class="text-xs text-red-500">{{ rejectForm.errors.rejection_reason }}</p>
                        </div>
                        <div class="flex gap-2 justify-end pt-1">
                            <button @click="closeRejectModal"
                                class="px-4 py-2 rounded-lg text-sm font-medium text-ink-500 dark:text-sage-500 hover:bg-ink-100 dark:hover:bg-ink-700 transition">
                                Batal
                            </button>
                            <button @click="rejectTask" :disabled="rejectForm.processing || !rejectForm.rejection_reason.trim()"
                                class="flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold bg-red-600 hover:bg-red-700 text-white transition disabled:opacity-50">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                Tolak & Kembalikan
                            </button>
                        </div>
                    </div>
                </div>
            </Teleport>

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
import { Link, useForm, router } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({ task: Object, is_assignee: Boolean, is_pm: Boolean })

const showProofForm   = ref(false)
const showRejectModal = ref(false)
const commentForm     = useForm({ body: '' })
const statusForm      = useForm({ status: '' })
const proofForm       = useForm({ status: 'review', proof: '' })
const approveForm     = useForm({})
const rejectForm      = useForm({ rejection_reason: '' })

const submitComment = () => commentForm.post(`/tasks/${props.task.id}/comments`, { onSuccess: () => commentForm.reset() })
const deleteComment = id => router.delete(`/comments/${id}`)

const startTask     = () => { statusForm.status = 'in_progress'; statusForm.patch(`/tasks/${props.task.id}/status`) }
const submitReview  = () => {
    if (!proofForm.proof.trim()) return
    proofForm.patch(`/tasks/${props.task.id}/status`, { onSuccess: () => { showProofForm.value = false } })
}
const approveTask = () => approveForm.post(`/tasks/${props.task.id}/approve`)
const rejectTask  = () => {
    if (!rejectForm.rejection_reason.trim()) return
    rejectForm.post(`/tasks/${props.task.id}/reject`, {
        onSuccess: () => closeRejectModal()
    })
}
const closeRejectModal = () => {
    showRejectModal.value = false
    rejectForm.reset()
}

const isUrl = str => { try { const u = new URL(str.trim()); return u.protocol === 'http:' || u.protocol === 'https:' } catch { return false } }
const taskStatusLabel = s => ({ todo:'Todo', in_progress:'In Progress', review:'Review', done:'Done' }[s] ?? s)
const taskStatusClass = s => ({ todo:'bg-ink-100 text-ink-600 dark:bg-ink-700/50 dark:text-ink-300', in_progress:'bg-sage-100 text-sage-700 dark:bg-sage-300/15 dark:text-sage-300', review:'bg-violet-100 text-violet-700 dark:bg-violet-300/15 dark:text-violet-300', done:'bg-emerald-100 text-emerald-700 dark:bg-emerald-300/15 dark:text-emerald-300' }[s] ?? 'bg-ink-100 text-ink-500')
const priorityClass   = p => ({ low:'bg-sage-100 text-sage-700 dark:bg-sage-300/15 dark:text-sage-300', medium:'bg-amber-100 text-amber-700 dark:bg-amber-300/15 dark:text-amber-300', high:'bg-red-100 text-red-700 dark:bg-red-300/15 dark:text-red-300' }[p] ?? 'bg-ink-100 text-ink-500')
const formatDate      = d => d ? new Date(d).toLocaleDateString('id-ID', { day:'numeric', month:'short', year:'numeric' }) : '-'
const formatDateTime  = d => d ? new Date(d).toLocaleString('id-ID', { day:'numeric', month:'short', year:'numeric', hour:'2-digit', minute:'2-digit' }) : '-'
</script>
