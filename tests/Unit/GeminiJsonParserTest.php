<?php

use App\Support\Json\GeminiJsonParser;

test('parses clean JSON with all keys present', function () {
    $raw = json_encode([
        'summary' => 'A test lease summary.',
        'key_parties' => ['Landlord', 'Tenant'],
        'key_clauses' => [['title' => 'Rent', 'description' => 'Monthly rent clause.']],
        'risks' => [['title' => 'Late fee', 'severity' => 'medium', 'reason' => 'Tight grace period.']],
        'obligations' => [['party' => 'Tenant', 'obligation' => 'Pay rent.', 'source_hint' => 'Section 4']],
        'deadlines' => [['title' => 'Renewal notice', 'date' => '2026-12-01', 'reason' => 'Renewal window.']],
        'questions_for_lawyer' => ['Is this enforceable?'],
    ]);

    $parsed = GeminiJsonParser::parse($raw);

    expect($parsed['summary'])->toBe('A test lease summary.')
        ->and($parsed['key_parties'])->toHaveCount(2)
        ->and($parsed['risks'][0]['severity'])->toBe('medium');
});

test('strips markdown json fences before parsing', function () {
    $raw = "```json\n" . json_encode(['summary' => 'Fenced response.']) . "\n```";

    $parsed = GeminiJsonParser::parse($raw);

    expect($parsed['summary'])->toBe('Fenced response.');
});

test('strips plain markdown fences without the json language tag', function () {
    $raw = "```\n" . json_encode(['summary' => 'Plain fenced response.']) . "\n```";

    $parsed = GeminiJsonParser::parse($raw);

    expect($parsed['summary'])->toBe('Plain fenced response.');
});

test('fills missing optional keys with lenient defaults', function () {
    // Only summary present - a simple correspondence letter with no
    // risks/deadlines is a legitimate result, not a parsing failure.
    $raw = json_encode(['summary' => 'A short letter with nothing else notable.']);

    $parsed = GeminiJsonParser::parse($raw);

    expect($parsed['risks'])->toBe([])
        ->and($parsed['deadlines'])->toBe([])
        ->and($parsed['key_parties'])->toBe([])
        ->and($parsed['questions_for_lawyer'])->toBe([]);
});

test('throws when summary is missing entirely', function () {
    $raw = json_encode(['risks' => [['title' => 'Some risk']]]);

    GeminiJsonParser::parse($raw);
})->throws(RuntimeException::class);

test('throws when summary is present but empty', function () {
    $raw = json_encode(['summary' => '']);

    GeminiJsonParser::parse($raw);
})->throws(RuntimeException::class);

test('throws when the response is not valid JSON at all', function () {
    GeminiJsonParser::parse('This is just plain prose, not JSON.');
})->throws(RuntimeException::class);

test('throws when fenced content is not valid JSON', function () {
    GeminiJsonParser::parse("```json\nnot actually json\n```");
})->throws(RuntimeException::class);
