<?php

namespace Database\Factories;

use App\Models\Document;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentInsightFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'document_id' => Document::factory(),
            'summary' => fake()->text(),
            'key_parties_json' => '{}',
            'key_clauses_json' => '{}',
            'risks_json' => '{}',
            'obligations_json' => '{}',
            'deadlines_json' => '{}',
            'questions_json' => '{}',
            'model_name' => fake()->word(),
            'raw_ai_response' => fake()->text(),
        ];
    }
}
