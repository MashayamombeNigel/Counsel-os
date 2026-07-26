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
                        'temperature' => 0.2, // low temperature for consistent structured extraction
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
