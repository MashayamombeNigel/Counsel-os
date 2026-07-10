<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class MatterFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->text(),
            'practice_area' => fake()->word(),
            'status' => fake()->randomElement(["open","in_review","waiting_client","closed"]),
            'opened_at' => fake()->dateTime(),
            'closed_at' => fake()->dateTime(),
        ];
    }
}
