<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Document;
use App\Models\Matter;
use App\Models\UploadedBy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\DocumentController
 */
final class DocumentControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $documents = Document::factory()->count(3)->create();

        $response = $this->get(route('documents.index'));

        $response->assertOk();
        $response->assertViewIs('document.index');
        $response->assertViewHas('documents', $documents);
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('documents.create'));

        $response->assertOk();
        $response->assertViewIs('document.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\DocumentController::class,
            'store',
            \App\Http\Requests\DocumentStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $matter = Matter::factory()->create();
        $uploaded_by = UploadedBy::factory()->create();
        $filename = fake()->word();
        $original_name = fake()->word();
        $storage_path = fake()->word();
        $mime_type = fake()->word();
        $file_size = fake()->numberBetween(-10000, 10000);
        $document_type = fake()->randomElement(/** enum_attributes **/);
        $processing_status = fake()->randomElement(/** enum_attributes **/);

        $response = $this->post(route('documents.store'), [
            'matter_id' => $matter->id,
            'uploaded_by' => $uploaded_by->id,
            'filename' => $filename,
            'original_name' => $original_name,
            'storage_path' => $storage_path,
            'mime_type' => $mime_type,
            'file_size' => $file_size,
            'document_type' => $document_type,
            'processing_status' => $processing_status,
        ]);

        $documents = Document::query()
            ->where('matter_id', $matter->id)
            ->where('uploaded_by', $uploaded_by->id)
            ->where('filename', $filename)
            ->where('original_name', $original_name)
            ->where('storage_path', $storage_path)
            ->where('mime_type', $mime_type)
            ->where('file_size', $file_size)
            ->where('document_type', $document_type)
            ->where('processing_status', $processing_status)
            ->get();
        $this->assertCount(1, $documents);
        $document = $documents->first();

        $response->assertRedirect(route('documents.index'));
        $response->assertSessionHas('document.id', $document->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $document = Document::factory()->create();

        $response = $this->get(route('documents.show', $document));

        $response->assertOk();
        $response->assertViewIs('document.show');
        $response->assertViewHas('document', $document);
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $document = Document::factory()->create();

        $response = $this->get(route('documents.edit', $document));

        $response->assertOk();
        $response->assertViewIs('document.edit');
        $response->assertViewHas('document', $document);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\DocumentController::class,
            'update',
            \App\Http\Requests\DocumentUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $document = Document::factory()->create();
        $matter = Matter::factory()->create();
        $uploaded_by = UploadedBy::factory()->create();
        $filename = fake()->word();
        $original_name = fake()->word();
        $storage_path = fake()->word();
        $mime_type = fake()->word();
        $file_size = fake()->numberBetween(-10000, 10000);
        $document_type = fake()->randomElement(/** enum_attributes **/);
        $processing_status = fake()->randomElement(/** enum_attributes **/);

        $response = $this->put(route('documents.update', $document), [
            'matter_id' => $matter->id,
            'uploaded_by' => $uploaded_by->id,
            'filename' => $filename,
            'original_name' => $original_name,
            'storage_path' => $storage_path,
            'mime_type' => $mime_type,
            'file_size' => $file_size,
            'document_type' => $document_type,
            'processing_status' => $processing_status,
        ]);

        $document->refresh();

        $response->assertRedirect(route('documents.index'));
        $response->assertSessionHas('document.id', $document->id);

        $this->assertEquals($matter->id, $document->matter_id);
        $this->assertEquals($uploaded_by->id, $document->uploaded_by);
        $this->assertEquals($filename, $document->filename);
        $this->assertEquals($original_name, $document->original_name);
        $this->assertEquals($storage_path, $document->storage_path);
        $this->assertEquals($mime_type, $document->mime_type);
        $this->assertEquals($file_size, $document->file_size);
        $this->assertEquals($document_type, $document->document_type);
        $this->assertEquals($processing_status, $document->processing_status);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $document = Document::factory()->create();

        $response = $this->delete(route('documents.destroy', $document));

        $response->assertRedirect(route('documents.index'));

        $this->assertModelMissing($document);
    }
}
