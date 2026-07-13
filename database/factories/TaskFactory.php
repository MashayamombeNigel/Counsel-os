<?php

namespace Database\Factories;

use App\Models\Matter;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'matter_id' => Matter::factory(),
            'title' => fake()->sentence(3),
            'due_date' => now()->addDays(fake()->numberBetween(1, 30)),
            'status' => 'open',
            'priority' => fake()->randomElement(['low', 'medium', 'high']),
            'created_by' => User::factory(),
        ];
    }
}
