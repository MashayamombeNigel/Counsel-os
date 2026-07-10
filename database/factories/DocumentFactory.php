<?php

namespace Database\Factories;

use App\Models\Matter;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'matter_id' => Matter::factory(),
            'uploaded_by' => User::factory()->create()->uploaded_by,
            'filename' => fake()->word(),
            'original_name' => fake()->word(),
            'storage_path' => fake()->word(),
            'mime_type' => fake()->word(),
            'file_size' => fake()->numberBetween(-10000, 10000),
            'document_type' => fake()->randomElement(["contract","lease","title_deed","correspondence","research","other"]),
            'extracted_text' => fake()->text(),
            'processing_status' => fake()->randomElement(["uploaded","extracting","analysis_pending","analyzed","failed"]),
            'error_message' => fake()->text(),
        ];
    }
}
