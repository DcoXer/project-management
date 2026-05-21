<template>
    <AppLayout>
        <div class="space-y-5">

            <!-- ── Profile Hero ─────────────────────────────────────── -->
            <div class="bg-white dark:bg-ink-900 rounded-2xl border border-ink-900/6 dark:border-white/5 shadow-sm">
                <!-- Decorative band — overflow-hidden hanya di sini supaya avatar tidak terpotong -->
                <div class="h-28 relative bg-ink-900 dark:bg-ink-850 rounded-t-2xl overflow-hidden">
                    <div class="absolute inset-0"
                        style="background-image: radial-gradient(ellipse at 20% 60%, rgba(110,231,183,0.25) 0%, transparent 55%), radial-gradient(ellipse at 80% 30%, rgba(167,139,250,0.2) 0%, transparent 50%), radial-gradient(ellipse at 60% 80%, rgba(251,191,36,0.1) 0%, transparent 45%)">
                    </div>
                    <div class="absolute inset-0 opacity-[0.04]"
                        style="background-image: linear-gradient(rgba(255,255,255,0.5) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.5) 1px, transparent 1px); background-size: 24px 24px">
                    </div>
                </div>

                <div class="px-6 pb-6">
                    <!-- Avatar overlap — z-10 supaya di atas band -->
                    <div class="flex items-end justify-between -mt-11 mb-4">
                        <div class="relative z-10 w-[72px] h-[72px] rounded-2xl bg-gradient-to-br from-sage-300 to-sage-400 dark:from-sage-300/80 dark:to-sage-400/60 text-ink-900 text-2xl font-black flex items-center justify-center border-4 border-white dark:border-ink-900 shadow-lg select-none">
                            {{ user.name.charAt(0).toUpperCase() }}
                        </div>
                        <span class="text-[11px] px-2.5 py-1 rounded-full font-semibold" :class="roleClass(user.role)">{{ roleLabel(user.role) }}</span>
                    </div>

                    <h1 class="text-xl font-bold text-ink-900 dark:text-white tracking-tight leading-none">{{ user.name }}</h1>
                    <div class="flex items-center gap-2 mt-1.5">
                        <span class="text-sm text-ink-400 dark:text-white">{{ user.email }}</span>
                        <span class="text-ink-200 dark:text-white">·</span>
                        <span class="text-xs text-ink-400 dark:text-white flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5" /></svg>
                            Bergabung {{ formatDate(user.created_at) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- ── Forms grid ───────────────────────────────────────── -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                <!-- Edit Info -->
                <div class="bg-white dark:bg-ink-900 rounded-2xl border border-ink-900/6 dark:border-white/5 p-6 shadow-sm">
                    <div class="mb-5">
                        <h2 class="text-sm font-bold text-ink-900 dark:text-white">Informasi Akun</h2>
                        <p class="text-xs text-ink-400 dark:text-white mt-0.5">Perbarui nama dan email kamu</p>
                    </div>
                    <form @submit.prevent="submitInfo" class="space-y-4">
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-semibold text-ink-400 dark:text-white uppercase tracking-widest">Nama</label>
                            <input v-model="infoForm.name" type="text"
                                class="w-full bg-ink-50 dark:bg-ink-800 border border-ink-200 dark:border-white/8 rounded-xl px-3.5 py-2.5 text-sm text-ink-900 dark:text-white placeholder-ink-300 focus:outline-none focus:ring-2 focus:ring-ink-900/10 dark:focus:ring-sage-300/15 transition"
                                :class="{ 'border-red-400 dark:border-red-400': infoForm.errors.name }" />
                            <p v-if="infoForm.errors.name" class="text-xs text-red-500">{{ infoForm.errors.name }}</p>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-semibold text-ink-400 dark:text-white uppercase tracking-widest">Email</label>
                            <input v-model="infoForm.email" type="email"
                                class="w-full bg-ink-50 dark:bg-ink-800 border border-ink-200 dark:border-white/8 rounded-xl px-3.5 py-2.5 text-sm text-ink-900 dark:text-white placeholder-ink-300 focus:outline-none focus:ring-2 focus:ring-ink-900/10 dark:focus:ring-sage-300/15 transition"
                                :class="{ 'border-red-400 dark:border-red-400': infoForm.errors.email }" />
                            <p v-if="infoForm.errors.email" class="text-xs text-red-500">{{ infoForm.errors.email }}</p>
                        </div>

                        <!-- Read-only: Role -->
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-semibold text-ink-400 dark:text-white uppercase tracking-widest">Role</label>
                            <div class="flex items-center gap-2 px-3.5 py-2.5 bg-ink-50 dark:bg-ink-800/50 border border-ink-100 dark:border-white/5 rounded-xl">
                                <span class="text-[11px] px-2 py-0.5 rounded-full font-semibold" :class="roleClass(user.role)">{{ roleLabel(user.role) }}</span>
                                <span class="text-xs text-ink-300 dark:text-white">— tidak bisa diubah sendiri</span>
                            </div>
                        </div>

                        <button type="submit" :disabled="infoForm.processing"
                            class="w-full mt-1 py-2.5 rounded-xl text-sm font-semibold bg-ink-900 dark:bg-sage-300 text-white dark:text-ink-900 hover:bg-ink-700 dark:hover:bg-sage-200 transition disabled:opacity-50">
                            Simpan Perubahan
                        </button>
                    </form>
                </div>

                <!-- Change Password -->
                <div class="bg-white dark:bg-ink-900 rounded-2xl border border-ink-900/6 dark:border-white/5 p-6 shadow-sm">
                    <div class="mb-5">
                        <h2 class="text-sm font-bold text-ink-900 dark:text-white">Ubah Password</h2>
                        <p class="text-xs text-ink-400 dark:text-white mt-0.5">Gunakan password yang kuat dan unik</p>
                    </div>
                    <form @submit.prevent="submitPassword" class="space-y-4">
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-semibold text-ink-400 dark:text-white uppercase tracking-widest">Password Saat Ini</label>
                            <input v-model="passForm.current_password" type="password" autocomplete="current-password"
                                class="w-full bg-ink-50 dark:bg-ink-800 border border-ink-200 dark:border-white/8 rounded-xl px-3.5 py-2.5 text-sm text-ink-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-ink-900/10 dark:focus:ring-sage-300/15 transition"
                                :class="{ 'border-red-400 dark:border-red-400': passForm.errors.current_password }" />
                            <p v-if="passForm.errors.current_password" class="text-xs text-red-500">{{ passForm.errors.current_password }}</p>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-semibold text-ink-400 dark:text-white uppercase tracking-widest">Password Baru</label>
                            <input v-model="passForm.password" type="password" autocomplete="new-password"
                                class="w-full bg-ink-50 dark:bg-ink-800 border border-ink-200 dark:border-white/8 rounded-xl px-3.5 py-2.5 text-sm text-ink-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-ink-900/10 dark:focus:ring-sage-300/15 transition"
                                :class="{ 'border-red-400 dark:border-red-400': passForm.errors.password }" />
                            <p v-if="passForm.errors.password" class="text-xs text-red-500">{{ passForm.errors.password }}</p>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-semibold text-ink-400 dark:text-white uppercase tracking-widest">Konfirmasi Password Baru</label>
                            <input v-model="passForm.password_confirmation" type="password" autocomplete="new-password"
                                class="w-full bg-ink-50 dark:bg-ink-800 border border-ink-200 dark:border-white/8 rounded-xl px-3.5 py-2.5 text-sm text-ink-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-ink-900/10 dark:focus:ring-sage-300/15 transition" />
                        </div>

                        <button type="submit" :disabled="passForm.processing"
                            class="w-full mt-1 py-2.5 rounded-xl text-sm font-semibold bg-red-600 hover:bg-red-700 text-white transition disabled:opacity-50">
                            Ubah Password
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { useForm } from '@inertiajs/vue3'
import { formatDate } from '@/composables/useFormatters'

const props = defineProps({ user: Object })

const infoForm = useForm({ name: props.user.name, email: props.user.email })
const passForm = useForm({ current_password: '', password: '', password_confirmation: '' })

const submitInfo     = () => infoForm.patch('/profile/info')
const submitPassword = () => passForm.patch('/profile/password', { onSuccess: () => passForm.reset() })

const roleLabel = r => ({ admin: 'Admin', project_manager: 'Project Manager', developer: 'Developer' }[r] ?? r)
const roleClass = r => ({
    admin:           'bg-red-600 text-white dark:bg-red-600 dark:text-white',
    project_manager: 'bg-violet-600 text-white dark:bg-violet-600 dark:text-white',
    developer:       'bg-sage-600 text-white dark:bg-sage-600 dark:text-white',
}[r] ?? 'bg-ink-100 text-ink-600')
</script>
