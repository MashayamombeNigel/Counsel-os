<?php

use App\Models\Document;
use App\Services\TextExtractionService;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory as WordIOFactory;

test('real TextExtractionService correctly extracts text from a genuine docx file', function () {
    Storage::fake('local');

    // Build a real, valid .docx using phpword's own writer - this is
    // the same library TextExtractionService uses to read it, so this
    // test genuinely exercises our extraction code against a real
    // file format rather than a canned string.
    $phpWord = new PhpWord();
    $section = $phpWord->addSection();
    $section->addText('This lease requires the tenant to maintain liability insurance.');
    $section->addText('Rent is due on the first of each month, with a five day grace period.');

    $tempPath = tempnam(sys_get_temp_dir(), 'docx_test_') . '.docx';
    WordIOFactory::createWriter($phpWord, 'Word2007')->save($tempPath);

    $storagePath = 'matters/1/documents/test-fixture.docx';
    Storage::disk('local')->put($storagePath, file_get_contents($tempPath));
    unlink($tempPath);

    $document = Document::factory()->make([
        'mime_type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'storage_path' => $storagePath,
    ]);

    $text = (new TextExtractionService())->extract($document);

    expect($text)->toContain('liability insurance')
        ->and($text)->toContain('grace period');
});
