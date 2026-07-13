<?php

use App\Models\Document;
use App\Models\DocumentInsight;
use App\Services\AiAnalysisService;
use Illuminate\Support\Facades\Http;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

function fakeGeminiResponse(string $text): void
{
    Http::fake([
        'generativelanguage.googleapis.com/*' => Http::response([
            'candidates' => [
                ['content' => ['parts' => [['text' => $text]]]],
            ],
        ], 200),
    ]);
}

test('analyzeDocument saves structured insights from a clean JSON response', function () {
    $document = Document::factory()->withExtractedText()->create();

    fakeGeminiResponse(json_encode([
        'summary' => 'A commercial lease with standard terms.',
        'key_parties' => ['Landlord Co', 'Tenant Co'],
        'risks' => [['title' => 'Late fee', 'severity' => 'high', 'reason' => 'Short grace period.']],
        'obligations' => [],
        'deadlines' => [],
        'key_clauses' => [],
        'questions_for_lawyer' => [],
    ]));

    app(AiAnalysisService::class)->analyzeDocument($document);

    $document->refresh();
    $insight = DocumentInsight::where('document_id', $document->id)->first();

    expect($document->processing_status)->toBe('analyzed')
        ->and($insight)->not->toBeNull()
        ->and($insight->summary)->toBe('A commercial lease with standard terms.')
        ->and($insight->risks_json[0]['severity'])->toBe('high');
});

test('analyzeDocument handles Gemini wrapping JSON in markdown fences', function () {
    $document = Document::factory()->withExtractedText()->create();

    $fencedJson = "```json\n" . json_encode(['summary' => 'Fenced but valid.']) . "\n```";
    fakeGeminiResponse($fencedJson);

    app(AiAnalysisService::class)->analyzeDocument($document);

    $insight = DocumentInsight::where('document_id', $document->id)->first();

    expect($insight->summary)->toBe('Fenced but valid.');
});

test('analyzeDocument throws and does not save when Gemini response has no summary', function () {
    $document = Document::factory()->withExtractedText()->create();

    fakeGeminiResponse(json_encode(['risks' => []]));

    app(AiAnalysisService::class)->analyzeDocument($document);
})->throws(RuntimeException::class);

test('analyzeDocument throws when the Gemini API call itself fails', function () {
    $document = Document::factory()->withExtractedText()->create();

    Http::fake([
        'generativelanguage.googleapis.com/*' => Http::response('Server error', 500),
    ]);

    app(AiAnalysisService::class)->analyzeDocument($document);
})->throws(RuntimeException::class);
