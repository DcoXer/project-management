<template>
    <div class="min-h-screen bg-sage-50 dark:bg-ink-950 font-sans">

        <!-- ── Sidebar (lg+) ──────────────────────────────────────────── -->
        <aside class="hidden lg:flex flex-col fixed inset-y-0 left-0 w-56 bg-white dark:bg-ink-900 border-r border-ink-900/5 dark:border-white/4 z-40">

            <!-- Brand -->
            <div class="flex items-center gap-3 px-5 h-[60px] shrink-0">
                <Link href="/dashboard" class="flex items-center gap-2.5 group">
                    <div class="w-8 h-8 bg-ink-900 dark:bg-sage-300 rounded-xl flex items-center justify-center shrink-0 transition group-hover:opacity-85">
                        <svg class="w-4 h-4 text-white dark:text-ink-900" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                        </svg>
                    </div>
                    <span class="text-[15px] font-bold text-ink-900 dark:text-sage-200 tracking-tight">ProjectMgmt</span>
                </Link>
            </div>

            <!-- Nav -->
            <nav class="flex-1 px-3 py-3 space-y-0.5 overflow-y-auto">
                <Link
                    v-for="nav in navLinks" :key="nav.href"
                    :href="nav.href"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150"
                    :class="$page.url.startsWith(nav.match)
                        ? 'bg-ink-900 dark:bg-sage-300/15 text-white dark:text-sage-300'
                        : 'text-ink-400 dark:text-sage-600 hover:bg-ink-50 dark:hover:bg-white/4 hover:text-ink-900 dark:hover:text-sage-200'"
                >
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" :d="nav.icon" />
                    </svg>
                    {{ nav.label }}
                </Link>
            </nav>

            <!-- User section -->
            <div class="px-3 pb-4 pt-2 border-t border-ink-900/5 dark:border-white/4 relative" ref="dropdownRef">
                <button
                    @click="dropdownOpen = !dropdownOpen"
                    class="w-full flex items-center gap-2.5 px-3 py-2.5 rounded-xl hover:bg-ink-50 dark:hover:bg-white/4 transition-colors text-left group"
                >
                    <div class="relative shrink-0">
                        <div class="w-7 h-7 rounded-full bg-ink-900 dark:bg-sage-300 text-white dark:text-ink-900 text-xs font-bold flex items-center justify-center">
                            {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                        </div>
                        <span v-if="$page.props.auth.user.unread_notifications_count > 0"
                            class="absolute -top-0.5 -right-0.5 w-2 h-2 bg-red-400 rounded-full border-2 border-white dark:border-ink-900">
                        </span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-ink-900 dark:text-sage-200 truncate leading-none">{{ $page.props.auth.user.name }}</p>
                        <p class="text-[11px] text-ink-400 dark:text-sage-600 mt-0.5 capitalize truncate">{{ $page.props.auth.user.role?.replace('_', ' ') }}</p>
                    </div>
                    <svg class="w-3.5 h-3.5 text-ink-300 dark:text-sage-600 shrink-0 transition-transform duration-200" :class="dropdownOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                    </svg>
                </button>

                <!-- Upward dropdown -->
                <Transition
                    enter-active-class="transition duration-150 ease-out"
                    enter-from-class="opacity-0 translate-y-2 scale-95"
                    enter-to-class="opacity-100 translate-y-0 scale-100"
                    leave-active-class="transition duration-100 ease-in"
                    leave-from-class="opacity-100 translate-y-0 scale-100"
                    leave-to-class="opacity-0 translate-y-2 scale-95"
                >
                    <div v-if="dropdownOpen" class="absolute bottom-full left-3 right-3 mb-2 bg-white dark:bg-ink-800 rounded-xl shadow-lg border border-ink-900/8 dark:border-sage-300/8 py-1 origin-bottom">
                        <div class="px-4 py-3 border-b border-ink-100 dark:border-sage-300/8">
                            <p class="text-sm font-semibold text-ink-900 dark:text-sage-200 truncate">{{ $page.props.auth.user.name }}</p>
                            <p class="text-xs text-ink-400 dark:text-sage-500 mt-0.5 truncate">{{ $page.props.auth.user.email }}</p>
                        </div>

                        <Link href="/profile" @click="dropdownOpen = false"
                            class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-ink-700 dark:text-sage-300 hover:bg-sage-50 dark:hover:bg-sage-300/5 transition-colors">
                            <svg class="w-4 h-4 text-ink-400 dark:text-sage-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                            Profil Saya
                        </Link>

                        <Link href="/notifications" @click="dropdownOpen = false"
                            class="flex items-center justify-between px-4 py-2.5 text-sm text-ink-700 dark:text-sage-300 hover:bg-sage-50 dark:hover:bg-sage-300/5 transition-colors">
                            <div class="flex items-center gap-2.5">
                                <svg class="w-4 h-4 text-ink-400 dark:text-sage-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                                </svg>
                                Notifikasi
                            </div>
                            <span v-if="$page.props.auth.user.unread_notifications_count > 0" class="bg-red-500 text-white text-[10px] font-bold rounded-full min-w-[18px] h-[18px] flex items-center justify-center px-1">
                                {{ $page.props.auth.user.unread_notifications_count > 99 ? '99+' : $page.props.auth.user.unread_notifications_count }}
                            </span>
                        </Link>

                        <button @click="toggleDark"
                            class="w-full flex items-center justify-between px-4 py-2.5 text-sm text-ink-700 dark:text-sage-300 hover:bg-sage-50 dark:hover:bg-sage-300/5 transition-colors">
                            <div class="flex items-center gap-2.5">
                                <svg v-if="isDark" class="w-4 h-4 text-ink-400 dark:text-sage-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                                </svg>
                                <svg v-else class="w-4 h-4 text-ink-400 dark:text-sage-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                                </svg>
                                {{ isDark ? 'Light Mode' : 'Dark Mode' }}
                            </div>
                            <div class="relative w-8 h-4.5 rounded-full transition-colors duration-200" :class="isDark ? 'bg-sage-400' : 'bg-ink-200'">
                                <div class="absolute top-0.5 w-3.5 h-3.5 rounded-full bg-white shadow transition-all duration-200" :class="isDark ? 'left-[17px]' : 'left-0.5'"></div>
                            </div>
                        </button>

                        <div class="my-1 border-t border-ink-100 dark:border-sage-300/8"></div>

                        <button @click="logout"
                            class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm text-red-500 hover:bg-red-50 dark:hover:bg-red-500/5 transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                            </svg>
                            Logout
                        </button>
                    </div>
                </Transition>
            </div>
        </aside>

        <!-- ── Mobile top header ──────────────────────────────────────── -->
        <header class="lg:hidden fixed top-0 inset-x-0 h-14 bg-white/90 dark:bg-ink-900/90 backdrop-blur-md border-b border-ink-900/5 dark:border-white/4 z-40 flex items-center justify-between px-4">
            <Link href="/dashboard" class="flex items-center gap-2">
                <div class="w-7 h-7 bg-ink-900 dark:bg-sage-300 rounded-lg flex items-center justify-center shrink-0">
                    <svg class="w-3.5 h-3.5 text-white dark:text-ink-900" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                    </svg>
                </div>
                <span class="text-sm font-bold text-ink-900 dark:text-sage-200 tracking-tight">ProjectMgmt</span>
            </Link>

            <!-- Mobile user button -->
            <div class="relative" ref="mobileDropdownRef">
                <button @click="dropdownOpen = !dropdownOpen" class="flex items-center gap-1.5 pl-1.5 pr-2 py-1.5 rounded-lg hover:bg-ink-50 dark:hover:bg-white/5 transition-colors">
                    <div class="relative">
                        <div class="w-7 h-7 rounded-full bg-ink-900 dark:bg-sage-300 text-white dark:text-ink-900 text-xs font-bold flex items-center justify-center">
                            {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                        </div>
                        <span v-if="$page.props.auth.user.unread_notifications_count > 0"
                            class="absolute -top-0.5 -right-0.5 w-2 h-2 bg-red-400 rounded-full border-2 border-white dark:border-ink-900">
                        </span>
                    </div>
                    <svg class="w-3 h-3 text-ink-400 dark:text-sage-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                    </svg>
                </button>

                <!-- Mobile dropdown (opens downward) -->
                <Transition
                    enter-active-class="transition duration-150 ease-out"
                    enter-from-class="opacity-0 -translate-y-2 scale-95"
                    enter-to-class="opacity-100 translate-y-0 scale-100"
                    leave-active-class="transition duration-100 ease-in"
                    leave-from-class="opacity-100 translate-y-0 scale-100"
                    leave-to-class="opacity-0 -translate-y-2 scale-95"
                >
                    <div v-if="dropdownOpen" class="absolute right-0 top-full mt-2 w-56 bg-white dark:bg-ink-800 rounded-xl shadow-lg border border-ink-900/8 dark:border-sage-300/8 py-1 origin-top-right">
                        <div class="px-4 py-3 border-b border-ink-100 dark:border-sage-300/8">
                            <p class="text-sm font-semibold text-ink-900 dark:text-sage-200">{{ $page.props.auth.user.name }}</p>
                            <p class="text-xs text-ink-400 dark:text-sage-500 mt-0.5">{{ $page.props.auth.user.email }}</p>
                        </div>

                        <Link href="/profile" @click="dropdownOpen = false"
                            class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-ink-700 dark:text-sage-300 hover:bg-sage-50 dark:hover:bg-sage-300/5 transition-colors">
                            <svg class="w-4 h-4 text-ink-400 dark:text-sage-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                            Profil Saya
                        </Link>

                        <Link href="/notifications" @click="dropdownOpen = false"
                            class="flex items-center justify-between px-4 py-2.5 text-sm text-ink-700 dark:text-sage-300 hover:bg-sage-50 dark:hover:bg-sage-300/5 transition-colors">
                            <div class="flex items-center gap-2.5">
                                <svg class="w-4 h-4 text-ink-400 dark:text-sage-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                                </svg>
                                Notifikasi
                            </div>
                            <span v-if="$page.props.auth.user.unread_notifications_count > 0" class="bg-red-500 text-white text-[10px] font-bold rounded-full min-w-[18px] h-[18px] flex items-center justify-center px-1">
                                {{ $page.props.auth.user.unread_notifications_count > 99 ? '99+' : $page.props.auth.user.unread_notifications_count }}
                            </span>
                        </Link>

                        <button @click="toggleDark"
                            class="w-full flex items-center justify-between px-4 py-2.5 text-sm text-ink-700 dark:text-sage-300 hover:bg-sage-50 dark:hover:bg-sage-300/5 transition-colors">
                            <div class="flex items-center gap-2.5">
                                <svg v-if="isDark" class="w-4 h-4 text-ink-400 dark:text-sage-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                                </svg>
                                <svg v-else class="w-4 h-4 text-ink-400 dark:text-sage-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                                </svg>
                                {{ isDark ? 'Light Mode' : 'Dark Mode' }}
                            </div>
                            <div class="relative w-8 h-[18px] rounded-full transition-colors duration-200" :class="isDark ? 'bg-sage-400' : 'bg-ink-200'">
                                <div class="absolute top-0.5 w-3.5 h-3.5 rounded-full bg-white shadow transition-all duration-200" :class="isDark ? 'left-[17px]' : 'left-0.5'"></div>
                            </div>
                        </button>

                        <div class="my-1 border-t border-ink-100 dark:border-sage-300/8"></div>

                        <button @click="logout"
                            class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm text-red-500 hover:bg-red-50 dark:hover:bg-red-500/5 transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                            </svg>
                            Logout
                        </button>
                    </div>
                </Transition>
            </div>
        </header>

        <!-- ── Main content ───────────────────────────────────────────── -->
        <div class="lg:pl-56">
            <main class="px-4 sm:px-6 lg:px-8 pt-20 pb-24 lg:pt-8 lg:pb-10 min-h-screen">
                <Transition
                    enter-active-class="transition duration-200 ease-out"
                    enter-from-class="opacity-0 translate-y-1"
                    enter-to-class="opacity-100 translate-y-0"
                    mode="out-in"
                >
                    <SkeletonLoading v-if="isNavigating" key="skeleton" />
                    <slot v-else key="content" />
                </Transition>
            </main>
        </div>

        <!-- ── Mobile bottom nav ──────────────────────────────────────── -->
        <nav class="lg:hidden fixed bottom-0 inset-x-0 z-40 bg-white/90 dark:bg-ink-900/90 backdrop-blur-md border-t border-ink-900/5 dark:border-white/4">
            <div class="flex items-stretch h-16">
                <Link
                    v-for="nav in navLinks" :key="nav.href"
                    :href="nav.href"
                    class="flex-1 flex flex-col items-center justify-center gap-1 transition-colors"
                    :class="$page.url.startsWith(nav.match)
                        ? 'text-ink-900 dark:text-sage-300'
                        : 'text-ink-300 dark:text-sage-700 hover:text-ink-600 dark:hover:text-sage-400'"
                >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" :d="nav.icon" />
                    </svg>
                    <span class="text-[10px] font-medium">{{ nav.label }}</span>
                </Link>
            </div>
        </nav>

        <ToastContainer />
    </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import ToastContainer from '@/Components/ToastContainer.vue'
import SkeletonLoading from '@/Components/SkeletonLoading.vue'
import { useToast } from '@/composables/useToast'

const page    = usePage()
const { success: toastSuccess, error: toastError } = useToast()

watch(
    () => page.props.flash,
    (flash) => {
        if (flash?.success) toastSuccess(flash.success)
        if (flash?.error)   toastError(flash.error)
    },
    { deep: true }
)

// ── Dark mode ──────────────────────────────────────────────────────────
const isDark = ref(false)

const applyDark = (dark) => {
    document.documentElement.classList.toggle('dark', dark)
    isDark.value = dark
}

const toggleDark = () => {
    const next = !isDark.value
    localStorage.setItem('theme', next ? 'dark' : 'light')
    applyDark(next)
}

// ── Dropdown ───────────────────────────────────────────────────────────
const isNavigating      = ref(false)
const dropdownOpen      = ref(false)
const dropdownRef       = ref(null)
const mobileDropdownRef = ref(null)

const navLinks = computed(() => [
    {
        href: '/dashboard', match: '/dashboard', label: 'Dashboard',
        icon: 'M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25',
    },
    {
        href: '/projects', match: '/projects', label: 'Projects',
        icon: 'M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z',
    },
    {
        href: '/tasks', match: '/tasks', label: page.props.auth.user?.is_pm ? 'Team Tasks' : 'My Tasks',
        icon: 'M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z',
    },
])

const logout = () => {
    dropdownOpen.value = false
    router.post('/logout')
}

const handleClickOutside = (e) => {
    const inDesktop = dropdownRef.value?.contains(e.target)
    const inMobile  = mobileDropdownRef.value?.contains(e.target)
    if (!inDesktop && !inMobile) dropdownOpen.value = false
}

let navTimer = null

onMounted(() => {
    const saved = localStorage.getItem('theme')
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches
    applyDark(saved ? saved === 'dark' : prefersDark)
    document.addEventListener('mousedown', handleClickOutside)

    router.on('start', () => {
        // Delay kecil supaya navigasi cepat (< 150ms) tidak sempat tampilkan skeleton
        navTimer = setTimeout(() => { isNavigating.value = true }, 150)
    })
    router.on('finish', () => {
        clearTimeout(navTimer)
        isNavigating.value = false
    })
})

onUnmounted(() => {
    document.removeEventListener('mousedown', handleClickOutside)
    clearTimeout(navTimer)
})
</script>
