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

    // Spesialisasi yang tersedia
    const SPECIALIZATIONS = [
        'frontend',
        'backend',
        'ui_ux',
        'qa',
        'devops',
        'mobile',
        'pentesting',
    ];

    // Nama project IT yang realistis
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
        'Security Audit & Pentesting System',
        'Migrasi Database ke Cloud',
        'Chatbot Customer Service',
    ];

    // Task yang realistis per spesialisasi
    const TASKS_BY_SPEC = [
        'frontend' => [
            'Implementasi halaman login & register',
            'Integrasi komponen reusable dengan Vue',
            'Optimasi performa rendering halaman',
            'Implementasi dark mode',
            'Buat halaman dashboard utama',
            'Responsive layout untuk mobile',
            'Integrasi dengan REST API backend',
            'Implementasi form validasi client-side',
        ],
        'backend' => [
            'Desain skema database relasional',
            'Buat endpoint REST API autentikasi',
            'Implementasi middleware rate limiting',
            'Optimasi query database dengan indexing',
            'Integrasi third-party payment API',
            'Setup queue & job processing',
            'Implementasi caching dengan Redis',
            'Unit testing modul core bisnis',
        ],
        'ui_ux' => [
            'Riset user persona dan user journey',
            'Buat wireframe halaman utama',
            'Desain high-fidelity mockup dashboard',
            'User testing & iterasi desain',
            'Buat design system & komponen library',
            'Presentasi desain ke stakeholder',
            'Finalisasi prototype interaktif',
        ],
        'qa' => [
            'Buat test plan & test case',
            'Regression testing fitur login',
            'Performance testing load balancing',
            'Bug report & dokumentasi issue',
            'Automation testing dengan Selenium',
            'UAT (User Acceptance Testing)',
            'API testing dengan Postman',
        ],
        'devops' => [
            'Setup CI/CD pipeline dengan GitHub Actions',
            'Konfigurasi Docker container',
            'Deploy ke staging environment',
            'Monitoring server dengan Grafana',
            'Backup database otomatis',
            'Setup SSL & konfigurasi Nginx',
            'Optimasi konfigurasi cloud server',
        ],
        'mobile' => [
            'Setup project React Native / Flutter',
            'Implementasi push notification',
            'Integrasi API backend ke mobile',
            'Implementasi offline mode',
            'UI screen onboarding aplikasi',
            'Testing di berbagai ukuran device',
            'Submit ke Google Play Store',
        ],
        'pentesting' => [
            'Vulnerability assessment aplikasi web',
            'SQL injection & XSS penetration test',
            'Analisis keamanan autentikasi',
            'Laporan temuan kerentanan keamanan',
            'Test OWASP Top 10 vulnerabilities',
            'Social engineering simulation',
            'Remediation & patch verification',
        ],
    ];

    public function run(): void
    {
        // ─── ADMIN ───────────────────────────────────────────────
        $admin = User::factory()->admin()->create([
            'name'     => 'Super Admin',
            'email'    => 'admin@admin.com',
            'password' => Hash::make('password'),
        ]);

        // ─── PROJECT MANAGERS (dengan kredensial jelas) ──────────
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

        // ─── DEVELOPERS (dengan spesialisasi beragam) ────────────
        $devData = [
            ['name' => 'Rizky Pratama',   'email' => 'rizky@dev.com',   'spec' => 'frontend'],
            ['name' => 'Dewi Anggraini',  'email' => 'dewi@dev.com',    'spec' => 'frontend'],
            ['name' => 'Hendra Wijaya',   'email' => 'hendra@dev.com',  'spec' => 'backend'],
            ['name' => 'Mega Kusuma',     'email' => 'mega@dev.com',    'spec' => 'backend'],
            ['name' => 'Fajar Nugroho',   'email' => 'fajar@dev.com',   'spec' => 'backend'],
            ['name' => 'Nadia Putri',     'email' => 'nadia@dev.com',   'spec' => 'ui_ux'],
            ['name' => 'Kevin Salim',     'email' => 'kevin@dev.com',   'spec' => 'mobile'],
            ['name' => 'Laras Wulandari', 'email' => 'laras@dev.com',   'spec' => 'qa'],
            ['name' => 'Dimas Prasetyo',  'email' => 'dimas@dev.com',   'spec' => 'devops'],
            ['name' => 'Citra Lestari',   'email' => 'citra@dev.com',   'spec' => 'pentesting'],
            ['name' => 'Andi Firmansyah', 'email' => 'andi@dev.com',    'spec' => 'frontend'],
            ['name' => 'Putri Handayani', 'email' => 'putri@dev.com',   'spec' => 'qa'],
        ];

        $developers = collect();
        foreach ($devData as $d) {
            $developers->push(
                User::factory()->create([
                    'name'     => $d['name'],
                    'email'    => $d['email'],
                    'password' => Hash::make('password'),
                    'role'     => 'developer',
                ])
            );
        }

        // ─── PROJECTS ────────────────────────────────────────────
        $projectNames = collect(self::PROJECT_NAMES)->shuffle();
        $nameIndex    = 0;

        foreach ($managers as $manager) {
            // Tiap manager punya 2 project
            for ($p = 0; $p < 2; $p++) {
                $project = Project::factory()->create([
                    'name'       => $projectNames[$nameIndex++] ?? fake()->sentence(3, false),
                    'created_by' => $manager->id,
                ]);

                // Tambah manager sebagai member (role: manager)
                $project->members()->attach($manager->id, [
                    'role'           => 'manager',
                    'specialization' => null,
                ]);

                // Pilih 3-6 developer untuk project ini
                $count       = rand(3, 6);
                $projectDevs = $developers->shuffle()->take($count);

                foreach ($projectDevs as $dev) {
                    // Cari data spesialisasi default dev ini
                    $defaultSpec = collect($devData)->firstWhere('email', $dev->email)['spec'] ?? 'backend';

                    $project->members()->syncWithoutDetaching([
                        $dev->id => [
                            'role'           => 'developer',
                            'specialization' => $defaultSpec,
                        ],
                    ]);
                }

                // ─── TASKS ───────────────────────────────────────
                $taskCount = rand(5, 8);
                for ($t = 0; $t < $taskCount; $t++) {
                    $assignedDev = $projectDevs->random();
                    $devSpec     = collect($devData)->firstWhere('email', $assignedDev->email)['spec'] ?? 'backend';

                    // Ambil task title sesuai spesialisasi dev
                    $taskTitles = self::TASKS_BY_SPEC[$devSpec] ?? self::TASKS_BY_SPEC['backend'];
                    $title      = $taskTitles[array_rand($taskTitles)];

                    Task::factory()->create([
                        'project_id'  => $project->id,
                        'title'       => $title,
                        'assigned_to' => $assignedDev->id,
                        'created_by'  => $manager->id,
                    ]);
                }
            }
        }

        // ─── INFO RINGKASAN ──────────────────────────────────────
        $this->command->info('');
        $this->command->info('✓ Seeder selesai! Akun yang tersedia:');
        $this->command->info('');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['Admin',           'admin@admin.com', 'password'],
                ['Project Manager', 'budi@pm.com',     'password'],
                ['Project Manager', 'siti@pm.com',     'password'],
                ['Project Manager', 'ahmad@pm.com',    'password'],
                ['Developer (FE)',   'rizky@dev.com',   'password'],
                ['Developer (BE)',   'hendra@dev.com',  'password'],
                ['Developer (QA)',   'laras@dev.com',   'password'],
                ['Developer (DevOps)', 'dimas@dev.com', 'password'],
                ['Developer (Pentest)', 'citra@dev.com','password'],
                ['... dst', '(nama)@dev.com', 'password'],
            ]
        );
        $this->command->info('');
    }
}
