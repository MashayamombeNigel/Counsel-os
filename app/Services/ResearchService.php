<?php

namespace App\Services;

use App\Models\Matter;
use App\Models\ResearchSession;

class ResearchService
{
    /**
     * Build context, ask Gemini, and save the session.
     */
    public function answerQuestion(Matter $matter, string $question, int $userId): ResearchSession
    {
        // 1. Build Dumb Context (per spec constraints)
        // summary + latest document insights + first N chars of extracted text
        $context = $this->buildMatterContext($matter);

        // 2. Call Gemini
        // TODO: Http::post to Gemini API using $question and $context
        $response = "This is a simulated AI answer based on the matter context.";

        // 3. Save Session
        return ResearchSession::create([
            'matter_id' => $matter->id,
            'user_id' => $userId,
            'query' => $question,
            'response' => $response,
            'sources_json' => ['System Context'],
            'model_name' => 'gemini-2.5-flash',
        ]);
    }

    private function buildMatterContext(Matter $matter): string
    {
        // TODO: Assemble the dumb context
        return "Context assembly logic here...";
    }
}
