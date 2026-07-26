<?php

namespace App\Services;

use App\Models\Matter;
use App\Models\ResearchSession;
use App\Support\Prompts\MatterResearchPrompt;

class ResearchService
{
    public function __construct(
        protected GeminiClient $gemini,
    ) {}

    /**
     * Builds context from analyzed document insights rather than raw extracted_text —
     * insights are already condensed, preventing oversized prompts across multi-document matters.
     */
    public function buildMatterContext(Matter $matter): string
    {
        $matter->loadMissing('documents.documentInsight');

        $lines = [
            "Matter: {$matter->title}",
            "Practice area: " . ($matter->practice_area ?? 'not specified'),
        ];

        if ($matter->description) {
            $lines[] = "Description: {$matter->description}";
        }

        $analyzedDocuments = $matter->documents->filter(
            fn ($document) => $document->processing_status === 'analyzed' && $document->documentInsight
        );

        if ($analyzedDocuments->isEmpty()) {
            $lines[] = "No analyzed documents are available yet for this matter.";
        }

        foreach ($analyzedDocuments as $document) {
            $insight = $document->documentInsight;

            $lines[] = "\n--- Document: {$document->original_name} ---";
            $lines[] = "Summary: {$insight->summary}";

            if (! empty($insight->risks_json)) {
                $riskLines = collect($insight->risks_json)
                    ->map(fn ($r) => "{$r['title']} ({$r['severity']}): {$r['reason']}")
                    ->implode('; ');
                $lines[] = "Risks: {$riskLines}";
            }

            if (! empty($insight->obligations_json)) {
                $obligationLines = collect($insight->obligations_json)
                    ->map(fn ($o) => "{$o['party']}: {$o['obligation']}")
                    ->implode('; ');
                $lines[] = "Obligations: {$obligationLines}";
            }

            if (! empty($insight->deadlines_json)) {
                $deadlineLines = collect($insight->deadlines_json)
                    ->map(fn ($d) => "{$d['title']} ({$d['date']})")
                    ->implode('; ');
                $lines[] = "Deadlines: {$deadlineLines}";
            }
        }

        return implode("\n", $lines);
    }

    /**
     * Runs synchronously — research answers are conversational and a queue+poll loop
     * would undercut the UX. Gemini Flash latency is acceptable for a single blocking
     * request here, unlike document analysis which involves longer prompts.
     */
    public function answerQuestion(Matter $matter, string $question, int $userId): ResearchSession
    {
        $context = $this->buildMatterContext($matter);

        $response = $this->gemini->generateText(
            MatterResearchPrompt::system(),
            MatterResearchPrompt::user($question, $context),
        );

        return ResearchSession::create([
            'matter_id' => $matter->id,
            'user_id' => $userId,
            'query' => $question,
            'response' => $response,
            'sources_json' => $matter->documents
                ->where('processing_status', 'analyzed')
                ->pluck('original_name')
                ->values()
                ->all(),
            'model_name' => config('services.gemini.model'),
        ]);
    }
}
