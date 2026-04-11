<template>
    <div class="min-h-screen bg-gray-50">

        <!-- Navbar -->
        <nav class="bg-white border-b border-gray-200 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-14">

                    <!-- Left: Brand + Nav links -->
                    <div class="flex items-center gap-6">
                        <Link href="/dashboard" class="flex items-center gap-2 shrink-0">
                            <div class="w-7 h-7 bg-blue-600 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                                </svg>
                            </div>
                            <span class="text-sm font-bold text-gray-900 tracking-tight">ProjectMgmt</span>
                        </Link>

                        <!-- Nav links -->
                        <div class="flex items-center gap-0.5">
                            <Link
                                href="/dashboard"
                                class="flex items-center gap-1.5 px-3 py-1.5 rounded-md text-sm font-medium transition-colors"
                                :class="$page.url.startsWith('/dashboard')
                                    ? 'bg-blue-50 text-blue-600'
                                    : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100'"
                            >
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                                </svg>
                                Dashboard
                            </Link>
                            <Link
                                href="/projects"
                                class="flex items-center gap-1.5 px-3 py-1.5 rounded-md text-sm font-medium transition-colors"
                                :class="$page.url.startsWith('/projects')
                                    ? 'bg-blue-50 text-blue-600'
                                    : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100'"
                            >
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" />
                                </svg>
                                Projects
                            </Link>
                            <Link
                                href="/tasks"
                                class="flex items-center gap-1.5 px-3 py-1.5 rounded-md text-sm font-medium transition-colors"
                                :class="$page.url.startsWith('/tasks')
                                    ? 'bg-blue-50 text-blue-600'
                                    : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100'"
                            >
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                                </svg>
                                Tasks
                            </Link>
                        </div>
                    </div>

                    <!-- Right: Dropdown -->
                    <div class="relative" ref="dropdownRef">

                        <!-- Trigger button -->
                        <button
                            @click="dropdownOpen = !dropdownOpen"
                            class="flex items-center gap-2 pl-2 pr-3 py-1.5 rounded-lg hover:bg-gray-100 transition-colors"
                        >
                            <!-- Avatar -->
                            <div class="relative">
                                <div class="w-7 h-7 rounded-full bg-blue-600 text-white text-xs font-bold flex items-center justify-center shrink-0">
                                    {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                                </div>
                                <!-- Unread dot -->
                                <span
                                    v-if="$page.props.auth.user.unread_notifications_count > 0"
                                    class="absolute -top-0.5 -right-0.5 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white"
                                ></span>
                            </div>
                            <!-- Name + role -->
                            <div class="hidden sm:block text-left">
                                <p class="text-sm font-medium text-gray-700 leading-none">{{ $page.props.auth.user.name }}</p>
                                <p class="text-xs text-gray-400 mt-0.5 capitalize">{{ $page.props.auth.user.role?.replace('_', ' ') }}</p>
                            </div>
                            <!-- Chevron -->
                            <svg
                                class="w-3.5 h-3.5 text-gray-400 transition-transform duration-200"
                                :class="dropdownOpen ? 'rotate-180' : ''"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </button>

                        <!-- Dropdown panel -->
                        <Transition
                            enter-active-class="transition duration-150 ease-out"
                            enter-from-class="opacity-0 scale-95 -translate-y-1"
                            enter-to-class="opacity-100 scale-100 translate-y-0"
                            leave-active-class="transition duration-100 ease-in"
                            leave-from-class="opacity-100 scale-100 translate-y-0"
                            leave-to-class="opacity-0 scale-95 -translate-y-1"
                        >
                            <div
                                v-if="dropdownOpen"
                                class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-gray-200 py-1 origin-top-right"
                            >
                                <!-- User info header -->
                                <div class="px-4 py-3 border-b border-gray-100">
                                    <p class="text-sm font-semibold text-gray-800">{{ $page.props.auth.user.name }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $page.props.auth.user.email }}</p>
                                </div>

                                <!-- Notifications -->
                                <Link
                                    href="/notifications"
                                    class="flex items-center justify-between px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors"
                                    @click="dropdownOpen = false"
                                >
                                    <div class="flex items-center gap-2.5">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                                        </svg>
                                        Notifikasi
                                    </div>
                                    <span
                                        v-if="$page.props.auth.user.unread_notifications_count > 0"
                                        class="bg-red-500 text-white text-[10px] font-bold rounded-full min-w-[18px] h-[18px] flex items-center justify-center px-1"
                                    >
                                        {{ $page.props.auth.user.unread_notifications_count > 99 ? '99+' : $page.props.auth.user.unread_notifications_count }}
                                    </span>
                                </Link>

                                <!-- Divider -->
                                <div class="my-1 border-t border-gray-100"></div>

                                <!-- Logout -->
                                <button
                                    @click="logout"
                                    class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm text-red-500 hover:bg-red-50 transition-colors"
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

        <!-- Flash message -->
        <div v-if="$page.props.flash?.success || $page.props.flash?.error" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
            <div v-if="$page.props.flash?.success" class="flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg px-4 py-3">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ $page.props.flash.success }}
            </div>
            <div v-if="$page.props.flash?.error" class="flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg px-4 py-3">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                </svg>
                {{ $page.props.flash.error }}
            </div>
        </div>

        <!-- Page Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <slot />
        </main>

    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { Link, router } from '@inertiajs/vue3'

const dropdownOpen = ref(false)
const dropdownRef = ref(null)

const logout = () => {
    dropdownOpen.value = false
    router.post('/logout')
}

const handleClickOutside = (e) => {
    if (dropdownRef.value && !dropdownRef.value.contains(e.target)) {
        dropdownOpen.value = false
    }
}

onMounted(() => document.addEventListener('mousedown', handleClickOutside))
onUnmounted(() => document.removeEventListener('mousedown', handleClickOutside))
</script>
