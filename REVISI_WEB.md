# 📋 Instruksi Revisi Web App — Project Management Tool

> Dokumen ini berisi daftar revisi dan fitur baru berdasarkan hasil review.  
> Implementasikan semua poin di bawah secara berurutan.

---

## 🗂️ 1. My Tasks — Perbaikan Tampilan & Logika

### 1.1 Daftar My Tasks
- Tampilan default daftar "My Tasks" harus menggunakan **Short View** (ringkas), bukan tampilan penuh.

### 1.2 Flowchart / Alur Koreksi
- Perbaiki dan koreksi alur flowchart yang sudah ada agar sesuai dengan logika terbaru.

### 1.3 Status Task
- Tentukan dan definisikan **status apa saja yang perlu ada** pada task (contoh: To Do, In Progress, Done, dll).
- Pastikan status ter-render dengan benar di semua view.

### 1.4 Status Otomatis
- Status task harus **berubah otomatis** (tidak perlu set manual oleh user).
- Hilangkan tampilan status dalam format tabel; ganti dengan representasi yang lebih visual/dinamis.

### 1.5 Field "Created By" → "Send To"
- Ganti label / field **"Created By"** menjadi **"Send To"** atau sesuaikan logika pengirim task.

### 1.6 Pemisahan Role: PM & Developer
- **PM** dan **Developer** harus dipisah secara eksplisit dalam sistem.
- Pastikan task yang dibuat oleh PM tidak tercampur dengan task milik Developer.

### 1.7 Task Baru — Project Tidak Muncul
- **Bug Fix**: Saat membuat task baru, nama project tidak muncul / tidak ter-assign.
- Pastikan dropdown atau field project muncul dan bisa dipilih saat membuat task baru.

### 1.8 Anggota Task
- Anggota yang bisa di-assign ke task **hanya anggota yang terdaftar di project tersebut**, bukan semua user.

### 1.9 Button Refresh → List View
- Tombol **refresh/ngerefresh** harus mengarahkan / kembali ke **List View**, bukan view lain.

---

## 👥 2. Multiple Tasks & Project per User

- Satu orang bisa terdaftar dan aktif di **beberapa project sekaligus** (multiple project).
- Pastikan relasi user ↔ project bersifat **many-to-many**.

### Yang perlu diperbarui:
- **Database**: Sesuaikan skema agar mendukung relasi many-to-many antara user dan project.
- **UI/UX**: Tampilkan semua project yang dimiliki user di halaman utama / dashboard.
- **Dashboard**: Update tampilan agar mencerminkan multi-project per user.
- **Fungsi / Logic**: Pastikan semua fungsi (filter, assign, report) sudah support multi-project.
- **Department & Role**: Tambahkan fitur pembuatan Department dan penambahan Role baru oleh admin.

---

## 📊 3. Dashboard & Feedback

### 3.1 Komentar pada Task Done
- Jika task sudah berstatus **Done**, user **tidak bisa lagi menambahkan komentar** (read-only).

### 3.2 Anggota Tim di Task
- Tampilan anggota tim di dalam task **tidak perlu terlalu banyak ditampilkan** — cukup avatar ringkas atau jumlah saja.

### 3.3 Tim Performance di Dashboard
- Tambahkan **section Tim Performance** di halaman Dashboard.
- Tampilkan metrik performa tim (jumlah task selesai, on-time, dll).

### 3.4 Dashboard Bisa Dilihat Admin
- Halaman Dashboard **harus bisa diakses oleh role Admin**.
- Pastikan data yang tampil di dashboard Admin mencakup seluruh project / tim.

### 3.5 Tambahkan Hover Tooltip
- Tambahkan **hover tooltip** pada elemen-elemen penting (status, avatar anggota, deadline, dll) untuk memberikan info tambahan saat di-hover.

### 3.6 ~~Board Custom~~ (Dibatalkan)
- ~~Fitur Tambah Board Custom~~ — **tidak jadi diimplementasikan**.

---

## 🧪 4. Skenario & User Flow

### 4.1 Buat Skenario
- Fitur **Skenario** perlu dibuat. Skenario mendefinisikan alur / flow penggunaan fitur tertentu.

### 4.2 User Flow untuk Skenario
- Gunakan pendekatan **User Flow** sebagai dasar pembuatan skenario.
- Dokumentasikan tiap langkah user dalam menggunakan fitur utama.

---

## 📅 5. Project — Tambahan Field Tanggal

Pada halaman / form **Project**, tambahkan field berikut:

| Field | Keterangan |
|---|---|
| **Start Date** | Tanggal mulai project |
| **End Date** | Tanggal selesai project (ganti dari "Deadline") |
| **Realisasi Date** | Tanggal realisasi aktual selesainya project |

- Hapus label "Deadline", ganti dengan **End Date**.
- Tambahkan field baru **Realisasi Date** untuk mencatat kapan project benar-benar selesai.

---

## ✅ Prioritas Pengerjaan (Saran Urutan)

1. Bug Fix: Task baru → project tidak muncul *(blocker)*
2. Pemisahan role PM & Developer
3. Status otomatis & definisi status
4. Anggota task hanya dari project
5. Field tanggal di Project (Start Date, End Date, Realisasi Date)
6. Multiple project per user (DB + UI)
7. Dashboard: Tim Performance + akses Admin
8. Hover tooltip
9. Komentar disabled saat task Done
10. Fitur Skenario + User Flow
11. Department & Role management
