<?php

namespace Database\Factories;

use App\Models\Document;
use App\Models\Matter;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentFactory extends Factory
{
    protected $model = Document::class;

    public function definition(): array
    {
        return [
            'matter_id' => Matter::factory(),
            'uploaded_by' => User::factory(),
            'filename' => fake()->uuid() . '.pdf',
            'original_name' => 'Sample_Lease.pdf',
            'storage_path' => 'matters/1/documents/' . fake()->uuid() . '.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 50000,
            'document_type' => 'lease',
            'processing_status' => 'uploaded',
        ];
    }

    public function withExtractedText(string $text = 'This is a sufficiently long sample extracted lease text for testing purposes.'): static
    {
        return $this->state([
            'extracted_text' => $text,
            'processing_status' => 'analysis_pending',
        ]);
    }

    public function analyzed(): static
    {
        return $this->state([
            'processing_status' => 'analyzed',
            'extracted_text' => 'This is a sufficiently long sample extracted lease text for testing purposes.',
        ]);
    }

    public function failed(string $errorMessage = 'Extraction failed.'): static
    {
        return $this->state([
            'processing_status' => 'failed',
            'error_message' => $errorMessage,
        ]);
    }
}
