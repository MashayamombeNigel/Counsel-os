<?php

namespace App\Services;

use App\Models\Document;
use Illuminate\Support\Facades\Log;

class TextExtractionService
{
    /**
     * Extract readable text from the document file.
     */
    public function extractText(Document $document): bool
    {
        $document->update(['processing_status' => 'extracting']);

        try {
            // TODO: Implement smalot/pdfparser for PDFs
            // TODO: Implement phpoffice/phpword for DOCX
            $extractedText = "Sample extracted text...";

            $document->update([
                'extracted_text' => $extractedText,
                'processing_status' => 'analysis_pending',
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Text extraction failed: ' . $e->getMessage());
            $document->update([
                'processing_status' => 'failed',
                'error_message' => 'Text extraction failed.',
            ]);
            
            return false;
        }
    }
}
