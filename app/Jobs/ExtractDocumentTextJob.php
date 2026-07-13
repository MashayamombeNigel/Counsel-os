<?php

namespace App\Jobs;

use App\Models\Document;
use App\Services\TextExtractionService;
use App\Services\TimelineService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class ExtractDocumentTextJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * No automatic retries - a malformed or scanned PDF won't parse
     * differently on a second attempt. Better to fail fast and let
     * the user see a clear error than retry silently in the background.
     */
    public int $tries = 1;

    public function __construct(
        protected Document $document,
    ) {}

    public function document(): Document
    {
        return $this->document;
    }

    public function handle(TextExtractionService $extraction, TimelineService $timeline): void
    {
        try {
            $text = $extraction->extract($this->document);

            $this->document->update([
                'extracted_text' => $text,
                'processing_status' => 'analysis_pending',
                'error_message' => null,
            ]);

            $timeline->recordDocumentEvent(
                document: $this->document,
                action: 'text_extracted',
                description: "Text extracted from \"{$this->document->original_name}\". Ready for AI analysis.",
            );
        } catch (Throwable $e) {
            $this->document->update([
                'processing_status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            $timeline->recordDocumentEvent(
                document: $this->document,
                action: 'extraction_failed',
                description: "Text extraction failed for \"{$this->document->original_name}\": {$e->getMessage()}",
            );
        }
    }

    /**
     * Safety net in case the job crashes hard (e.g. OOM) rather than
     * throwing a catchable exception inside handle(). Without this,
     * a hard crash would leave the document stuck on "extracting"
     * forever with no error shown to the user.
     */
    public function failed(Throwable $e): void
    {
        $this->document->update([
            'processing_status' => 'failed',
            'error_message' => 'Extraction job failed unexpectedly: ' . $e->getMessage(),
        ]);
    }
}
