<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-md p-8">

            <!-- Header -->
            <div class="mb-8 text-center">
                <h1 class="text-2xl font-bold text-gray-800">Project Management</h1>
                <p class="text-sm text-gray-500 mt-1">Masuk ke akun kamu</p>
            </div>

            <!-- Form -->
            <form @submit.prevent="submit">

                <!-- Email -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Email
                    </label>
                    <input
                        v-model="form.email"
                        type="email"
                        placeholder="admin@admin.com"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        :class="{ 'border-red-500': form.errors.email }"
                    />
                    <p v-if="form.errors.email" class="text-red-500 text-xs mt-1">
                        {{ form.errors.email }}
                    </p>
                </div>

                <!-- Password -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Password
                    </label>
                    <input
                        v-model="form.password"
                        type="password"
                        placeholder="••••••••"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        :class="{ 'border-red-500': form.errors.password }"
                    />
                    <p v-if="form.errors.password" class="text-red-500 text-xs mt-1">
                        {{ form.errors.password }}
                    </p>
                </div>

                <!-- Submit -->
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 rounded-lg text-sm transition disabled:opacity-50"
                >
                    {{ form.processing ? 'Masuk...' : 'Masuk' }}
                </button>

            </form>
        </div>
    </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'

const form = useForm({
    email: '',
    password: '',
})

const submit = () => {
    form.post('/login')
}
</script>