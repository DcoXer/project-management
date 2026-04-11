<template>
    <AppLayout>
        <div class="max-w-2xl space-y-6">

            <div>
                <h1 class="text-2xl font-bold text-ink-900 dark:text-sage-200 tracking-tight">Profil Saya</h1>
                <p class="text-sm text-ink-500 dark:text-sage-500 mt-1">Kelola informasi akun kamu</p>
            </div>

            <!-- Info akun -->
            <div class="bg-white dark:bg-ink-800 rounded-xl border border-ink-900/8 dark:border-sage-300/8 shadow-sm">
                <div class="px-6 py-4 border-b border-ink-100 dark:border-sage-300/8">
                    <h2 class="font-semibold text-ink-900 dark:text-sage-200">Informasi Akun</h2>
                </div>
                <form @submit.prevent="submitInfo" class="p-6 space-y-4">
                    <!-- Avatar -->
                    <div class="flex items-center gap-4 pb-2">
                        <div class="w-16 h-16 rounded-full bg-sage-300 dark:bg-sage-300/30 text-ink-900 dark:text-sage-200 text-2xl font-bold flex items-center justify-center shrink-0">
                            {{ user.name.charAt(0).toUpperCase() }}
                        </div>
                        <div>
                            <p class="font-semibold text-ink-900 dark:text-sage-200">{{ user.name }}</p>
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium" :class="roleClass(user.role)">{{ roleLabel(user.role) }}</span>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-semibold text-ink-700 dark:text-sage-300">Nama</label>
                        <input v-model="infoForm.name" type="text"
                            class="w-full bg-sage-50 dark:bg-ink-850 border border-ink-200 dark:border-ink-600 rounded-lg px-3 py-2 text-sm text-ink-900 dark:text-sage-200 focus:outline-none focus:ring-2 focus:ring-ink-900/15 dark:focus:ring-sage-300/20 transition"
                            :class="{ 'border-red-400': infoForm.errors.name }" />
                        <p v-if="infoForm.errors.name" class="text-xs text-red-500">{{ infoForm.errors.name }}</p>
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-semibold text-ink-700 dark:text-sage-300">Email</label>
                        <input v-model="infoForm.email" type="email"
                            class="w-full bg-sage-50 dark:bg-ink-850 border border-ink-200 dark:border-ink-600 rounded-lg px-3 py-2 text-sm text-ink-900 dark:text-sage-200 focus:outline-none focus:ring-2 focus:ring-ink-900/15 dark:focus:ring-sage-300/20 transition"
                            :class="{ 'border-red-400': infoForm.errors.email }" />
                        <p v-if="infoForm.errors.email" class="text-xs text-red-500">{{ infoForm.errors.email }}</p>
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-semibold text-ink-700 dark:text-sage-300">Role</label>
                        <input :value="roleLabel(user.role)" disabled
                            class="w-full bg-ink-100 dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-lg px-3 py-2 text-sm text-ink-400 dark:text-sage-500 cursor-not-allowed" />
                        <p class="text-xs text-ink-400 dark:text-sage-600">Role dikelola oleh admin dan tidak bisa diubah sendiri.</p>
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-semibold text-ink-700 dark:text-sage-300">Bergabung sejak</label>
                        <input :value="formatDate(user.created_at)" disabled
                            class="w-full bg-ink-100 dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-lg px-3 py-2 text-sm text-ink-400 dark:text-sage-500 cursor-not-allowed" />
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit" :disabled="infoForm.processing"
                            class="px-5 py-2 rounded-lg text-sm font-semibold bg-ink-900 dark:bg-sage-300 text-white dark:text-ink-900 hover:bg-ink-700 dark:hover:bg-sage-200 transition disabled:opacity-50">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            <!-- Ganti password -->
            <div class="bg-white dark:bg-ink-800 rounded-xl border border-ink-900/8 dark:border-sage-300/8 shadow-sm">
                <div class="px-6 py-4 border-b border-ink-100 dark:border-sage-300/8">
                    <h2 class="font-semibold text-ink-900 dark:text-sage-200">Ganti Password</h2>
                </div>
                <form @submit.prevent="submitPassword" class="p-6 space-y-4">
                    <div class="space-y-1">
                        <label class="text-sm font-semibold text-ink-700 dark:text-sage-300">Password Saat Ini</label>
                        <input v-model="passForm.current_password" type="password" autocomplete="current-password"
                            class="w-full bg-sage-50 dark:bg-ink-850 border border-ink-200 dark:border-ink-600 rounded-lg px-3 py-2 text-sm text-ink-900 dark:text-sage-200 focus:outline-none focus:ring-2 focus:ring-ink-900/15 dark:focus:ring-sage-300/20 transition"
                            :class="{ 'border-red-400': passForm.errors.current_password }" />
                        <p v-if="passForm.errors.current_password" class="text-xs text-red-500">{{ passForm.errors.current_password }}</p>
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-semibold text-ink-700 dark:text-sage-300">Password Baru</label>
                        <input v-model="passForm.password" type="password" autocomplete="new-password"
                            class="w-full bg-sage-50 dark:bg-ink-850 border border-ink-200 dark:border-ink-600 rounded-lg px-3 py-2 text-sm text-ink-900 dark:text-sage-200 focus:outline-none focus:ring-2 focus:ring-ink-900/15 dark:focus:ring-sage-300/20 transition"
                            :class="{ 'border-red-400': passForm.errors.password }" />
                        <p v-if="passForm.errors.password" class="text-xs text-red-500">{{ passForm.errors.password }}</p>
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-semibold text-ink-700 dark:text-sage-300">Konfirmasi Password Baru</label>
                        <input v-model="passForm.password_confirmation" type="password" autocomplete="new-password"
                            class="w-full bg-sage-50 dark:bg-ink-850 border border-ink-200 dark:border-ink-600 rounded-lg px-3 py-2 text-sm text-ink-900 dark:text-sage-200 focus:outline-none focus:ring-2 focus:ring-ink-900/15 dark:focus:ring-sage-300/20 transition" />
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit" :disabled="passForm.processing"
                            class="px-5 py-2 rounded-lg text-sm font-semibold bg-red-600 hover:bg-red-700 text-white transition disabled:opacity-50">
                            Ubah Password
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps({ user: Object })

const infoForm = useForm({ name: props.user.name, email: props.user.email })
const passForm = useForm({ current_password: '', password: '', password_confirmation: '' })

const submitInfo     = () => infoForm.patch('/profile/info')
const submitPassword = () => passForm.patch('/profile/password', { onSuccess: () => passForm.reset() })

const roleLabel = r => ({ admin: 'Admin', project_manager: 'Project Manager', developer: 'Developer' }[r] ?? r)
const roleClass = r => ({ admin: 'bg-red-100 text-red-700 dark:bg-red-400/15 dark:text-red-400', project_manager: 'bg-violet-100 text-violet-700 dark:bg-violet-400/15 dark:text-violet-400', developer: 'bg-sage-100 text-sage-700 dark:bg-sage-300/15 dark:text-sage-300' }[r] ?? 'bg-ink-100 text-ink-600')
const formatDate = d => d ? new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '-'
</script>
