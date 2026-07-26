<?php

namespace App\Support\Prompts;

class DocumentAnalysisPrompt
{
    public static function system(): string
    {
        return <<<PROMPT
        You are a legal document review assistant. You help qualified legal professionals
        review documents. You do not provide final legal advice. Extract structured
        information only from the provided document text.
        PROMPT;
    }

    /**
     * Documents are truncated to 30,000 characters to keep requests within a
     * reasonable token budget. Latency and request size are the binding constraint
     * at this scale, not Gemini's context window.
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
