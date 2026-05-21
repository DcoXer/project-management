// ── Date formatters ────────────────────────────────────────────────────
export const formatDate = d =>
    d ? new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : '—'

export const timeAgo = d => {
    if (!d) return '—'
    const diff = Math.floor((Date.now() - new Date(d)) / 1000)
    if (diff < 60)     return 'baru saja'
    if (diff < 3600)   return `${Math.floor(diff / 60)} menit lalu`
    if (diff < 86400)  return `${Math.floor(diff / 3600)} jam lalu`
    if (diff < 604800) return `${Math.floor(diff / 86400)} hari lalu`
    return formatDate(d)
}

// Tanpa tahun — dipakai di card Kanban
export const formatDateShort = d =>
    d ? new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }) : '—'

export const formatDateTime = d =>
    d ? new Date(d).toLocaleString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }) : '—'

export const isOverdue = task =>
    task.due_date && task.status !== 'done' && new Date(task.due_date) < new Date()

// ── Task status ────────────────────────────────────────────────────────
export const taskStatusLabel = s =>
    ({ todo: 'Todo', in_progress: 'In Progress', review: 'Review', done: 'Done' }[s] ?? s)

export const taskStatusClass = s =>
    ({
        todo:        'bg-ink-100 text-ink-600 dark:bg-ink-800 dark:text-ink-300',
        in_progress: 'bg-sage-100 text-sage-700 dark:bg-sage-300/15 dark:text-sage-300',
        review:      'bg-violet-100 text-violet-700 dark:bg-violet-300/15 dark:text-violet-300',
        done:        'bg-emerald-100 text-emerald-700 dark:bg-emerald-300/15 dark:text-emerald-300',
    }[s] ?? 'bg-ink-100 text-ink-500')

// ── Priority ───────────────────────────────────────────────────────────
export const priorityClass = p =>
    ({
        low:    'bg-sage-100 text-sage-700 dark:bg-sage-300/15 dark:text-sage-300',
        medium: 'bg-amber-100 text-amber-700 dark:bg-amber-300/15 dark:text-amber-300',
        high:   'bg-red-100 text-red-700 dark:bg-red-300/15 dark:text-red-300',
    }[p] ?? 'bg-ink-100 text-ink-500')

// ── Project status ─────────────────────────────────────────────────────
export const projectStatusLabel = s =>
    ({ planning: 'Planning', in_progress: 'In Progress', on_hold: 'On Hold', completed: 'Completed' }[s] ?? s)

export const projectStatusClass = s =>
    ({
        planning:    'bg-ink-100 text-ink-600 dark:bg-ink-800 dark:text-ink-300',
        in_progress: 'bg-sage-100 text-sage-700 dark:bg-sage-300/15 dark:text-sage-300',
        on_hold:     'bg-amber-100 text-amber-700 dark:bg-amber-300/15 dark:text-amber-300',
        completed:   'bg-emerald-100 text-emerald-700 dark:bg-emerald-300/15 dark:text-emerald-300',
    }[s] ?? 'bg-ink-100 text-ink-500')

// ── Specialization ─────────────────────────────────────────────────────
export const specializationLabel = s =>
    ({
        frontend:   'Frontend Dev',
        backend:    'Backend Dev',
        ui_ux:      'UI/UX Designer',
        qa:         'QA Engineer',
        devops:     'DevOps',
        mobile:     'Mobile Dev',
        pentesting: 'Pentesting',
    }[s] ?? '—')
