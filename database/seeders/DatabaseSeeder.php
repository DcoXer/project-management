<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    const SPECIALIZATIONS = ['backend', 'frontend', 'ui/ux'];

    const PROJECT_NAMES = [
        'Pengembangan Platform E-Commerce',
        'Aplikasi Mobile Banking',
        'Sistem Manajemen Inventaris',
        'Dashboard Analytics Perusahaan',
        'Integrasi API Payment Gateway',
        'Redesain UI/UX Website Korporat',
        'Sistem Monitoring Infrastruktur',
        'Aplikasi HR & Payroll Internal',
        'Platform Belajar Online (LMS)',
        'Migrasi Database ke Cloud',
        'Chatbot Customer Service',
        'Sistem Absensi & Penggajian',
    ];

    const TASKS_BY_SPEC = [
        'backend' => [
            'Desain skema database relasional',
            'Buat endpoint REST API autentikasi',
            'Implementasi middleware rate limiting',
            'Optimasi query database dengan indexing',
            'Integrasi third-party payment API',
            'Setup queue & job processing',
            'Implementasi caching dengan Redis',
            'Unit testing modul core bisnis',
            'Buat endpoint notifikasi real-time',
            'Implementasi fitur export laporan PDF',
        ],
        'frontend' => [
            'Implementasi halaman login & register',
            'Integrasi komponen reusable dengan Vue',
            'Optimasi performa rendering halaman',
            'Implementasi dark mode',
            'Buat halaman dashboard utama',
            'Responsive layout untuk mobile',
            'Integrasi dengan REST API backend',
            'Implementasi form validasi client-side',
            'Buat komponen tabel dengan pagination',
            'Animasi transisi halaman',
        ],
        'ui/ux' => [
            'Riset user persona dan user journey',
            'Buat wireframe halaman utama',
            'Desain high-fidelity mockup dashboard',
            'User testing & iterasi desain',
            'Buat design system & komponen library',
            'Presentasi desain ke stakeholder',
            'Finalisasi prototype interaktif',
            'Audit aksesibilitas UI (WCAG)',
            'Desain onboarding flow pengguna baru',
            'Review konsistensi visual seluruh halaman',
        ],
    ];

    public function run(): void
    {
        // ─── ADMIN ────────────────────────────────────────────────────────
        $admin = User::factory()->admin()->create([
            'name'     => 'Super Admin',
            'email'    => 'admin@admin.com',
            'password' => Hash::make('password'),
        ]);

        // ─── PROJECT MANAGERS ─────────────────────────────────────────────
        $managers = collect([
            User::factory()->projectManager()->create([
                'name'     => 'Budi Santoso',
                'email'    => 'budi@pm.com',
                'password' => Hash::make('password'),
            ]),
            User::factory()->projectManager()->create([
                'name'     => 'Siti Rahayu',
                'email'    => 'siti@pm.com',
                'password' => Hash::make('password'),
            ]),
            User::factory()->projectManager()->create([
                'name'     => 'Ahmad Fauzi',
                'email'    => 'ahmad@pm.com',
                'password' => Hash::make('password'),
            ]),
        ]);

        // ─── DEVELOPERS (specialization: backend, frontend, ui/ux) ────────
        $devData = [
            // Backend
            ['name' => 'Hendra Wijaya',   'email' => 'hendra@dev.com',  'spec' => 'backend'],
            ['name' => 'Mega Kusuma',     'email' => 'mega@dev.com',    'spec' => 'backend'],
            ['name' => 'Fajar Nugroho',   'email' => 'fajar@dev.com',   'spec' => 'backend'],
            // Frontend
            ['name' => 'Rizky Pratama',   'email' => 'rizky@dev.com',   'spec' => 'frontend'],
            ['name' => 'Dewi Anggraini',  'email' => 'dewi@dev.com',    'spec' => 'frontend'],
            ['name' => 'Andi Firmansyah', 'email' => 'andi@dev.com',    'spec' => 'frontend'],
            // UI/UX
            ['name' => 'Nadia Putri',     'email' => 'nadia@dev.com',   'spec' => 'ui/ux'],
            ['name' => 'Citra Lestari',   'email' => 'citra@dev.com',   'spec' => 'ui/ux'],
        ];

        $developers = collect();
        foreach ($devData as $d) {
            $developers->push(
                User::factory()->developer($d['spec'])->create([
                    'name'     => $d['name'],
                    'email'    => $d['email'],
                    'password' => Hash::make('password'),
                ])
            );
        }

        // ─── PROJECTS & TASKS ─────────────────────────────────────────────
        // Flow: Admin buat project → set created_by ke PM → PM auto jadi member
        // PM buat tasks → assign developer → developer auto jadi member

        $projectNames = collect(self::PROJECT_NAMES)->shuffle();
        $nameIndex    = 0;

        foreach ($managers as $manager) {
            // Tiap manager dapat 2 project dari admin
            for ($p = 0; $p < 2; $p++) {
                $project = Project::factory()->create([
                    'name'       => $projectNames[$nameIndex++] ?? fake()->words(3, true),
                    'created_by' => $manager->id, // Admin set PM sebagai creator
                ]);

                // PM otomatis jadi member dengan role manager (simulasi afterCreate hook)
                $project->members()->attach($manager->id, [
                    'role'           => 'manager',
                    'specialization' => null,
                ]);

                // ── PM buat tasks, assign ke developer ────────────────────
                // Pilih 4-6 developer secara acak untuk project ini
                $projectDevs = $developers->shuffle()->take(rand(4, 6));
                $taskCount   = rand(5, 9);

                for ($t = 0; $t < $taskCount; $t++) {
                    $assignedDev = $projectDevs->random();
                    $devSpec     = $assignedDev->specialization;

                    $taskTitles = self::TASKS_BY_SPEC[$devSpec];
                    $title      = $taskTitles[array_rand($taskTitles)];

                    // PM buat task
                    Task::factory()->create([
                        'project_id'  => $project->id,
                        'title'       => $title,
                        'assigned_to' => $assignedDev->id,
                        'created_by'  => $manager->id,
                    ]);

                    // Developer otomatis jadi member project (simulasi auto-attach di store())
                    $alreadyMember = $project->members()->where('user_id', $assignedDev->id)->exists();
                    if (! $alreadyMember) {
                        $project->members()->attach($assignedDev->id, [
                            'role'           => 'developer',
                            'specialization' => $devSpec,
                        ]);
                    }
                }
            }
        }

        // ─── RINGKASAN ────────────────────────────────────────────────────
        $this->command->info('');
        $this->command->info('Seeder selesai! Akun yang tersedia:');
        $this->command->info('');
        $this->command->table(
            ['Role', 'Specialization', 'Email', 'Password'],
            [
                ['Admin',           '-',        'admin@admin.com', 'password'],
                ['Project Manager', '-',        'budi@pm.com',     'password'],
                ['Project Manager', '-',        'siti@pm.com',     'password'],
                ['Project Manager', '-',        'ahmad@pm.com',    'password'],
                ['Developer',       'Backend',  'hendra@dev.com',  'password'],
                ['Developer',       'Backend',  'mega@dev.com',    'password'],
                ['Developer',       'Backend',  'fajar@dev.com',   'password'],
                ['Developer',       'Frontend', 'rizky@dev.com',   'password'],
                ['Developer',       'Frontend', 'dewi@dev.com',    'password'],
                ['Developer',       'Frontend', 'andi@dev.com',    'password'],
                ['Developer',       'UI/UX',    'nadia@dev.com',   'password'],
                ['Developer',       'UI/UX',    'citra@dev.com',   'password'],
            ]
        );
        $this->command->info('');
    }
}
