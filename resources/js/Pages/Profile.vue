<template>
    <AppLayout>
        <div class="space-y-5">

            <!-- ── Profile Hero ─────────────────────────────────────── -->
            <div class="bg-white dark:bg-ink-900 rounded-2xl border border-ink-900/6 dark:border-white/5 shadow-sm">
                <div class="h-28 relative bg-ink-900 dark:bg-ink-850 rounded-t-2xl overflow-hidden">
                    <div class="absolute inset-0"
                        style="background-image: radial-gradient(ellipse at 20% 60%, rgba(110,231,183,0.25) 0%, transparent 55%), radial-gradient(ellipse at 80% 30%, rgba(167,139,250,0.2) 0%, transparent 50%), radial-gradient(ellipse at 60% 80%, rgba(251,191,36,0.1) 0%, transparent 45%)">
                    </div>
                    <div class="absolute inset-0 opacity-[0.04]"
                        style="background-image: linear-gradient(rgba(255,255,255,0.5) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.5) 1px, transparent 1px); background-size: 24px 24px">
                    </div>
                </div>

                <div class="px-6 pb-6">
                    <div class="flex items-end justify-between -mt-11 mb-4">
                        <!-- Avatar with upload overlay -->
                        <div class="relative z-10 group cursor-pointer" @click="triggerFileInput" title="Klik untuk ganti foto profil">
                            <!-- Photo or initials -->
                            <div class="w-[72px] h-[72px] rounded-2xl border-4 border-white dark:border-ink-900 shadow-lg overflow-hidden">
                                <img v-if="previewUrl || user.avatar_url"
                                    :src="previewUrl || user.avatar_url"
                                    alt="Foto profil"
                                    class="w-full h-full object-cover" />
                                <div v-else
                                    class="w-full h-full bg-gradient-to-br from-sage-300 to-sage-400 dark:from-sage-300/80 dark:to-sage-400/60 text-ink-900 text-2xl font-black flex items-center justify-center select-none">
                                    {{ user.name.charAt(0).toUpperCase() }}
                                </div>
                            </div>
                            <!-- Camera overlay on hover -->
                            <div class="absolute inset-0 rounded-2xl bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                                </svg>
                            </div>
                            <!-- Hidden file input -->
                            <input ref="fileInput" type="file" accept="image/*" class="hidden" @change="handleFileChange" />
                        </div>

                        <span class="text-[11px] px-2.5 py-1 rounded-full font-semibold" :class="roleClass(user.role)">{{ roleLabel(user.role) }}</span>
                    </div>

                    <!-- Photo upload confirmation bar -->
                    <div v-if="previewUrl" class="mb-3 flex items-center gap-3 px-3.5 py-2.5 bg-sage-50 dark:bg-sage-300/10 border border-sage-200 dark:border-sage-300/20 rounded-xl">
                        <svg class="w-4 h-4 text-sage-600 dark:text-sage-300 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                        </svg>
                        <span class="text-xs text-sage-700 dark:text-sage-300 flex-1">Foto dipilih — klik simpan untuk mengupload</span>
                        <button @click="submitPhoto" :disabled="photoProcessing"
                            class="text-xs font-semibold px-3 py-1 rounded-lg bg-sage-600 hover:bg-sage-700 text-white transition disabled:opacity-50">
                            {{ photoProcessing ? 'Menyimpan...' : 'Simpan Foto' }}
                        </button>
                        <button @click="cancelPhoto" class="text-xs text-ink-400 dark:text-white hover:text-ink-700 transition">Batal</button>
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
import { ref } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import { useForm, router } from '@inertiajs/vue3'
import { formatDate } from '@/composables/useFormatters'

const props = defineProps({ user: Object })

const infoForm = useForm({ name: props.user.name, email: props.user.email })
const passForm = useForm({ current_password: '', password: '', password_confirmation: '' })

const fileInput = ref(null)
const previewUrl = ref(null)
const selectedFile = ref(null)
const photoProcessing = ref(false)

const triggerFileInput = () => fileInput.value?.click()

const handleFileChange = (e) => {
    const file = e.target.files[0]
    if (!file) return
    selectedFile.value = file
    previewUrl.value = URL.createObjectURL(file)
}

const submitPhoto = () => {
    if (!selectedFile.value) return

    const formData = new FormData()
    formData.append('photo', selectedFile.value)

    photoProcessing.value = true
    router.post('/profile/photo', formData, {
        onFinish: () => { photoProcessing.value = false },
        onSuccess: () => {
            previewUrl.value = null
            selectedFile.value = null
            if (fileInput.value) fileInput.value.value = ''
        },
        onError: (errors) => {
            console.error('Upload error:', errors)
        },
    })
}

const cancelPhoto = () => {
    previewUrl.value = null
    selectedFile.value = null
    if (fileInput.value) fileInput.value.value = ''
}

const submitInfo     = () => infoForm.patch('/profile/info')
const submitPassword = () => passForm.patch('/profile/password', { onSuccess: () => passForm.reset() })

const roleLabel = r => ({ admin: 'Admin', project_manager: 'Project Manager', developer: 'Developer' }[r] ?? r)
const roleClass = r => ({
    admin:           'bg-red-600 text-white dark:bg-red-600 dark:text-white',
    project_manager: 'bg-violet-600 text-white dark:bg-violet-600 dark:text-white',
    developer:       'bg-sage-600 text-white dark:bg-sage-600 dark:text-white',
}[r] ?? 'bg-ink-100 text-ink-600')
</script>
