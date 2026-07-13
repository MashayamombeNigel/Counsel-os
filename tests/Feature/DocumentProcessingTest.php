<?php

use App\Models\Document;
use App\Models\User;
use App\Jobs\ExtractDocumentTextJob;
use App\Jobs\AnalyzeDocumentJob;
use Illuminate\Support\Facades\Queue;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('extract dispatches the extraction job and flips status to extracting', function () {
    Queue::fake();

    $user = User::factory()->create();
    $document = Document::factory()->create(['processing_status' => 'uploaded']);

    $this->actingAs($user)->post(route('documents.extract', $document));

    $document->refresh();

    expect($document->processing_status)->toBe('extracting');
    Queue::assertPushed(ExtractDocumentTextJob::class, fn ($job) => $job->document()->is($document));
});

test('extract can retry from a failed status', function () {
    Queue::fake();

    $user = User::factory()->create();
    $document = Document::factory()->failed()->create();

    $this->actingAs($user)->post(route('documents.extract', $document));

    $document->refresh();

    expect($document->processing_status)->toBe('extracting')
        ->and($document->error_message)->toBeNull();
    Queue::assertPushed(ExtractDocumentTextJob::class);
});

test('extract refuses to run on a document already analyzed', function () {
    Queue::fake();

    $user = User::factory()->create();
    $document = Document::factory()->analyzed()->create();

    $response = $this->actingAs($user)->post(route('documents.extract', $document));

    $response->assertSessionHas('error');
    Queue::assertNotPushed(ExtractDocumentTextJob::class);
});

test('analyze dispatches the analysis job when text has been extracted', function () {
    Queue::fake();

    $user = User::factory()->create();
    $document = Document::factory()->withExtractedText()->create();

    $this->actingAs($user)->post(route('documents.analyze', $document));

    Queue::assertPushed(AnalyzeDocumentJob::class, fn ($job) => $job->document()->is($document));
});

test('analyze refuses to run without extracted text', function () {
    Queue::fake();

    $user = User::factory()->create();
    $document = Document::factory()->create(['processing_status' => 'uploaded', 'extracted_text' => null]);

    $response = $this->actingAs($user)->post(route('documents.analyze', $document));

    $response->assertSessionHas('error');
    Queue::assertNotPushed(AnalyzeDocumentJob::class);
});

test('analyze can retry from a failed status when extracted text exists', function () {
    Queue::fake();

    $user = User::factory()->create();
    $document = Document::factory()->failed()->create([
        'extracted_text' => 'Some previously extracted text.',
    ]);

    $this->actingAs($user)->post(route('documents.analyze', $document));

    Queue::assertPushed(AnalyzeDocumentJob::class);
});
