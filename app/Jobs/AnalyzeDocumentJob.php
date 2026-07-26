<?php

namespace App\Jobs;

use App\Models\Document;
use App\Services\AiAnalysisService;
use App\Services\TimelineService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class AnalyzeDocumentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // No retries — a bad Gemini response or API error won't resolve itself,
    // and silent retries would rack up unnecessary API costs.
    public int $tries = 1;

    public function __construct(
        protected Document $document,
    ) {}

    public function document(): Document
    {
        return $this->document;
    }

    public function handle(AiAnalysisService $analysis, TimelineService $timeline): void
    {
        try {
            $analysis->analyzeDocument($this->document);

            $timeline->recordDocumentEvent(
                document: $this->document,
                action: 'ai_analysis_completed',
                description: "AI analysis completed for \"{$this->document->original_name}\".",
                userId: $this->document->uploaded_by,
            );
        } catch (Throwable $e) {
            $this->document->update([
                'processing_status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            $timeline->recordDocumentEvent(
                document: $this->document,
                action: 'ai_analysis_failed',
                description: "AI analysis failed for \"{$this->document->original_name}\": {$e->getMessage()}",
                userId: $this->document->uploaded_by,
            );
        }
    }

    /**
     * Catches hard crashes (e.g. OOM) that bypass the try/catch in handle(),
     * preventing the document from being stuck on 'analyzing' indefinitely.
     */
    public function failed(Throwable $e): void
    {
        $this->document->update([
            'processing_status' => 'failed',
            'error_message' => 'AI analysis job failed unexpectedly: ' . $e->getMessage(),
        ]);
    }
}
