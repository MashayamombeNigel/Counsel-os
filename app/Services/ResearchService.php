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
     * Assembles context from the matter summary plus every analyzed
     * document's insight data - NOT raw extracted_text, which could
     * push the prompt far too large across several documents. Insights
     * are already the condensed representation; using them as context
     * is the "dumb but working" retrieval strategy the spec calls for
     * at MVP stage (Section 10: "Start MVP with simple context").
     *
     * Includes insights from ALL analyzed documents in the matter per
     * your decision - no per-document selection UI.
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
     * Runs synchronously per your decision - research answers are
     * meant to feel conversational, and adding a queue+refresh step
     * would undercut that. Gemini Flash's latency is acceptable for
     * a single blocking request here, unlike full document analysis
     * which can involve longer prompts and is less time-sensitive
     * for the user waiting on it.
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
