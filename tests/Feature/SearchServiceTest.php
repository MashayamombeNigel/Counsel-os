<?php

use App\Models\Document;
use App\Models\Matter;
use App\Services\SearchService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('search finds a document by text that only exists in extracted_text, not the filename', function () {
    Document::factory()->create([
        'original_name'     => 'Unrelated_Filename.pdf',
        'extracted_text'    => 'This lease contains a specific indemnification clause about liability.',
        'processing_status' => 'analyzed',
    ]);

    $results = app(SearchService::class)->search('indemnification');

    expect($results['documents'])->toHaveCount(1);
});

test('search does not match unrelated terms', function () {
    Document::factory()->create([
        'extracted_text' => 'This lease contains a specific indemnification clause about liability.',
    ]);

    $results = app(SearchService::class)->search('zebra');

    expect($results['documents'])->toHaveCount(0);
});

test('search snippet highlights the matched term and escapes any raw html in source text', function () {
    Document::factory()->create([
        'extracted_text' => 'The tenant must maintain <script>alert(1)</script> liability insurance for the term.',
    ]);

    $results = app(SearchService::class)->search('liability');

    $snippet = $results['documents']->first()->safe_snippet;

    expect($snippet)
        ->toContain('<mark>')
        ->and($snippet)->not->toContain('<script>alert(1)</script>');
});

test('search also matches matter titles', function () {
    Matter::factory()->create(['title' => 'Riverside Lease Review']);

    $results = app(SearchService::class)->search('Riverside');

    expect($results['matters'])->toHaveCount(1);
});
