<template>
    <div class="min-h-screen flex items-center justify-center bg-sage-300 dark:bg-ink-900 px-4">
        <div class="bg-white dark:bg-ink-800 w-full max-w-md rounded-2xl shadow-lg border border-ink-900/8 dark:border-sage-300/10 p-8">

            <!-- Header -->
            <div class="mb-8 text-center">
                <div class="w-12 h-12 bg-ink-900 dark:bg-sage-300 rounded-xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-sage-300 dark:text-ink-900" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                    </svg>
                </div>
                <h1 class="text-xl font-bold text-ink-900 dark:text-sage-200">Project Management</h1>
                <p class="text-sm text-ink-400 dark:text-sage-500 mt-1">Masuk ke akun kamu</p>
            </div>

            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-ink-700 dark:text-sage-300 mb-1.5">Email</label>
                    <input
                        v-model="form.email"
                        type="email"
                        placeholder="admin@admin.com"
                        class="w-full bg-sage-50 dark:bg-ink-850 border border-ink-200 dark:border-ink-600 rounded-lg px-4 py-2.5 text-sm text-ink-900 dark:text-sage-200 placeholder-ink-300 dark:placeholder-ink-500 focus:outline-none focus:ring-2 focus:ring-ink-900/15 dark:focus:ring-sage-300/20 focus:border-ink-400 dark:focus:border-sage-500 transition"
                        :class="{ 'border-red-400 dark:border-red-500': form.errors.email }"
                    />
                    <p v-if="form.errors.email" class="text-red-500 text-xs mt-1.5">{{ form.errors.email }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-ink-700 dark:text-sage-300 mb-1.5">Password</label>
                    <input
                        v-model="form.password"
                        type="password"
                        placeholder="••••••••"
                        class="w-full bg-sage-50 dark:bg-ink-850 border border-ink-200 dark:border-ink-600 rounded-lg px-4 py-2.5 text-sm text-ink-900 dark:text-sage-200 placeholder-ink-300 dark:placeholder-ink-500 focus:outline-none focus:ring-2 focus:ring-ink-900/15 dark:focus:ring-sage-300/20 focus:border-ink-400 dark:focus:border-sage-500 transition"
                        :class="{ 'border-red-400 dark:border-red-500': form.errors.password }"
                    />
                    <p v-if="form.errors.password" class="text-red-500 text-xs mt-1.5">{{ form.errors.password }}</p>
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full mt-2 bg-ink-900 dark:bg-sage-300 text-sage-200 dark:text-ink-900 font-semibold py-2.5 rounded-lg text-sm transition hover:bg-ink-800 dark:hover:bg-sage-200 disabled:opacity-50"
                >
                    {{ form.processing ? 'Masuk...' : 'Masuk' }}
                </button>
            </form>
        </div>
    </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'

const form = useForm({ email: '', password: '' })
const submit = () => form.post('/login')
</script>
