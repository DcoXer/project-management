<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-6 months', 'now');
        $endDate = fake()->dateTimeBetween($startDate, '+6 months');

        return [
            'name' => fake()->sentence(3, false),
            'description' => fake()->paragraph(),
            'status' => fake()->randomElement(['planning', 'in_progress', 'on_hold', 'completed']),
            'priority' => fake()->randomElement(['low', 'medium', 'high']),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'created_by' => User::factory(),
        ];
    }

    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'in_progress',
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }
}
