<?php

namespace App\Services;

use App\Models\Document;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\IOFactory as WordIOFactory;
use Smalot\PdfParser\Parser as PdfParser;

class TextExtractionService
{
    /**
     * Extracts text based on the document's mime type. Throws on
     * failure (caught by the caller) and throws a distinct exception
     * when extraction "succeeds" but yields no usable text - a scanned
     * or DRM'd PDF often parses without error but returns nothing
     * useful, and that should be treated as a failure, not a silent
     * pass-through to analysis_pending. OCR is explicitly out of
     * scope per the spec, so this is a hard stop, not a retry path.
     */
    public function extract(Document $document): string
    {
        $absolutePath = Storage::disk('local')->path($document->storage_path);

        $text = match ($document->mime_type) {
            'application/pdf' => $this->extractFromPdf($absolutePath),
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => $this->extractFromDocx($absolutePath),
            default => throw new \RuntimeException("Unsupported mime type for extraction: {$document->mime_type}"),
        };

        $trimmed = trim($text);

        if (mb_strlen($trimmed) < 20) {
            throw new \RuntimeException(
                'Extraction produced little or no readable text. This document may be scanned, ' .
                'image-based, or encrypted - OCR is not supported in this MVP.'
            );
        }

        return $trimmed;
    }

    protected function extractFromPdf(string $path): string
    {
        $parser = new PdfParser();
        $pdf = $parser->parseFile($path);

        return $pdf->getText();
    }

    protected function extractFromDocx(string $path): string
    {
        $phpWord = WordIOFactory::load($path, 'Word2007');
        $text = '';

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if (method_exists($element, 'getText')) {
                    $elementText = $element->getText();
                    $text .= (is_string($elementText) ? $elementText : '') . "\n";
                }
            }
        }

        return $text;
    }
}
