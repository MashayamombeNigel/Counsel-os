<?php

use App\Models\Document;
use App\Models\Matter;
use App\Models\User;
use App\Services\ResearchService;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('buildMatterContext includes analyzed documents and excludes unanalyzed ones', function () {
    $matter = Matter::factory()->create(['title' => 'Riverside Lease Review']);

    $analyzedDoc = Document::factory()->analyzed()->create(['matter_id' => $matter->id, 'original_name' => 'Analyzed.pdf']);
    \App\Models\DocumentInsight::create([
        'document_id' => $analyzedDoc->id,
        'summary' => 'This document has a late payment risk.',
        'key_parties_json' => [], 'key_clauses_json' => [], 'risks_json' => [],
        'obligations_json' => [], 'deadlines_json' => [], 'questions_json' => [],
    ]);

    Document::factory()->create(['matter_id' => $matter->id, 'original_name' => 'NotAnalyzed.pdf', 'processing_status' => 'uploaded']);

    $context = app(ResearchService::class)->buildMatterContext($matter->fresh());

    expect($context)->toContain('Riverside Lease Review')
        ->toContain('Analyzed.pdf')
        ->toContain('late payment risk')
        ->not->toContain('NotAnalyzed.pdf');
});

test('buildMatterContext notes when no documents are analyzed yet', function () {
    $matter = Matter::factory()->create();
    Document::factory()->create(['matter_id' => $matter->id, 'processing_status' => 'uploaded']);

    $context = app(ResearchService::class)->buildMatterContext($matter->fresh());

    expect($context)->toContain('No analyzed documents are available yet');
});

test('answerQuestion saves a research session with the faked Gemini response', function () {
    Http::fake([
        'generativelanguage.googleapis.com/*' => Http::response([
            'candidates' => [
                ['content' => ['parts' => [['text' => 'Short answer: yes, grounded in the lease terms.']]]],
            ],
        ], 200),
    ]);

    $user = User::factory()->create();
    $matter = Matter::factory()->create();

    $session = app(ResearchService::class)->answerQuestion($matter, 'What liabilities apply?', $user->id);

    expect($session->query)->toBe('What liabilities apply?')
        ->and($session->response)->toContain('Short answer');

    $this->assertDatabaseHas('research_sessions', [
        'matter_id' => $matter->id,
        'user_id' => $user->id,
    ]);
});

test('a Gemini failure during research surfaces as a flash error, not a crash', function () {
    Http::fake([
        'generativelanguage.googleapis.com/*' => Http::response('Server error', 500),
    ]);

    $user = User::factory()->create();
    $matter = Matter::factory()->create();

    $response = $this->actingAs($user)->post(route('matters.research.store', $matter), [
        'query' => 'What liabilities apply?',
    ]);

    $response->assertSessionHas('error');
    $this->assertDatabaseCount('research_sessions', 0);
});
