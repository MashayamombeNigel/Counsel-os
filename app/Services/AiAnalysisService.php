<?php

namespace App\Services;

use App\Models\Document;
use App\Models\DocumentInsight;
use App\Support\Prompts\DocumentAnalysisPrompt;
use App\Support\Json\GeminiJsonParser;

class AiAnalysisService
{
    public function __construct(
        protected GeminiClient $gemini,
    ) {}

    /**
     * Builds the prompt, calls Gemini, parses the response, and saves
     * structured insights. Raw response is kept on DocumentInsight for
     * debugging/portfolio transparency (spec Section 10), but only
     * ever shown to end users behind an explicit "view raw" action,
     * never by default (spec Section 17 AI safety baseline).
     *
     * Throws on any failure - the caller (AnalyzeDocumentJob) is
     * responsible for catching this and setting processing_status
     * to failed. This method's only job is: analyze or throw.
     */
    public function analyzeDocument(Document $document): DocumentInsight
    {
        $systemPrompt = DocumentAnalysisPrompt::system();
        $userPrompt = DocumentAnalysisPrompt::user($document->extracted_text);

        $rawResponse = $this->gemini->generateText($systemPrompt, $userPrompt);

        $parsed = GeminiJsonParser::parse($rawResponse);

        $insight = DocumentInsight::updateOrCreate(
            ['document_id' => $document->id],
            [
                'summary' => $parsed['summary'],
                'key_parties_json' => $parsed['key_parties'],
                'key_clauses_json' => $parsed['key_clauses'],
                'risks_json' => $parsed['risks'],
                'obligations_json' => $parsed['obligations'],
                'deadlines_json' => $parsed['deadlines'],
                'questions_json' => $parsed['questions_for_lawyer'],
                'model_name' => config('services.gemini.model'),
                'raw_ai_response' => $rawResponse,
            ]
        );

        $document->update(['processing_status' => 'analyzed']);

        return $insight;
    }
}
