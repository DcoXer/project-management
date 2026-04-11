<template>
    <AppLayout>
        <div class="space-y-5">

            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Notifikasi</h1>
                    <p class="text-sm text-gray-500 mt-1">
                        {{ $page.props.auth.user.unread_notifications_count }} belum dibaca
                    </p>
                </div>
                <button
                    v-if="$page.props.auth.user.unread_notifications_count > 0"
                    @click="markAllRead"
                    class="text-sm text-blue-600 hover:text-blue-700 font-medium px-4 py-2 rounded-lg hover:bg-blue-50 transition"
                >
                    Tandai semua dibaca
                </button>
            </div>

            <!-- Notifications List -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div v-if="notifications.data.length === 0" class="px-5 py-12 text-center text-sm text-gray-400">
                    Tidak ada notifikasi.
                </div>
                <div class="divide-y divide-gray-50">
                    <div
                        v-for="notif in notifications.data"
                        :key="notif.id"
                        class="flex items-start gap-4 px-5 py-4 transition"
                        :class="!notif.read_at ? 'bg-blue-50/40' : 'hover:bg-gray-50'"
                    >
                        <!-- Dot indicator -->
                        <div class="mt-1.5 shrink-0">
                            <div
                                class="w-2 h-2 rounded-full"
                                :class="!notif.read_at ? 'bg-blue-500' : 'bg-transparent'"
                            ></div>
                        </div>

                        <!-- Icon -->
                        <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0 text-sm"
                            :class="notifIconClass(notif.data.type)">
                            {{ notifIcon(notif.data.type) }}
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-700 leading-snug">{{ notif.data.message }}</p>
                            <div class="flex items-center gap-3 mt-1">
                                <Link
                                    v-if="notif.data.task_id"
                                    :href="`/tasks/${notif.data.task_id}`"
                                    class="text-xs text-blue-500 hover:underline"
                                >
                                    {{ notif.data.task_title }}
                                </Link>
                                <span
                                    v-if="notif.data.type === 'task_status_changed'"
                                    class="text-xs text-gray-400"
                                >
                                    {{ taskStatusLabel(notif.data.old_status) }} &rarr; {{ taskStatusLabel(notif.data.new_status) }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-400 mt-1">{{ formatDateTime(notif.created_at) }}</p>
                        </div>

                        <!-- Mark as read -->
                        <button
                            v-if="!notif.read_at"
                            @click="markRead(notif.id)"
                            class="shrink-0 text-xs text-gray-400 hover:text-blue-600 transition"
                            title="Tandai dibaca"
                        >
                            &#10003;
                        </button>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="notifications.last_page > 1" class="flex items-center justify-center gap-1">
                <Link
                    v-for="link in notifications.links"
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

const props = defineProps({
    notifications: Object,
})

const markRead = (id) => {
    router.patch(`/notifications/${id}/read`)
}

const markAllRead = () => {
    router.patch('/notifications/read-all')
}

const notifIcon = (type) => ({
    task_assigned: '📋',
    task_commented: '💬',
    task_status_changed: '🔄',
}[type] ?? '🔔')

const notifIconClass = (type) => ({
    task_assigned: 'bg-blue-100',
    task_commented: 'bg-purple-100',
    task_status_changed: 'bg-yellow-100',
}[type] ?? 'bg-gray-100')

const taskStatusLabel = (s) => ({
    todo: 'Todo',
    in_progress: 'In Progress',
    review: 'Review',
    done: 'Done',
}[s] ?? s)

const formatDateTime = (d) => {
    if (!d) return '-'
    return new Date(d).toLocaleString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })
}
</script>
