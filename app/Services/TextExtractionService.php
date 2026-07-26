<?php

namespace App\Services;

use App\Models\Document;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\IOFactory as WordIOFactory;
use Smalot\PdfParser\Parser as PdfParser;

class TextExtractionService
{
    /**
     * Throws a distinct exception when extraction yields fewer than 20 characters —
     * scanned or DRM-protected PDFs often parse without error but return nothing usable.
     * OCR is out of scope, so this is treated as a hard failure, not a retry path.
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
