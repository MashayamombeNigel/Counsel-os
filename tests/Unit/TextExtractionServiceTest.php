<?php

use App\Models\Document;
use App\Services\TextExtractionService;

/**
 * Overrides the real parsing methods so tests exercise
 * TextExtractionService::extract()'s own logic (mime-type dispatch,
 * the near-empty-result failure threshold) without needing real PDF
 * or DOCX binaries on disk. The real extractFromPdf/extractFromDocx
 * implementations wrap smalot/pdfparser and phpoffice/phpword - both
 * well-tested third-party libraries whose internal correctness isn't
 * something this test suite needs to re-verify.
 */
class TestableTextExtractionService extends TextExtractionService
{
    public static string $canned = 'A sufficiently long piece of extracted sample text for testing.';

    protected function extractFromPdf(string $path): string
    {
        return static::$canned;
    }

    protected function extractFromDocx(string $path): string
    {
        return static::$canned;
    }
}

beforeEach(function () {
    TestableTextExtractionService::$canned = 'A sufficiently long piece of extracted sample text for testing.';
});

test('extracts text successfully for a pdf document', function () {
    $document = Document::factory()->make(['mime_type' => 'application/pdf']);

    $service = new TestableTextExtractionService();
    $text = $service->extract($document);

    expect($text)->toBe('A sufficiently long piece of extracted sample text for testing.');
});

test('extracts text successfully for a docx document', function () {
    $document = Document::factory()->make([
        'mime_type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    ]);

    $service = new TestableTextExtractionService();
    $text = $service->extract($document);

    expect($text)->toBe('A sufficiently long piece of extracted sample text for testing.');
});

test('throws for an unsupported mime type', function () {
    $document = Document::factory()->make(['mime_type' => 'image/png']);

    (new TestableTextExtractionService())->extract($document);
})->throws(RuntimeException::class, 'Unsupported mime type');

test('throws when extraction yields near-empty text - scanned/image PDF case', function () {
    TestableTextExtractionService::$canned = 'short';

    $document = Document::factory()->make(['mime_type' => 'application/pdf']);

    (new TestableTextExtractionService())->extract($document);
})->throws(RuntimeException::class, 'little or no readable text');

test('trims whitespace-only output and still treats it as a failure', function () {
    TestableTextExtractionService::$canned = "   \n\n   ";

    $document = Document::factory()->make(['mime_type' => 'application/pdf']);

    (new TestableTextExtractionService())->extract($document);
})->throws(RuntimeException::class);
