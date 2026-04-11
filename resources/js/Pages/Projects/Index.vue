<template>
    <AppLayout>
        <div class="space-y-5">

            <!-- Header -->
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Projects</h1>
                <p class="text-sm text-gray-500 mt-1">Semua project yang tersedia</p>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <div class="flex flex-wrap gap-3">
                    <input
                        v-model="form.search"
                        type="text"
                        placeholder="Cari project..."
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-56"
                        @keyup.enter="applyFilter"
                    />
                    <select
                        v-model="form.status"
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        @change="applyFilter"
                    >
                        <option value="">Semua Status</option>
                        <option value="planning">Planning</option>
                        <option value="in_progress">In Progress</option>
                        <option value="on_hold">On Hold</option>
                        <option value="completed">Completed</option>
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
                    <button
                        v-if="hasFilter"
                        @click="resetFilter"
                        class="text-sm text-gray-500 hover:text-gray-700 px-3 py-2 rounded-lg hover:bg-gray-50 transition"
                    >
                        Reset
                    </button>
                </div>
            </div>

            <!-- Project Grid -->
            <div v-if="projects.data.length === 0" class="bg-white rounded-xl shadow-sm border border-gray-100 px-5 py-12 text-center text-gray-400 text-sm">
                Tidak ada project ditemukan.
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                <Link
                    v-for="project in projects.data"
                    :key="project.id"
                    :href="`/projects/${project.id}`"
                    class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md hover:border-blue-200 transition block"
                >
                    <div class="flex items-start justify-between gap-2 mb-3">
                        <h3 class="font-semibold text-gray-800 text-sm leading-snug">{{ project.name }}</h3>
                        <span :class="priorityClass(project.priority)" class="text-xs px-2 py-0.5 rounded-full font-medium shrink-0">
                            {{ project.priority }}
                        </span>
                    </div>

                    <p v-if="project.description" class="text-xs text-gray-500 mb-3 line-clamp-2">
                        {{ project.description }}
                    </p>

                    <div class="flex items-center gap-2 mb-4">
                        <span :class="statusClass(project.status)" class="text-xs px-2 py-0.5 rounded-full font-medium">
                            {{ statusLabel(project.status) }}
                        </span>
                        <span class="text-xs text-gray-400">oleh {{ project.creator.name }}</span>
                    </div>

                    <!-- Progress -->
                    <div>
                        <div class="flex justify-between text-xs text-gray-400 mb-1">
                            <span>{{ project.tasks_done_count }}/{{ project.tasks_count }} task selesai</span>
                            <span>{{ project.progress }}%</span>
                        </div>
                        <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                            <div
                                class="h-full bg-blue-500 rounded-full"
                                :style="{ width: project.progress + '%' }"
                            ></div>
                        </div>
                    </div>

                    <!-- Members -->
                    <div v-if="project.members?.length" class="mt-3 flex items-center gap-1">
                        <div
                            v-for="(member, i) in project.members.slice(0, 4)"
                            :key="member.id"
                            class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 text-xs font-medium flex items-center justify-center -ml-1 first:ml-0 border border-white"
                            :title="member.name"
                        >
                            {{ member.name.charAt(0).toUpperCase() }}
                        </div>
                        <span v-if="project.members.length > 4" class="text-xs text-gray-400 ml-1">
                            +{{ project.members.length - 4 }}
                        </span>
                    </div>
                </Link>
            </div>

            <!-- Pagination -->
            <div v-if="projects.last_page > 1" class="flex items-center justify-center gap-1">
                <Link
                    v-for="link in projects.links"
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
    projects: Object,
    filters: Object,
})

const form = reactive({
    search: props.filters?.search ?? '',
    status: props.filters?.status ?? '',
    priority: props.filters?.priority ?? '',
})

const hasFilter = computed(() => form.search || form.status || form.priority)

const applyFilter = () => {
    router.get('/projects', {
        search: form.search || undefined,
        status: form.status || undefined,
        priority: form.priority || undefined,
    }, { preserveState: true, replace: true })
}

const resetFilter = () => {
    form.search = ''
    form.status = ''
    form.priority = ''
    router.get('/projects', {}, { preserveState: false, replace: true })
}

const statusLabel = (s) => ({
    planning: 'Planning',
    in_progress: 'In Progress',
    on_hold: 'On Hold',
    completed: 'Completed',
}[s] ?? s)

const statusClass = (s) => ({
    planning: 'bg-gray-100 text-gray-600',
    in_progress: 'bg-blue-100 text-blue-600',
    on_hold: 'bg-yellow-100 text-yellow-600',
    completed: 'bg-green-100 text-green-600',
}[s] ?? 'bg-gray-100 text-gray-600')

const priorityClass = (p) => ({
    low: 'bg-green-100 text-green-600',
    medium: 'bg-yellow-100 text-yellow-600',
    high: 'bg-red-100 text-red-600',
}[p] ?? 'bg-gray-100 text-gray-600')
</script>
