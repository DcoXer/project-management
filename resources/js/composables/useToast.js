import { reactive } from 'vue'

const toasts = reactive([])
let nextId = 0

export function useToast() {
    const add = (message, type = 'success', duration = 4000) => {
        const id = ++nextId
        toasts.push({ id, message, type, duration, progress: 100 })

        // Countdown progress bar
        const step = 100 / (duration / 50)
        const interval = setInterval(() => {
            const toast = toasts.find(t => t.id === id)
            if (!toast) { clearInterval(interval); return }
            toast.progress -= step
            if (toast.progress <= 0) clearInterval(interval)
        }, 50)

        setTimeout(() => remove(id), duration)
    }

    const remove = (id) => {
        const idx = toasts.findIndex(t => t.id === id)
        if (idx !== -1) toasts.splice(idx, 1)
    }

    const success = (message, duration) => add(message, 'success', duration)
    const error   = (message, duration) => add(message, 'error', duration)

    return { toasts, success, error, remove }
}
