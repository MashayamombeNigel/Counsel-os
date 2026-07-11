<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class GeminiClient
{
    protected string $apiKey;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
        $this->model = config('services.gemini.model', 'gemini-2.5-flash');
    }

    /**
     * Sends a system + user prompt pair to Gemini and returns the raw
     * text response. Deliberately does not know anything about JSON
     * parsing or document logic - that belongs in AiAnalysisService
     * and GeminiJsonParser. This class only knows how to talk to the API.
     */
    public function generateText(string $systemPrompt, string $userPrompt): string
    {
        $response = Http::timeout(60)
            ->post(
                "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}",
                [
                    'system_instruction' => [
                        'parts' => [['text' => $systemPrompt]],
                    ],
                    'contents' => [
                        ['parts' => [['text' => $userPrompt]]],
                    ],
                    'generationConfig' => [
                        // Lower temperature for structured extraction -
                        // we want consistent JSON shape, not creative variance.
                        'temperature' => 0.2,
                    ],
                ]
            );

        if ($response->failed()) {
            throw new RuntimeException(
                "Gemini API request failed with status {$response->status()}: {$response->body()}"
            );
        }

        $text = data_get($response->json(), 'candidates.0.content.parts.0.text');

        if (blank($text)) {
            throw new RuntimeException('Gemini returned an empty response with no candidate text.');
        }

        return $text;
    }
}
