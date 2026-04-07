<template>
    <div class="min-h-screen bg-gray-100">
        <!-- Navbar -->
        <nav class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <span class="text-xl font-bold text-gray-800">Project Management</span>
                    </div>
                    <div class="flex items-center gap-6">
                        <Link href="/" class="text-gray-600 hover:text-gray-900 text-sm">Dashboard</Link>
                        <Link href="/projects" class="text-gray-600 hover:text-gray-900 text-sm">Projects</Link>
                        <Link href="/tasks" class="text-gray-600 hover:text-gray-900 text-sm">Tasks</Link>
                        <span class="text-sm text-gray-500">{{ $page.props.auth.user.name }}</span>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <slot />
        </main>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
</script>