<?php

namespace App\Support\Prompts;

class MatterResearchPrompt
{
    public static function system(): string
    {
        return <<<PROMPT
        You are CounselOS Research Assistant. Answer only using the matter context
        provided. If the context does not contain enough information, say that the
        uploaded matter documents do not provide enough evidence.
        PROMPT;
    }

    // $context is the pre-assembled string from ResearchService::buildMatterContext().
    // This class only formats the final prompt — it does not gather context itself.
    public static function user(string $question, string $context): string
    {
        return <<<PROMPT
        USER QUESTION:
        {$question}

        MATTER CONTEXT:
        {$context}

        RESPONSE FORMAT:
        - Short answer
        - Supporting points
        - Relevant source document names
        - Human review disclaimer
        PROMPT;
    }
}
