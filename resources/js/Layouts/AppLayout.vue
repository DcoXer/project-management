<template>
    <div class="min-h-screen bg-sage-300 dark:bg-ink-900 font-sans">

        <!-- Navbar -->
        <nav class="bg-ink-900 dark:bg-ink-950 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-14">

                    <!-- Left: Brand + Nav -->
                    <div class="flex items-center gap-6">
                        <Link href="/dashboard" class="flex items-center gap-2 shrink-0">
                            <div class="w-7 h-7 bg-sage-300 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-ink-900" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                                </svg>
                            </div>
                            <span class="text-sm font-bold text-sage-300 tracking-tight">ProjectMgmt</span>
                        </Link>

                        <div class="flex items-center gap-0.5">
                            <Link
                                v-for="nav in navLinks" :key="nav.href"
                                :href="nav.href"
                                class="flex items-center gap-1.5 px-3 py-1.5 rounded-md text-sm font-medium transition-colors"
                                :class="$page.url.startsWith(nav.match)
                                    ? 'bg-white/10 text-white'
                                    : 'text-sage-400 hover:text-white hover:bg-white/8'"
                            >
                                <component :is="'svg'" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" :d="nav.icon" />
                                </component>
                                {{ nav.label }}
                            </Link>
                        </div>
                    </div>

                    <div class="relative" ref="dropdownRef">
                        <button
                            @click="dropdownOpen = !dropdownOpen"
                            class="flex items-center gap-2 pl-2 pr-3 py-1.5 rounded-lg hover:bg-white/8 transition-colors"
                        >
                            <div class="relative">
                                <div class="w-7 h-7 rounded-full bg-sage-300 text-ink-900 text-xs font-bold flex items-center justify-center shrink-0">
                                    {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                                </div>
                                <span
                                    v-if="$page.props.auth.user.unread_notifications_count > 0"
                                    class="absolute -top-0.5 -right-0.5 w-2.5 h-2.5 bg-red-400 rounded-full border-2 border-ink-900"
                                ></span>
                            </div>
                            <div class="hidden sm:block text-left">
                                <p class="text-sm font-medium text-sage-200 leading-none">{{ $page.props.auth.user.name }}</p>
                                <p class="text-xs text-sage-500 mt-0.5 capitalize">{{ $page.props.auth.user.role?.replace('_', ' ') }}</p>
                            </div>
                            <svg class="w-3.5 h-3.5 text-sage-500 transition-transform duration-200" :class="dropdownOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </button>

                        <Transition
                            enter-active-class="transition duration-150 ease-out"
                            enter-from-class="opacity-0 scale-95 -translate-y-1"
                            enter-to-class="opacity-100 scale-100 translate-y-0"
                            leave-active-class="transition duration-100 ease-in"
                            leave-from-class="opacity-100 scale-100 translate-y-0"
                            leave-to-class="opacity-0 scale-95 -translate-y-1"
                        >
                            <div v-if="dropdownOpen" class="absolute right-0 mt-2 w-56 bg-white dark:bg-ink-800 rounded-xl shadow-lg border border-ink-900/10 dark:border-sage-300/10 py-1 origin-top-right">
                                <div class="px-4 py-3 border-b border-ink-100 dark:border-sage-300/10">
                                    <p class="text-sm font-semibold text-ink-900 dark:text-sage-200">{{ $page.props.auth.user.name }}</p>
                                    <p class="text-xs text-ink-400 dark:text-sage-500 mt-0.5">{{ $page.props.auth.user.email }}</p>
                                </div>

                                <Link
                                    href="/notifications"
                                    class="flex items-center justify-between px-4 py-2.5 text-sm text-ink-700 dark:text-sage-300 hover:bg-sage-50 dark:hover:bg-sage-300/5 transition-colors"
                                    @click="dropdownOpen = false"
                                >
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

                                <!-- Dark mode toggle -->
                                <button
                                    @click="toggleDark"
                                    class="w-full flex items-center justify-between px-4 py-2.5 text-sm text-ink-700 dark:text-sage-300 hover:bg-sage-50 dark:hover:bg-sage-300/5 transition-colors"
                                >
                                    <div class="flex items-center gap-2.5">
                                        <svg v-if="isDark" class="w-4 h-4 text-ink-400 dark:text-sage-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                                        </svg>
                                        <svg v-else class="w-4 h-4 text-ink-400 dark:text-sage-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                                        </svg>
                                        {{ isDark ? 'Light Mode' : 'Dark Mode' }}
                                    </div>
                                    <div class="relative w-9 h-5 rounded-full transition-colors duration-200" :class="isDark ? 'bg-sage-300' : 'bg-ink-200'">
                                        <div class="absolute top-0.5 w-4 h-4 rounded-full bg-white shadow transition-all duration-200" :class="isDark ? 'left-[18px]' : 'left-0.5'"></div>
                                    </div>
                                </button>

                                <div class="my-1 border-t border-ink-100 dark:border-sage-300/10"></div>

                                <button
                                    @click="logout"
                                    class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm text-red-500 hover:bg-red-50 dark:hover:bg-red-500/5 transition-colors"
                                >
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                                    </svg>
                                    Logout
                                </button>
                            </div>
                        </Transition>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Flash -->
        <div v-if="$page.props.flash?.success || $page.props.flash?.error" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
            <div v-if="$page.props.flash?.success" class="flex items-center gap-2 bg-sage-100 dark:bg-sage-300/10 border border-sage-400/30 text-sage-800 dark:text-sage-300 text-sm rounded-lg px-4 py-3">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                {{ $page.props.flash.success }}
            </div>
            <div v-if="$page.props.flash?.error" class="flex items-center gap-2 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-400/20 text-red-700 dark:text-red-400 text-sm rounded-lg px-4 py-3">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" /></svg>
                {{ $page.props.flash.error }}
            </div>
        </div>

        <!-- Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <slot />
        </main>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { Link, router } from '@inertiajs/vue3'

// ── Dark mode ──────────────────────────────────────────
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

// ── Dropdown ───────────────────────────────────────────
const dropdownOpen = ref(false)
const dropdownRef = ref(null)

const navLinks = [
    {
        href: '/dashboard', match: '/dashboard', label: 'Dashboard',
        icon: 'M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25',
    },
    {
        href: '/projects', match: '/projects', label: 'Projects',
        icon: 'M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z',
    },
    {
        href: '/tasks', match: '/tasks', label: 'Tasks',
        icon: 'M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z',
    },
]

const logout = () => {
    dropdownOpen.value = false
    router.post('/logout')
}

const handleClickOutside = (e) => {
    if (dropdownRef.value && !dropdownRef.value.contains(e.target)) {
        dropdownOpen.value = false
    }
}

onMounted(() => {
    // Restore saved preference, fallback ke system preference
    const saved = localStorage.getItem('theme')
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches
    applyDark(saved ? saved === 'dark' : prefersDark)

    document.addEventListener('mousedown', handleClickOutside)
})

onUnmounted(() => document.removeEventListener('mousedown', handleClickOutside))
</script>
