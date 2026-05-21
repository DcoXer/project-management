<template>
    <AppLayout>
        <div class="space-y-5">

            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-ink-900 dark:text-white tracking-tight">Notifikasi</h1>
                    <p class="text-sm text-ink-400 dark:text-white mt-1">{{ $page.props.auth.user.unread_notifications_count }} belum dibaca</p>
                </div>
                <button
                    v-if="$page.props.auth.user.unread_notifications_count > 0"
                    @click="markAllRead"
                    class="text-sm font-medium text-sage-700 dark:text-white hover:text-sage-800 dark:hover:text-white px-4 py-2 rounded-lg hover:bg-sage-100 dark:hover:bg-sage-300/8 transition"
                >Tandai semua dibaca</button>
            </div>

            <div class="bg-white dark:bg-ink-800 rounded-xl border border-ink-900/8 dark:border-sage-300/8 overflow-hidden shadow-sm">
                <div v-if="notifications.data.length === 0" class="px-5 py-12 text-center text-sm text-ink-300 dark:text-white">Tidak ada notifikasi.</div>
                <div class="divide-y divide-ink-50 dark:divide-sage-300/5">
                    <div
                        v-for="notif in notifications.data" :key="notif.id"
                        class="flex items-start gap-4 px-5 py-4 transition"
                        :class="!notif.read_at ? 'bg-sage-50 dark:bg-sage-300/4' : 'hover:bg-sage-50/50 dark:hover:bg-sage-300/3'"
                    >
                        <!-- Unread dot -->
                        <div class="mt-2 shrink-0 w-2 h-2 rounded-full" :class="!notif.read_at ? 'bg-ink-900 dark:bg-sage-300' : 'bg-transparent'"></div>

                        <!-- Icon -->
                        <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0 text-sm" :class="notifIconBg(notif.data.type)">
                            {{ notifIcon(notif.data.type) }}
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-ink-700 dark:text-white leading-snug">{{ notif.data.message }}</p>
                            <div class="flex items-center gap-3 mt-1">
                                <Link v-if="notif.data.task_id" :href="`/tasks/${notif.data.task_id}`" class="text-xs text-sage-600 dark:text-white hover:underline">{{ notif.data.task_title }}</Link>
                                <span v-if="notif.data.type === 'task_status_changed'" class="text-xs text-ink-300 dark:text-white">
                                    {{ taskStatusLabel(notif.data.old_status) }} &rarr; {{ taskStatusLabel(notif.data.new_status) }}
                                </span>
                            </div>
                            <p class="text-xs text-ink-300 dark:text-white mt-1">{{ formatDateTime(notif.created_at) }}</p>
                        </div>

                        <!-- Mark read -->
                        <button v-if="!notif.read_at" @click="markRead(notif.id)" class="shrink-0 text-xs text-ink-300 dark:text-white hover:text-ink-700 dark:hover:text-white transition" title="Tandai dibaca">&#10003;</button>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="notifications.last_page > 1" class="flex items-center justify-center gap-1">
                <Link
                    v-for="link in notifications.links" :key="link.label"
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
import { Link, router } from '@inertiajs/vue3'
import { taskStatusLabel, formatDateTime } from '@/composables/useFormatters'

defineProps({ notifications: Object })

const markRead    = id => router.patch(`/notifications/${id}/read`)
const markAllRead = ()  => router.patch('/notifications/read-all')

const notifIcon   = t => ({ task_assigned: '📋', task_commented: '💬', task_status_changed: '🔄', deadline_reminder: '⏰', task_overdue: '🚨' }[t] ?? '🔔')
const notifIconBg = t => ({
    task_assigned:       'bg-sage-100 dark:bg-sage-300/10',
    task_commented:      'bg-violet-100 dark:bg-violet-300/10',
    task_status_changed: 'bg-amber-100 dark:bg-amber-300/10',
    deadline_reminder:   'bg-amber-100 dark:bg-amber-300/10',
    task_overdue:        'bg-red-100 dark:bg-red-300/10',
}[t] ?? 'bg-ink-100 dark:bg-ink-700')
</script>
