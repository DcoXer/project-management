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

    public function run(): void
    {
        // Admin
        $admin = User::factory()->admin()->create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
        ]);

        // Project Managers
        $managers = User::factory()->projectManager()->count(3)->create();

        // Developers
        $developers = User::factory()->count(8)->create();

        $allUsers = collect([$admin])->merge($managers)->merge($developers);

        // Projects
        $projects = collect();

        foreach ($managers as $manager) {
            $managerProjects = Project::factory()->count(2)->create([
                'created_by' => $manager->id,
            ]);

            foreach ($managerProjects as $project) {
                // Tambah manager sebagai member
                $project->members()->attach($manager->id, ['role' => 'manager']);

                // Tambah 3-5 developer ke project
                $projectDevs = $developers->random(rand(3, 5));
                foreach ($projectDevs as $dev) {
                    $project->members()->syncWithoutDetaching([
                        $dev->id => ['role' => 'developer'],
                    ]);
                }

                // Buat 4-8 tasks per project
                $taskCount = rand(4, 8);
                for ($i = 0; $i < $taskCount; $i++) {
                    $assignedDev = $projectDevs->random();
                    Task::factory()->create([
                        'project_id' => $project->id,
                        'assigned_to' => $assignedDev->id,
                        'created_by' => $manager->id,
                    ]);
                }
            }

            $projects = $projects->merge($managerProjects);
        }
    }
}
