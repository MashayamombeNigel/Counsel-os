<?php

namespace Database\Factories;

use App\Models\Matter;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ResearchSessionFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'matter_id' => Matter::factory(),
            'user_id' => User::factory(),
            'query' => fake()->text(),
            'response' => fake()->text(),
            'sources_json' => '{}',
            'model_name' => fake()->word(),
        ];
    }
}
