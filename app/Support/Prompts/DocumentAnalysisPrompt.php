<?php

namespace App\Support\Prompts;

class DocumentAnalysisPrompt
{
    /**
     * System prompt - sets the assistant's role and boundaries.
     * Matches spec Section 10's exact prompt contract: legal review
     * assistant, not a source of final legal advice, extraction-only.
     */
    public static function system(): string
    {
        return <<<PROMPT
        You are a legal document review assistant. You help qualified legal professionals
        review documents. You do not provide final legal advice. Extract structured
        information only from the provided document text.
        PROMPT;
    }

    /**
     * User prompt - the extraction instruction plus the document text.
     * Truncates extremely long documents to keep the request within a
     * reasonable token budget; the spec's non-functional requirements
     * don't mandate full-document analysis for arbitrarily large files,
     * and Gemini Flash's context window isn't the bottleneck we need to
     * plan around at MVP scale - request size and latency are.
     */
    public static function user(string $extractedText): string
    {
        $truncated = mb_substr($extractedText, 0, 30000);

        return <<<PROMPT
        Analyze this legal document for a matter workspace.
        Return ONLY valid JSON with the following keys:
        {
         "summary": "string",
         "key_parties": ["string"],
         "key_clauses": [{"title":"string", "description":"string"}],
         "risks": [{"title":"string", "severity":"low|medium|high", "reason":"string"}],
         "obligations": [{"party":"string", "obligation":"string", "source_hint":"string"}],
         "deadlines": [{"title":"string", "date":"YYYY-MM-DD or unknown", "reason":"string"}],
         "questions_for_lawyer": ["string"]
        }
        DOCUMENT_TEXT:
        {$truncated}
        PROMPT;
    }
}
