<?php

namespace Database\Factories;

use App\Models\Document;
use App\Models\Matter;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'matter_id' => Matter::factory(),
            'source_document_id' => Document::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->text(),
            'due_date' => fake()->date(),
            'status' => fake()->randomElement(["open","in_progress","done"]),
            'priority' => fake()->randomElement(["low","medium","high"]),
            'created_by' => User::factory()->create()->created_by,
            'completed_at' => fake()->dateTime(),
        ];
    }
}
