# Role & Responsibilities

## Ringkasan Akses

| Fitur | Admin | Project Manager | Developer |
|---|:---:|:---:|:---:|
| Filament Admin Panel | ✅ | ❌ | ❌ |
| Filament: Manage Projects | ✅ | ❌ | ❌ |
| Filament: Manage Tasks | ✅ | ❌ | ❌ |
| Filament: Manage Users | ✅ | ❌ | ❌ |
| User App: Dashboard | ✅ | ✅ (PM view) | ✅ (Dev view) |
| User App: Projects | ✅ | ✅ (project sendiri) | ✅ (project sendiri) |
| User App: Tasks | ✅ | ✅ (semua task project) | ✅ (task sendiri) |
| Approve / Reject task | ❌ | ✅ | ❌ |
| Update status task | ❌ | ❌ | ✅ |

---

## Admin

**Akses:** User app + Filament admin panel (satu-satunya role yang bisa masuk `/admin`)

### Filament (`/admin`)
- Membuat, mengedit, dan menghapus **Projects** (nama, deskripsi, status, priority, start/end date)
- Membuat, mengedit, dan menghapus **Tasks**
- Mengelola **Users** (CRUD user, assign role)

### User App
- Dapat mengakses semua halaman (`/dashboard`, `/projects`, `/tasks`, `/profile`, `/notifications`)
- Dapat melihat semua project dan task tanpa filter

---

## Project Manager

**Akses:** User app saja (tidak bisa masuk `/admin`)

### User App
- **Dashboard** — statistik project yang dikelola, daftar task pending review, jumlah anggota tim, chart task by status & priority
- **Projects** — hanya melihat project di mana dia terdaftar sebagai `manager` di tabel `project_members`
- **Tasks** — melihat semua task dari project yang dikelola
- **Kanban** — melihat task dalam tampilan board kanban
- **Approve task** — menyetujui task yang masuk status `review` → status berubah menjadi `done`
- **Reject task** — menolak task review → task kembali ke `in_progress` + wajib isi alasan penolakan
- **Notifications** — menerima notifikasi saat status task berubah

---

## Developer

**Akses:** User app saja (tidak bisa masuk `/admin`)

### User App
- **Dashboard** — statistik task sendiri, recent projects, daftar task aktif, chart by status & priority
- **Projects** — hanya melihat project di mana dia terdaftar sebagai member di tabel `project_members`
- **Tasks** — hanya melihat task yang di-assign ke dirinya (`assigned_to = user.id`)
- **Update status task** — menggeser status task milik sendiri:
  - `todo` → `in_progress` → `review`
  - Saat submit ke `review` wajib mengisi **proof** (teks deskripsi + file opsional)
  - Tidak bisa langsung set ke `done`, harus menunggu approval dari Project Manager
- **Komentar** — menambah dan menghapus komentar di task
- **Notifications** — menerima notifikasi saat di-assign task baru atau status task berubah

---

## Alur Kerja

```
Admin                    Project Manager              Developer
  |                            |                          |
  |  Buat Project              |                          |
  |  (via Filament)            |                          |
  |                            |                          |
  |                            |  Buat Task               |
  |                            |  (via Filament)          |
  |                            |  Assign ke Developer     |
  |                            |------------------------->|
  |                            |                          |
  |                            |         Notifikasi task baru
  |                            |                          |
  |                            |                          |  Kerjain task
  |                            |                          |  todo → in_progress
  |                            |                          |
  |                            |                          |  Submit review
  |                            |                          |  + upload proof
  |                            |<-------------------------|
  |                            |                          |
  |                            |  Review proof            |
  |                            |                          |
  |                            |  [Approve] → done        |
  |                            |------------------------->|
  |                            |                          |
  |                            |  [Reject] → in_progress  |
  |                            |  + isi alasan            |
  |                            |------------------------->|
  |                            |         Notifikasi hasil review
```

---

## Status Flow Task

```
todo  →  in_progress  →  review  →  done
                  ↑______________|
                  (jika di-reject PM)
```

| Status | Siapa yang bisa set | Keterangan |
|---|---|---|
| `todo` | Project Manager (saat create) | Status awal task |
| `in_progress` | Developer | Task sedang dikerjakan |
| `review` | Developer | Wajib upload proof |
| `done` | Project Manager (approve) | Task selesai |
