# Backend Documentation

Dokumentasi lengkap backend untuk tim frontend.

---

## Tech Stack

- **Laravel 13** + PHP 8.3
- **Inertia.js** — semua response pakai `Inertia::render()`, bukan JSON API biasa
- **Spatie Laravel Permission** — role management
- **Database** — MySQL

---

## Roles

| Role | Akses |
|------|-------|
| `admin` | Semua data, semua aksi |
| `project_manager` | Semua task di project yang dia manage, semua project |
| `developer` | Hanya task yang di-assign ke dia, hanya project yang dia ikuti |

---

## Shared Data (tersedia di semua halaman Vue)

Akses via `usePage().props`:

```js
const { props } = usePage()

props.auth.user = {
  id: 1,
  name: "Budi",
  email: "budi@mail.com",
  role: "developer",                  // 'admin' | 'project_manager' | 'developer'
  unread_notifications_count: 3
}

props.flash = {
  success: "Status task berhasil diubah.",   // atau null
  error: null
}
```

Contoh pakai flash untuk toast:
```js
watch(() => page.props.flash.success, (msg) => {
  if (msg) showToast(msg)
})
```

---

## Routes & Data

### Auth

#### `GET /login`
Render halaman login. Kalau sudah login → redirect ke `/dashboard`.

Render: `Auth/Login`

#### `POST /login` — throttle 5x/menit per email+IP
```js
// Request body
{ email, password, remember? }

// Sukses → redirect ke /dashboard
// Gagal → back + errors.email = "Email atau password salah."
```

#### `POST /logout`
Logout dan redirect ke `/login`.

---

### Dashboard

#### `GET /dashboard`
Render: `Dashboard`

```js
props = {
  stats: {
    total_projects: 10,
    total_tasks: 48,
    total_users: 12,
    my_tasks: 5,
    overdue_tasks: 2,      // task yang due date-nya sudah lewat & belum done
    warning_tasks: 1       // task yang due date-nya H-3 atau kurang & belum done
  },
  recentProjects: [
    {
      id, name, status, priority, end_date, created_by,
      tasks_count: 8,
      tasks_done_count: 3,
      progress: 38,        // % (0-100)
      creator: { id, name }
    }
  ],
  myTasks: [
    {
      id, title, status, priority, due_date, project_id,
      deadline_status: 'overdue' | 'warning' | 'upcoming' | null,
      project: { id, name }
    }
  ]
}
```

---

### Projects

#### `GET /projects`
Render: `Projects/Index`

Query params (opsional): `?search=nama&status=in_progress&priority=high`

**Visibility:**
- `admin` / `project_manager` → semua project
- `developer` → hanya project yang dia ikuti sebagai member

```js
props = {
  projects: {              // paginated
    data: [
      {
        id, name, description, status, priority,
        start_date, end_date, created_by,
        tasks_count, tasks_done_count,
        progress,          // % (0-100)
        creator: { id, name },
        members: [{ id, name, pivot: { role } }]
      }
    ],
    links: {...},
    meta: { current_page, last_page, total, ... }
  },
  filters: { search, status, priority }
}
```

Status values: `planning` | `in_progress` | `on_hold` | `completed`

Priority values: `low` | `medium` | `high`

#### `GET /projects/{id}`
Render: `Projects/Show`

> 403 kalau developer bukan member project ini.

```js
props = {
  project: {
    id, name, description, status, priority,
    start_date, end_date, created_by,
    tasks_count, tasks_done_count, progress,
    creator: { id, name },
    members: [{ id, name, pivot: { role } }],
    tasks: [
      {
        id, title, status, priority, due_date,
        assignee: { id, name }
      }
    ]
  }
}
```

---

### Tasks

#### `GET /tasks`
Render: `Tasks/Index`

Query params (opsional): `?status=todo&priority=high&project_id=1`

**Visibility:**
- `admin` → semua task
- `project_manager` → semua task di project yang dia manage
- `developer` → hanya task yang di-assign ke dia

```js
props = {
  tasks: {                 // paginated, 15 per halaman
    data: [
      {
        id, title, status, priority, due_date,
        deadline_status: 'overdue' | 'warning' | 'upcoming' | null,
        project: { id, name },
        assignee: { id, name }
      }
    ],
    links: {...},
    meta: {...}
  },
  projects: [{ id, name }],   // untuk dropdown filter
  filters: { status, priority, project_id }
}
```

Status values: `todo` | `in_progress` | `review` | `done`

#### `GET /tasks/{id}`
Render: `Tasks/Show`

> 403 kalau user bukan member project dari task ini.

```js
props = {
  task: {
    id, title, description, status, priority, due_date,
    deadline_status: 'overdue' | 'warning' | 'upcoming' | null,
    project: { id, name },
    assignee: { id, name },
    creator: { id, name },
    comments: [
      {
        id, body, created_at,
        user: { id, name }
      }
    ],
    activities: [
      {
        id, action, description, created_at,
        user: { id, name }
      }
    ]
  }
}
```

#### `PATCH /tasks/{id}/status`
Update status task.

> 403 kalau bukan assignee / creator / manager project / admin.

```js
// Request body
{ status: 'in_progress' }   // 'todo' | 'in_progress' | 'review' | 'done'

// Sukses → back() + flash.success
```

---

### Comments

#### `POST /tasks/{id}/comments`
Tambah komentar ke task.

> 403 kalau user bukan member project dari task ini.

```js
// Request body
{ body: "isi komentar..." }   // max 2000 karakter

// Sukses → back() + flash.success
```

#### `DELETE /comments/{id}`
Hapus komentar.

> 403 kalau bukan pemilik komentar atau admin.

```js
// Sukses → back() + flash.success
```

---

### Notifications

#### `GET /notifications`
Render: `Notifications/Index`

```js
props = {
  notifications: {           // paginated, 20 per halaman
    data: [
      {
        id,
        read_at,             // null = belum dibaca
        created_at,
        data: {
          type: 'task_assigned' | 'task_commented' | 'task_status_changed',
          message: "Budi menugaskan task ...",
          task_id: 5,
          task_title: "Fix login bug",
          // task_status_changed juga ada:
          old_status: "todo",
          new_status: "in_progress"
        }
      }
    ],
    links: {...},
    meta: {...}
  }
}
```

#### `PATCH /notifications/{id}/read`
Tandai satu notifikasi sudah dibaca.

#### `PATCH /notifications/read-all`
Tandai semua notifikasi sudah dibaca.

---

## Kapan Notifikasi Dikirim

| Event | Penerima |
|-------|----------|
| Task di-assign (buat/re-assign) | Assignee baru |
| Status task berubah | Assignee + Creator (kecuali yang ubah) |
| Komentar ditambah | Assignee + Creator (kecuali yang komen) |

---

## Inertia Form Submission

Pakai `useForm` dari Inertia:

```js
import { useForm } from '@inertiajs/vue3'

// Login
const form = useForm({ email: '', password: '', remember: false })
form.post('/login')

// Update status task
const form = useForm({ status: 'in_progress' })
form.patch(`/tasks/${task.id}/status`)

// Tambah komentar
const form = useForm({ body: '' })
form.post(`/tasks/${task.id}/comments`)

// Hapus komentar
router.delete(`/comments/${comment.id}`)

// Logout
router.post('/logout')

// Mark notif as read
router.patch(`/notifications/${id}/read`)
router.patch('/notifications/read-all')
```

---

## Halaman Vue yang Perlu Dibuat

| Path | Render dari route |
|------|-------------------|
| `Pages/Auth/Login.vue` | `GET /login` |
| `Pages/Dashboard.vue` | `GET /dashboard` |
| `Pages/Projects/Index.vue` | `GET /projects` |
| `Pages/Projects/Show.vue` | `GET /projects/{id}` |
| `Pages/Tasks/Index.vue` | `GET /tasks` |
| `Pages/Tasks/Show.vue` | `GET /tasks/{id}` |
| `Pages/Notifications/Index.vue` | `GET /notifications` |

---

## Error Responses

| Code | Artinya |
|------|---------|
| 403 | Tidak punya akses (unauthorized) |
| 422 | Validasi gagal — cek `$page.props.errors` |
| 429 | Rate limit login terlampaui (5x/menit) |
