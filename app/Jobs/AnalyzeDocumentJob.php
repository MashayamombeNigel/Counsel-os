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

    /**
     * No automatic retries - same reasoning as ExtractDocumentTextJob.
     * A malformed Gemini response or API error won't fix itself on
     * retry, and silently retrying could rack up API costs for a
     * document that's fundamentally not going to parse.
     */
    public int $tries = 1;

    public function __construct(
        protected Document $document,
    ) {}

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

    public function failed(Throwable $e): void
    {
        $this->document->update([
            'processing_status' => 'failed',
            'error_message' => 'AI analysis job failed unexpectedly: ' . $e->getMessage(),
        ]);
    }
}
