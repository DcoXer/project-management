<template>
    <div class="min-h-screen flex items-center justify-center bg-white dark:bg-ink-900 px-4 relative overflow-hidden">

        <!-- Split screen container -->
        <div class="login-card relative w-full max-w-4xl rounded-2xl overflow-hidden flex
                    shadow-[0_2px_4px_rgba(0,0,0,0.04),0_8px_16px_rgba(0,0,0,0.06),0_20px_48px_rgba(0,0,0,0.12),0_40px_80px_rgba(15,110,86,0.10)]
                    dark:shadow-[0_4px_12px_rgba(0,0,0,0.4),0_20px_60px_rgba(0,0,0,0.6),0_0_0_1px_rgba(255,255,255,0.04)]
                    ring-1 ring-slate-200/80 dark:ring-ink-700">

            <!-- LEFT PANEL: Branding -->
            <div class="left-panel relative hidden md:flex flex-col justify-between w-5/12 p-10 overflow-hidden"
                 style="background: #0F6E56;">

                <!-- Decorative circles -->
                <div class="absolute -top-10 -right-10 w-40 h-40 rounded-full"
                     style="background: rgba(255,255,255,0.08);"></div>
                <div class="absolute -bottom-14 -left-6 w-52 h-52 rounded-full"
                     style="background: rgba(255,255,255,0.05);"></div>

                <!-- Brand -->
                <div class="relative z-10">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-6"
                         style="background: rgba(255,255,255,0.15);">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold leading-tight" style="color: white; font-family: sans-serif;">
                        Project<br>
                        <span style="color: #9FE1CB;">Management</span>
                    </h2>
                    <p class="mt-3 text-sm" style="color: rgba(255,255,255,0.6);">
                        Kelola semua proyek dalam satu tempat
                    </p>
                </div>

                <!-- Stats -->
                <div class="relative z-10 flex flex-col gap-3">
                    <div class="flex items-center gap-3 rounded-xl p-3"
                         style="background: rgba(255,255,255,0.10);">
                        <svg class="w-5 h-5 flex-shrink-0" style="color: #9FE1CB;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <div class="text-base font-semibold text-white">{{ tasksSelesai }}</div>
                            <div class="text-xs" style="color: rgba(255,255,255,0.6);">Task selesai</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 rounded-xl p-3"
                         style="background: rgba(255,255,255,0.10);">
                        <svg class="w-5 h-5 flex-shrink-0" style="color: #9FE1CB;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                        <div>
                            <div class="text-base font-semibold text-white">{{ anggotaAktif }}</div>
                            <div class="text-xs" style="color: rgba(255,255,255,0.6);">Anggota aktif</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT PANEL: Form -->
            <div class="flex-1 bg-white dark:bg-ink-800 flex flex-col justify-center p-8 md:p-10">

                <!-- Mobile header (hanya tampil di mobile) -->
                <div class="md:hidden flex items-center gap-3 mb-8">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center"
                         style="background: #0F6E56;">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                        </svg>
                    </div>
                    <span class="font-bold text-ink-900 dark:text-white text-lg">Project Management</span>
                </div>

                <div class="mb-7">
                    <h1 class="text-2xl font-bold text-ink-900 dark:text-white tracking-tight">Selamat datang</h1>
                    <p class="text-sm text-ink-400 dark:text-ink-300 mt-1">Masuk untuk melanjutkan</p>
                </div>

                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-widest text-ink-400 dark:text-ink-300 mb-1.5">
                            Email
                        </label>
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-ink-300 dark:text-ink-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                            </svg>
                            <input
                                v-model="form.email"
                                type="email"
                                placeholder="admin@admin.com"
                                class="w-full bg-slate-50 dark:bg-ink-700 border border-slate-200 dark:border-ink-600 rounded-xl pl-10 pr-4 py-2.5 text-sm text-ink-900 dark:text-white placeholder-ink-300 dark:placeholder-ink-400 focus:outline-none focus:ring-2 focus:border-transparent transition"
                                :class="form.errors.email
                                    ? 'border-red-400 dark:border-red-500 focus:ring-red-400/25'
                                    : 'focus:ring-[#1D9E75]/25 focus:border-[#1D9E75]'"
                            />
                        </div>
                        <p v-if="form.errors.email" class="text-red-500 text-xs mt-1.5">{{ form.errors.email }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-widest text-ink-400 dark:text-ink-300 mb-1.5">
                            Password
                        </label>
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-ink-300 dark:text-ink-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                            <input
                                v-model="form.password"
                                type="password"
                                placeholder="••••••••"
                                class="w-full bg-slate-50 dark:bg-ink-700 border border-slate-200 dark:border-ink-600 rounded-xl pl-10 pr-4 py-2.5 text-sm text-ink-900 dark:text-white placeholder-ink-300 dark:placeholder-ink-400 focus:outline-none focus:ring-2 focus:border-transparent transition"
                                :class="form.errors.password
                                    ? 'border-red-400 dark:border-red-500 focus:ring-red-400/25'
                                    : 'focus:ring-[#1D9E75]/25 focus:border-[#1D9E75]'"
                            />
                        </div>
                        <p v-if="form.errors.password" class="text-red-500 text-xs mt-1.5">{{ form.errors.password }}</p>
                    </div>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full text-white font-semibold py-2.5 rounded-xl text-sm transition disabled:opacity-50 shadow-sm flex items-center justify-center gap-2"
                        style="background: #0F6E56;"
                        onmouseover="this.style.background='#085041'"
                        onmouseout="this.style.background='#0F6E56'"
                    >
                        <span>{{ form.processing ? 'Masuk...' : 'Masuk' }}</span>
                        <svg v-if="!form.processing" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                        </svg>
                    </button>
                </form>

                <p class="text-center mt-6 text-xs text-ink-400 dark:text-ink-500">
                    Belum punya akun?
                    <a href="#" class="font-medium" style="color: #1D9E75;">Hubungi admin</a>
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'

defineProps({ tasksSelesai: Number, anggotaAktif: Number })

const form = useForm({ email: '', password: '' })
const submit = () => form.post('/login')
</script>

<style scoped>
.login-card {
    animation: cardIn 0.5s cubic-bezier(0.16, 1, 0.3, 1) both;
}

@keyframes cardIn {
    from { opacity: 0; transform: translateY(24px) scale(0.97); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
}
</style>