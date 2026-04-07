# 🗂️ Project Management System

Sistem manajemen proyek berbasis web untuk PT yang bergerak di bidang IT.  
Dibangun dengan Laravel 13, Filament v5, Inertia.js, dan Vue 3.

---

## 🧰 Tech Stack

| Layer | Teknologi |
|-------|-----------|
| Backend | Laravel 13 + PHP 8.3 |
| Admin Panel | Filament v5 |
| Frontend | Inertia.js + Vue 3 |
| Styling | Tailwind CSS |
| Permission | Spatie Laravel Permission |
| Database | MySQL |

---

## ✅ Requirements

Pastikan semua ini sudah terinstall di komputer kamu sebelum mulai:

| Tool | Cek dengan |
|------|-----------|
| PHP 8.3+ | `php -v` |
| Composer | `composer -v` |
| Node.js & NPM | `node -v` dan `npm -v` |
| MySQL | Jalankan Laragon/XAMPP |
| Git | `git -v` |

> 💡 Direkomendasikan pakai **Laragon** karena sudah include PHP, MySQL, dan virtual host otomatis.

---

## 🚀 Setup dari Nol (Wajib Dibaca)

Ikuti langkah ini secara berurutan. Jangan skip.

### Step 1 — Clone Repository

Buka terminal, lalu jalankan:

```bash
git clone <repository-url>
cd project-management
```

> Ganti `<repository-url>` dengan URL repo GitHub yang dikasih.

---

### Step 2 — Install Dependencies PHP

```bash
composer install
```

> Proses ini akan download semua package Laravel. Tunggu sampai selesai.

---

### Step 3 — Install Dependencies JavaScript

```bash
npm install
```

---

### Step 4 — Setup File Environment

```bash
cp .env.example .env
php artisan key:generate
```

Perintah ini akan:
- Menyalin file `.env.example` menjadi `.env`
- Generate app key untuk enkripsi

---

### Step 5 — Konfigurasi Database

**Pertama**, buat database baru di phpMyAdmin atau HeidiSQL:
- Nama database: `project_management`
- Collation: `utf8mb4_unicode_ci`

**Kedua**, buka file `.env` dan sesuaikan bagian ini:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=project_management
DB_USERNAME=root
DB_PASSWORD=
```

> Kalau password MySQL kamu bukan kosong, isi sesuai password kamu.

---

### Step 6 — Jalankan Migration

```bash
php artisan migrate
```

Perintah ini akan membuat semua tabel di database secara otomatis.

Output yang benar kira-kira seperti ini:
```
INFO  Running migrations.
  ✓ 0001_01_01_000000_create_users_table
  ✓ 0001_01_01_000001_create_cache_table
  ✓ 2026_04_07_052015_create_permission_tables
  ✓ 2026_04_07_052234_create_projects_table
  ✓ 2026_04_07_052242_create_tasks_table
  ...
```

---

### Step 7 — Buat Admin User

```bash
php artisan make:filament-user
```

Isi saat diminta:
- **Name**: `Admin`
- **Email**: `admin@admin.com`
- **Password**: `password`

**Penting** — Setelah user dibuat, update role-nya supaya bisa akses admin panel:

```bash
php artisan tinker
```

```php
App\Models\User::find(1)->update(['role' => 'admin']);
exit
```

---

### Step 8 — Jalankan Aplikasi

Buka **dua terminal** secara bersamaan:

**Terminal 1:**
```bash
php artisan serve
```

**Terminal 2:**
```bash
npm run dev
```

> Kalau pakai Laragon, `php artisan serve` tidak perlu — cukup jalankan `npm run dev` saja karena Laragon sudah handle server-nya.

---

### Step 9 — Akses Aplikasi

| URL | Keterangan |
|-----|-----------|
| `http://project-management.test` | Halaman utama (developer view) |
| `http://project-management.test/admin` | Admin panel (Filament) |

Login admin panel dengan:
- Email: `admin@admin.com`
- Password: `password`

---

## 🗂️ Struktur Folder Penting

```
app/
├── Filament/
│   └── Resources/
│       ├── Projects/       → CRUD Projects di admin panel
│       │   ├── Schemas/    → Form fields
│       │   └── Tables/     → Table columns
│       ├── Tasks/          → CRUD Tasks di admin panel
│       └── Users/          → CRUD Users di admin panel
├── Models/
│   ├── User.php            → Model user
│   ├── Project.php         → Model project
│   └── Task.php            → Model task
└── Http/
    ├── Controllers/        → Controllers untuk Inertia pages
    └── Middleware/
        └── HandleInertiaRequests.php  → Shared data ke Vue (auth user)

resources/
├── js/
│   ├── Pages/              → ⭐ Taruh Vue pages di sini
│   │   └── Home.vue        → Contoh halaman
│   └── Layouts/
│       └── AppLayout.vue   → Layout utama (navbar + wrapper)
└── views/
    └── app.blade.php       → Root template Inertia (jangan diubah)

database/
├── migrations/             → Struktur tabel database
└── seeders/                → Dummy data
```

---

## 🖼️ Cara Membuat Halaman Vue Baru

Semua halaman Vue taruh di `resources/js/Pages/`.

Contoh membuat halaman Projects:

**1. Buat file** `resources/js/Pages/Projects/Index.vue`:

```vue
<template>
    <AppLayout>
        <h1 class="text-2xl font-bold">Daftar Projects</h1>
        <!-- konten di sini -->
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
</script>
```

**2. Tambah route** di `routes/web.php`:

```php
use Inertia\Inertia;

Route::get('/projects', function () {
    return Inertia::render('Projects/Index');
});
```

---

## 🔀 Git Workflow

### Struktur Branch

```
main          → kode final yang sudah siap (jangan disentuh langsung)
develop       → tempat gabung semua fitur
feature/xxx   → branch kerja masing-masing orang
```

### Alur Kerja Harian

**Sebelum mulai kerja — selalu pull dulu:**
```bash
git checkout develop
git pull origin develop
git checkout -b feature/nama-fitur-kamu
```

**Setelah selesai ngoding:**
```bash
git add .
git commit -m "feat: deskripsi singkat apa yang dikerjakan"
git push origin feature/nama-fitur-kamu
```

**Kemudian** buat Pull Request di GitHub dari branch kamu ke `develop`.

> ⚠️ JANGAN pernah push langsung ke `main` atau `develop`.  
> Selalu lewat Pull Request.

### Contoh Nama Branch yang Benar

```
feature/halaman-dashboard
feature/crud-task-vue
feature/seeder-dummy-data
fix/bug-login-redirect
```

---

## 👥 Pembagian Tugas

| Anggota | Tugas | Branch |
|---------|-------|--------|
| Orang 1 | Filament Resources — polish form & table di admin panel | `feature/admin-panel` |
| Orang 2 | Inertia + Vue Pages — halaman developer (dashboard, projects, tasks) | `feature/vue-pages` |
| Orang 3 | Seeder dummy data, testing, dan polish UI | `feature/seeder-dan-ui` |

---

## 🔧 Shared Data (HandleInertiaRequests)

File `app/Http/Middleware/HandleInertiaRequests.php` sudah dikonfigurasi untuk mengirim data auth ke semua Vue pages.

Data yang tersedia di semua halaman Vue:

```javascript
// Akses di Vue component
const page = usePage()
console.log(page.props.auth.user)
// { id, name, email, role }
```

Contoh penggunaan di Vue:

```vue
<script setup>
import { usePage } from '@inertiajs/vue3'

const { props } = usePage()
const user = props.auth.user
</script>

<template>
    <p>Halo, {{ user.name }}!</p>
</template>
```

---

## ❌ Troubleshooting

**Error: These credentials do not match our records**
→ User belum dibuat atau role belum diupdate ke `admin`. Ulangi Step 7.

**Error: SQLSTATE Connection Refused**
→ MySQL belum jalan. Buka Laragon/XAMPP dan start MySQL dulu.

**Error: Class not found setelah clone**
→ Jalankan `composer install` dan `npm install` ulang.

**Halaman putih / blank setelah login**
→ Pastikan `npm run dev` sedang berjalan di terminal.

**Migration error: Table already exists**
→ Jalankan `php artisan migrate:fresh` — tapi hati-hati, ini akan hapus semua data.

**Port conflict di Laragon**
→ Ganti port di Laragon settings atau matikan aplikasi lain yang pakai port 80/3306.