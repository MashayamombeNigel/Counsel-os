<?php

namespace Database\Factories;

use App\Models\Matter;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityLogFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'matter_id' => Matter::factory(),
            'subject_type' => fake()->word(),
            'subject_id' => fake()->randomNumber(),
            'action' => fake()->word(),
            'description' => fake()->text(),
            'metadata_json' => '{}',
        ];
    }
}
