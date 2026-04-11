<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'title' => fake()->sentence(4, false),
            'description' => fake()->optional(0.7)->paragraph(),
            'status' => fake()->randomElement(['todo', 'in_progress', 'review', 'done']),
            'priority' => fake()->randomElement(['low', 'medium', 'high']),
            'assigned_to' => User::factory(),
            'created_by' => User::factory(),
            'due_date' => fake()->optional(0.8)->dateTimeBetween('now', '+3 months'),
        ];
    }
}
