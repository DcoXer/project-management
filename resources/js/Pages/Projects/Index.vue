<template>
    <AppLayout>
        <div class="space-y-5">

            <div>
                <h1 class="text-2xl font-bold text-ink-900 dark:text-sage-200 tracking-tight">Projects</h1>
                <p class="text-sm text-ink-500 dark:text-sage-500 mt-1">Semua project yang tersedia</p>
            </div>

            <!-- Filters -->
            <div class="bg-white dark:bg-ink-800 rounded-xl border border-ink-900/8 dark:border-sage-300/8 p-4 shadow-sm">
                <div class="flex flex-wrap gap-3">
                    <input
                        v-model="form.search" type="text" placeholder="Cari project..."
                        @keyup.enter="applyFilter"
                        class="border border-ink-200 dark:border-ink-600 bg-sage-50 dark:bg-ink-850 text-ink-900 dark:text-sage-200 placeholder-ink-300 dark:placeholder-ink-500 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ink-900/15 dark:focus:ring-sage-300/20 w-52 transition"
                    />
                    <select v-model="form.status" @change="applyFilter" class="border border-ink-200 dark:border-ink-600 bg-sage-50 dark:bg-ink-850 text-ink-900 dark:text-sage-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ink-900/15 dark:focus:ring-sage-300/20 transition">
                        <option value="">Semua Status</option>
                        <option value="planning">Planning</option>
                        <option value="in_progress">In Progress</option>
                        <option value="on_hold">On Hold</option>
                        <option value="completed">Completed</option>
                    </select>
                    <select v-model="form.priority" @change="applyFilter" class="border border-ink-200 dark:border-ink-600 bg-sage-50 dark:bg-ink-850 text-ink-900 dark:text-sage-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ink-900/15 dark:focus:ring-sage-300/20 transition">
                        <option value="">Semua Priority</option>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                    </select>
                    <button v-if="hasFilter" @click="resetFilter" class="text-sm text-ink-500 dark:text-sage-500 hover:text-ink-800 dark:hover:text-sage-300 px-3 py-2 rounded-lg hover:bg-sage-100 dark:hover:bg-sage-300/8 transition">Reset</button>
                </div>
            </div>

            <!-- Empty -->
            <div v-if="projects.data.length === 0" class="bg-white dark:bg-ink-800 rounded-xl border border-ink-900/8 dark:border-sage-300/8 px-5 py-12 text-center text-ink-300 dark:text-sage-600 text-sm shadow-sm">
                Tidak ada project ditemukan.
            </div>

            <!-- Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                <Link
                    v-for="project in projects.data" :key="project.id"
                    :href="`/projects/${project.id}`"
                    class="bg-white dark:bg-ink-800 rounded-xl border border-ink-900/8 dark:border-sage-300/8 p-5 hover:shadow-md hover:border-sage-400/40 dark:hover:border-sage-300/20 transition block shadow-sm"
                >
                    <div class="flex items-start justify-between gap-2 mb-3">
                        <h3 class="font-semibold text-ink-900 dark:text-sage-200 text-sm leading-snug">{{ project.name }}</h3>
                        <PriorityBadge :priority="project.priority" sm class="shrink-0" />
                    </div>

                    <p v-if="project.description" class="text-xs text-ink-400 dark:text-sage-500 mb-3 line-clamp-2">{{ project.description }}</p>

                    <div class="flex items-center gap-2 mb-4">
                        <ProjectStatusBadge :status="project.status" sm />
                        <span class="text-xs text-ink-300 dark:text-sage-600">oleh {{ project.creator.name }}</span>
                    </div>

                    <div>
                        <div class="flex justify-between text-xs text-ink-400 dark:text-sage-500 mb-1">
                            <span>{{ project.tasks_done_count }}/{{ project.tasks_count }} selesai</span>
                            <span>{{ project.progress }}%</span>
                        </div>
                        <div class="h-1.5 bg-ink-100 dark:bg-ink-700 rounded-full overflow-hidden">
                            <div class="h-full bg-ink-900 dark:bg-sage-300 rounded-full" :style="{ width: project.progress + '%' }"></div>
                        </div>
                    </div>

                    <div v-if="project.members?.length" class="mt-3 flex items-center gap-1">
                        <div
                            v-for="member in project.members.slice(0,4)" :key="member.id"
                            class="w-6 h-6 rounded-full bg-sage-200 dark:bg-sage-300/20 text-sage-700 dark:text-sage-300 text-xs font-semibold flex items-center justify-center -ml-1 first:ml-0 border-2 border-white dark:border-ink-800"
                            :title="member.name"
                        >{{ member.name.charAt(0).toUpperCase() }}</div>
                        <span v-if="project.members.length > 4" class="text-xs text-ink-400 dark:text-sage-500 ml-1">+{{ project.members.length - 4 }}</span>
                    </div>
                </Link>
            </div>

            <!-- Pagination -->
            <div v-if="projects.last_page > 1" class="flex items-center justify-center gap-1">
                <Link
                    v-for="link in projects.links" :key="link.label"
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
import ProjectStatusBadge from '@/Components/ProjectStatusBadge.vue'
import PriorityBadge from '@/Components/PriorityBadge.vue'

const props = defineProps({ projects: Object, filters: Object })

const form = reactive({ search: props.filters?.search ?? '', status: props.filters?.status ?? '', priority: props.filters?.priority ?? '' })
const hasFilter = computed(() => form.search || form.status || form.priority)

const applyFilter = () => router.get('/projects', { search: form.search||undefined, status: form.status||undefined, priority: form.priority||undefined }, { preserveState: true, replace: true })
const resetFilter = () => { form.search=''; form.status=''; form.priority=''; router.get('/projects', {}, { preserveState: false, replace: true }) }
</script>
