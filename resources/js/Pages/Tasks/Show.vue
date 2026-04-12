<template>
    <AppLayout>

        <!-- ── Page header ─────────────────────────────────────────── -->
        <div class="mb-7">
            <Link href="/tasks" class="inline-flex items-center gap-1.5 text-xs font-medium text-ink-400 dark:text-sage-600 hover:text-ink-700 dark:hover:text-sage-300 transition mb-4">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg>
                Kembali ke Tasks
            </Link>

            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="min-w-0">
                    <h1 class="text-2xl sm:text-3xl font-bold text-ink-900 dark:text-sage-100 tracking-tight leading-tight">{{ task.title }}</h1>
                    <div class="flex flex-wrap items-center gap-2 mt-3">
                        <TaskStatusBadge :status="task.status" />
                        <PriorityBadge :priority="task.priority" />
                        <span class="text-ink-300 dark:text-sage-700 text-xs">·</span>
                        <Link :href="`/projects/${task.project.id}`" class="text-xs text-ink-400 dark:text-sage-500 hover:text-ink-700 dark:hover:text-sage-300 transition font-medium">{{ task.project.name }}</Link>
                        <template v-if="task.due_date">
                            <span class="text-ink-300 dark:text-sage-700 text-xs">·</span>
                            <span class="text-xs text-ink-400 dark:text-sage-500">Due {{ formatDate(task.due_date) }}</span>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── Content grid ────────────────────────────────────────── -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Main column (description + proof + comments) -->
            <div class="lg:col-span-2 space-y-5 order-2 lg:order-1">

                <!-- Description -->
                <div class="bg-white dark:bg-ink-900 rounded-2xl border border-ink-900/6 dark:border-white/5 p-6">
                    <h2 class="text-xs font-semibold text-ink-400 dark:text-sage-600 uppercase tracking-widest mb-3">Deskripsi</h2>
                    <p v-if="task.description" class="text-sm text-ink-600 dark:text-sage-400 leading-relaxed whitespace-pre-wrap">{{ task.description }}</p>
                    <p v-else class="text-sm text-ink-300 dark:text-sage-700 italic">Tidak ada deskripsi.</p>
                </div>

                <!-- Bukti Pekerjaan (tampil kalau ada) -->
                <div v-if="task.proof || task.proof_file" class="bg-white dark:bg-ink-900 rounded-2xl border border-violet-200 dark:border-violet-400/15 p-6">
                    <h2 class="text-xs font-semibold text-violet-500 dark:text-violet-400 uppercase tracking-widest mb-3">Bukti Pekerjaan</h2>
                    <p v-if="task.proof" class="text-sm text-ink-600 dark:text-sage-400 whitespace-pre-wrap leading-relaxed">{{ task.proof }}</p>
                    <a v-if="task.proof_file" :href="`/storage/${task.proof_file}`" target="_blank" rel="noopener noreferrer"
                        class="inline-flex items-center gap-1.5 text-xs font-medium text-violet-700 dark:text-violet-300 bg-violet-50 dark:bg-violet-400/10 hover:bg-violet-100 dark:hover:bg-violet-400/20 px-3 py-1.5 rounded-lg transition mt-2">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                        Download File Bukti
                    </a>
                </div>

                <!-- Comments -->
                <div class="bg-white dark:bg-ink-900 rounded-2xl border border-ink-900/6 dark:border-white/5 overflow-hidden">
                    <div class="px-6 py-4 border-b border-ink-50 dark:border-white/4">
                        <h2 class="text-xs font-semibold text-ink-400 dark:text-sage-600 uppercase tracking-widest">Komentar</h2>
                    </div>

                    <!-- Comment list -->
                    <div class="divide-y divide-ink-50 dark:divide-white/4">
                        <div v-if="!task.comments?.length" class="px-6 py-8 text-center text-sm text-ink-300 dark:text-sage-700">Belum ada komentar.</div>
                        <div v-for="comment in task.comments" :key="comment.id" class="px-6 py-4 flex items-start gap-3.5 group">
                            <UserAvatar :name="comment.user.name" class="bg-ink-100 dark:bg-ink-800 text-ink-500 dark:text-sage-400 mt-0.5" />
                            <div class="flex-1 min-w-0">
                                <div class="flex items-baseline justify-between gap-2">
                                    <span class="text-sm font-semibold text-ink-800 dark:text-sage-200">{{ comment.user.name }}</span>
                                    <span class="text-[11px] text-ink-300 dark:text-sage-700 shrink-0">{{ formatDateTime(comment.created_at) }}</span>
                                </div>
                                <p class="text-sm text-ink-600 dark:text-sage-400 mt-1 leading-relaxed">{{ comment.body }}</p>
                            </div>
                            <button
                                v-if="comment.user.id === $page.props.auth.user.id || $page.props.auth.user.role === 'admin'"
                                @click="deleteComment(comment.id)"
                                class="text-[11px] text-ink-200 dark:text-sage-700 hover:text-red-400 dark:hover:text-red-400 transition opacity-0 group-hover:opacity-100 shrink-0 mt-1">
                                hapus
                            </button>
                        </div>
                    </div>

                    <!-- Comment form -->
                    <div class="px-6 py-4 bg-ink-50/50 dark:bg-white/2 border-t border-ink-50 dark:border-white/4">
                        <form @submit.prevent="submitComment" class="flex items-end gap-3">
                            <textarea
                                v-model="commentForm.body" rows="2" placeholder="Tulis komentar..."
                                class="flex-1 bg-white dark:bg-ink-800 border border-ink-200 dark:border-white/8 rounded-xl px-3.5 py-2.5 text-sm text-ink-900 dark:text-sage-200 placeholder-ink-300 dark:placeholder-ink-600 focus:outline-none focus:ring-2 focus:ring-ink-900/10 dark:focus:ring-sage-300/15 resize-none transition"
                                :class="{ 'border-red-400': commentForm.errors.body }"
                            ></textarea>
                            <button type="submit" :disabled="commentForm.processing || !commentForm.body.trim()"
                                class="shrink-0 bg-ink-900 dark:bg-sage-300 text-white dark:text-ink-900 text-sm px-4 py-2.5 rounded-xl font-semibold transition hover:bg-ink-700 dark:hover:bg-sage-200 disabled:opacity-40">
                                Kirim
                            </button>
                        </form>
                        <p v-if="commentForm.errors.body" class="text-xs text-red-500 mt-1.5">{{ commentForm.errors.body }}</p>
                    </div>
                </div>

            </div>

            <!-- Sidebar (action + meta + activity) -->
            <div class="space-y-5 order-1 lg:order-2">

                <!-- Status Action Panel -->
                <div v-if="is_assignee || is_pm" class="bg-white dark:bg-ink-900 rounded-2xl border border-ink-900/6 dark:border-white/5 p-5">
                    <h2 class="text-xs font-semibold text-ink-400 dark:text-sage-600 uppercase tracking-widest mb-4">Status Task</h2>

                    <!-- DONE -->
                    <div v-if="task.status === 'done'" class="flex items-center gap-2 text-emerald-600 dark:text-emerald-400">
                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span class="text-sm font-semibold">Task selesai.</span>
                    </div>

                    <!-- PM: approve/reject saat review -->
                    <template v-else-if="is_pm">
                        <div v-if="task.status === 'review'" class="space-y-4">
                            <p class="text-sm text-ink-500 dark:text-sage-500">Developer mengajukan review. Periksa bukti lalu setujui atau tolak.</p>

                            <!-- Bukti inline untuk PM -->
                            <div v-if="task.proof || task.proof_file" class="rounded-xl bg-violet-50 dark:bg-violet-400/8 border border-violet-100 dark:border-violet-400/15 p-3.5 space-y-2">
                                <p class="text-[11px] font-semibold text-violet-600 dark:text-violet-400 uppercase tracking-wide">Bukti Developer</p>
                                <template v-if="task.proof">
                                    <p class="text-sm text-ink-700 dark:text-sage-300 whitespace-pre-wrap leading-relaxed">{{ task.proof }}</p>
                                    <a v-if="isUrl(task.proof)" :href="task.proof" target="_blank" rel="noopener noreferrer"
                                        class="inline-flex items-center gap-1 text-xs text-violet-600 dark:text-violet-400 hover:underline">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" /></svg>
                                        Buka link
                                    </a>
                                </template>
                                <a v-if="task.proof_file" :href="`/storage/${task.proof_file}`" target="_blank" rel="noopener noreferrer"
                                    class="inline-flex items-center gap-1.5 text-xs font-medium text-violet-700 dark:text-violet-300 bg-violet-100 dark:bg-violet-400/15 hover:bg-violet-200 dark:hover:bg-violet-400/25 px-2.5 py-1 rounded-lg transition">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                                    Download File
                                </a>
                            </div>
                            <div v-else class="rounded-xl bg-amber-50 dark:bg-amber-400/8 border border-amber-100 dark:border-amber-400/15 p-3">
                                <p class="text-xs text-amber-600 dark:text-amber-400">Belum ada bukti dilampirkan.</p>
                            </div>

                            <div class="flex gap-2 pt-1">
                                <button @click="approveTask" :disabled="approveForm.processing"
                                    class="flex-1 flex items-center justify-center gap-1.5 py-2 rounded-xl text-sm font-semibold bg-emerald-600 hover:bg-emerald-700 text-white transition disabled:opacity-50">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                    Setujui
                                </button>
                                <button @click="showRejectModal = true"
                                    class="flex-1 flex items-center justify-center gap-1.5 py-2 rounded-xl text-sm font-semibold bg-red-500 hover:bg-red-600 text-white transition">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                    Tolak
                                </button>
                            </div>
                        </div>
                        <p v-else class="text-sm text-ink-400 dark:text-sage-600 italic">Perubahan status adalah tanggung jawab developer.</p>
                    </template>

                    <!-- Developer actions -->
                    <template v-else-if="is_assignee">
                        <!-- todo → in_progress -->
                        <div v-if="task.status === 'todo'">
                            <p class="text-sm text-ink-500 dark:text-sage-500 mb-3">Task belum dimulai.</p>
                            <button @click="startTask" :disabled="statusForm.processing"
                                class="w-full flex items-center justify-center gap-1.5 py-2.5 rounded-xl text-sm font-semibold bg-ink-900 dark:bg-sage-300 text-white dark:text-ink-900 hover:bg-ink-700 dark:hover:bg-sage-200 transition disabled:opacity-50">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 010 1.972l-11.54 6.347c-.75.412-1.667-.13-1.667-.986V5.653z" /></svg>
                                Mulai Kerjakan
                            </button>
                        </div>

                        <!-- in_progress → review -->
                        <div v-else-if="task.status === 'in_progress'" class="space-y-3">
                            <!-- Rejection notice -->
                            <div v-if="task.rejection_reason" class="rounded-xl bg-red-50 dark:bg-red-400/8 border border-red-100 dark:border-red-400/15 p-3.5">
                                <p class="text-[11px] font-semibold text-red-600 dark:text-red-400 uppercase tracking-wide mb-1">Review Ditolak</p>
                                <p class="text-sm text-red-700 dark:text-red-300 leading-relaxed">{{ task.rejection_reason }}</p>
                            </div>

                            <p v-else class="text-sm text-ink-500 dark:text-sage-500">Sudah selesai? Submit ke review.</p>

                            <div v-if="!showProofForm">
                                <button @click="showProofForm = true"
                                    class="w-full flex items-center justify-center gap-1.5 py-2.5 rounded-xl text-sm font-semibold bg-violet-600 hover:bg-violet-700 text-white transition">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" /></svg>
                                    Submit ke Review
                                </button>
                            </div>

                            <div v-else class="space-y-3 bg-violet-50 dark:bg-violet-400/8 rounded-xl p-4 border border-violet-100 dark:border-violet-400/15">
                                <p class="text-[11px] font-semibold text-violet-600 dark:text-violet-400 uppercase tracking-wide">Bukti Pekerjaan <span class="text-red-400">*</span></p>
                                <p class="text-xs text-ink-400 dark:text-sage-600">Lampirkan URL / deskripsi hasil, atau upload file. Minimal salah satu.</p>
                                <textarea v-model="proofForm.proof" rows="3"
                                    placeholder="https://github.com/... atau deskripsi singkat hasil kerja"
                                    class="w-full bg-white dark:bg-ink-800 border border-ink-200 dark:border-white/8 rounded-xl px-3 py-2 text-sm text-ink-900 dark:text-sage-200 placeholder-ink-300 dark:placeholder-ink-600 focus:outline-none focus:ring-2 focus:ring-violet-500/20 resize-none transition"
                                    :class="{ 'border-red-400': proofForm.errors.proof }"
                                ></textarea>
                                <!-- File upload -->
                                <div>
                                    <p class="text-[11px] text-ink-400 dark:text-sage-600 mb-1">Upload file (opsional)</p>
                                    <input ref="proofFileInput" type="file"
                                        accept=".jpg,.jpeg,.png,.gif,.pdf,.zip"
                                        @change="proofForm.proof_file = $event.target.files[0]"
                                        class="block w-full text-xs text-ink-500 dark:text-sage-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-violet-100 file:text-violet-700 dark:file:bg-violet-400/15 dark:file:text-violet-300 hover:file:bg-violet-200 cursor-pointer"
                                    />
                                    <p class="text-[11px] text-ink-300 dark:text-sage-700 mt-1">JPG, PNG, PDF, ZIP · maks 10MB</p>
                                </div>
                                <p v-if="proofForm.errors.proof" class="text-xs text-red-500">{{ proofForm.errors.proof }}</p>
                                <p v-if="proofForm.errors.proof_file" class="text-xs text-red-500">{{ proofForm.errors.proof_file }}</p>
                                <div class="flex gap-2">
                                    <button @click="submitReview" :disabled="proofForm.processing || (!proofForm.proof.trim() && !proofForm.proof_file)"
                                        class="flex-1 py-2 rounded-xl text-sm font-semibold bg-violet-600 hover:bg-violet-700 text-white transition disabled:opacity-50">
                                        Kirim Review
                                    </button>
                                    <button @click="cancelProofForm"
                                        class="px-3 py-2 rounded-xl text-sm text-ink-500 dark:text-sage-500 hover:bg-ink-100 dark:hover:bg-white/5 transition">
                                        Batal
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Waiting for PM -->
                        <div v-else-if="task.status === 'review'" class="flex items-center gap-2 text-violet-600 dark:text-violet-400">
                            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <span class="text-sm font-semibold">Menunggu persetujuan PM.</span>
                        </div>
                    </template>
                </div>

                <!-- Task Meta -->
                <div class="bg-white dark:bg-ink-900 rounded-2xl border border-ink-900/6 dark:border-white/5 p-5">
                    <h2 class="text-xs font-semibold text-ink-400 dark:text-sage-600 uppercase tracking-widest mb-4">Detail</h2>
                    <div class="space-y-3 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-ink-400 dark:text-sage-600">Assignee</span>
                            <span class="font-medium text-ink-800 dark:text-sage-200">{{ task.assignee?.name ?? '—' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-ink-400 dark:text-sage-600">Creator</span>
                            <span class="text-ink-700 dark:text-sage-300">{{ task.creator?.name ?? '—' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-ink-400 dark:text-sage-600">Due Date</span>
                            <span class="text-ink-700 dark:text-sage-300">{{ task.due_date ? formatDate(task.due_date) : '—' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-ink-400 dark:text-sage-600">Project</span>
                            <Link :href="`/projects/${task.project.id}`" class="text-sage-600 dark:text-sage-400 hover:underline font-medium truncate max-w-[140px]">{{ task.project.name }}</Link>
                        </div>
                    </div>
                </div>

                <!-- Activity -->
                <div v-if="task.activities?.length" class="bg-white dark:bg-ink-900 rounded-2xl border border-ink-900/6 dark:border-white/5 p-5">
                    <h2 class="text-xs font-semibold text-ink-400 dark:text-sage-600 uppercase tracking-widest mb-4">Aktivitas</h2>
                    <div class="space-y-3">
                        <div v-for="activity in task.activities" :key="activity.id" class="flex items-start gap-2.5">
                            <UserAvatar :name="activity.user.name" size="xs" class="bg-ink-100 dark:bg-ink-800 text-ink-400 dark:text-sage-500 mt-0.5" />
                            <div>
                                <p class="text-xs text-ink-600 dark:text-sage-400 leading-snug"><span class="font-semibold text-ink-800 dark:text-sage-300">{{ activity.user.name }}</span> {{ activity.description }}</p>
                                <p class="text-[11px] text-ink-300 dark:text-sage-700 mt-0.5">{{ formatDateTime(activity.created_at) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- ── Reject Modal ─────────────────────────────────────────── -->
        <Teleport to="body">
            <div v-if="showRejectModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-black/40 dark:bg-black/60 backdrop-blur-sm" @click="closeRejectModal"></div>
                <div class="relative bg-white dark:bg-ink-900 rounded-2xl shadow-xl w-full max-w-md border border-ink-900/8 dark:border-white/6 p-6 space-y-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h3 class="text-base font-bold text-ink-900 dark:text-sage-200">Tolak Review</h3>
                            <p class="text-sm text-ink-500 dark:text-sage-500 mt-0.5">Berikan alasan agar developer bisa memperbaiki pekerjaan.</p>
                        </div>
                        <button @click="closeRejectModal" class="text-ink-300 hover:text-ink-600 dark:text-sage-600 dark:hover:text-sage-300 transition mt-0.5">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                    <textarea v-model="rejectForm.rejection_reason" rows="4"
                        placeholder="Contoh: Implementasi belum sesuai spesifikasi, harap perbaiki validasi form..."
                        class="w-full bg-ink-50 dark:bg-ink-800 border border-ink-200 dark:border-white/8 rounded-xl px-3.5 py-3 text-sm text-ink-900 dark:text-sage-200 placeholder-ink-300 dark:placeholder-ink-600 focus:outline-none focus:ring-2 focus:ring-red-500/20 resize-none transition"
                        :class="{ 'border-red-400': rejectForm.errors.rejection_reason }"
                    ></textarea>
                    <p v-if="rejectForm.errors.rejection_reason" class="text-xs text-red-500">{{ rejectForm.errors.rejection_reason }}</p>
                    <div class="flex gap-2 justify-end">
                        <button @click="closeRejectModal" class="px-4 py-2 rounded-xl text-sm font-medium text-ink-500 dark:text-sage-500 hover:bg-ink-100 dark:hover:bg-white/5 transition">Batal</button>
                        <button @click="rejectTask" :disabled="rejectForm.processing || !rejectForm.rejection_reason.trim()"
                            class="flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold bg-red-500 hover:bg-red-600 text-white transition disabled:opacity-50">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                            Tolak & Kembalikan
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>

    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link, useForm, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import { formatDate, formatDateTime } from '@/composables/useFormatters'
import TaskStatusBadge from '@/Components/TaskStatusBadge.vue'
import PriorityBadge from '@/Components/PriorityBadge.vue'
import UserAvatar from '@/Components/UserAvatar.vue'

const props = defineProps({ task: Object, is_assignee: Boolean, is_pm: Boolean })

const showProofForm   = ref(false)
const showRejectModal = ref(false)
const proofFileInput  = ref(null)
const commentForm     = useForm({ body: '' })
const statusForm      = useForm({ status: '' })
const proofForm       = useForm({ status: 'review', proof: '', proof_file: null })
const approveForm     = useForm({})
const rejectForm      = useForm({ rejection_reason: '' })

const submitComment = () => commentForm.post(`/tasks/${props.task.id}/comments`, { onSuccess: () => commentForm.reset() })
const deleteComment = id => router.delete(`/comments/${id}`)

const startTask    = () => { statusForm.status = 'in_progress'; statusForm.patch(`/tasks/${props.task.id}/status`) }
const submitReview = () => {
    if (!proofForm.proof.trim() && !proofForm.proof_file) return
    proofForm.patch(`/tasks/${props.task.id}/status`, { onSuccess: () => { showProofForm.value = false } })
}
const cancelProofForm = () => {
    showProofForm.value = false
    proofForm.reset()
    if (proofFileInput.value) proofFileInput.value.value = ''
}
const approveTask = () => approveForm.post(`/tasks/${props.task.id}/approve`)
const rejectTask  = () => {
    if (!rejectForm.rejection_reason.trim()) return
    rejectForm.post(`/tasks/${props.task.id}/reject`, { onSuccess: () => closeRejectModal() })
}
const closeRejectModal = () => { showRejectModal.value = false; rejectForm.reset() }

const isUrl = str => { try { const u = new URL(str.trim()); return u.protocol === 'http:' || u.protocol === 'https:' } catch { return false } }
</script>
