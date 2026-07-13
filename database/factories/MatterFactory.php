<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Matter;
use Illuminate\Database\Eloquent\Factories\Factory;

class MatterFactory extends Factory
{
    protected $model = Matter::class;

    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'title' => fake()->sentence(4),
            'practice_area' => fake()->randomElement(['Commercial Real Estate', 'Employment', 'Contracts']),
            'status' => 'open',
            'opened_at' => now(),
        ];
    }
}
