<?php

namespace App\Services;

use App\Models\Document;
use App\Models\DocumentInsight;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiAnalysisService
{
    /**
     * Analyze the document using Gemini API and save insights.
     */
    public function analyzeDocument(Document $document): ?DocumentInsight
    {
        $document->update(['processing_status' => 'analysis_pending']);

        try {
            // TODO: Construct prompt and call Gemini API via Http facade
            // For now, simulating the response retrieval
            $rawResponse = ''; 
            
            $parsedJson = $this->parseInsightJson($rawResponse);

            $insight = DocumentInsight::create([
                'document_id' => $document->id,
                'summary' => $parsedJson['summary'] ?? null,
                'key_parties_json' => $parsedJson['key_parties'] ?? [],
                'key_clauses_json' => $parsedJson['key_clauses'] ?? [],
                'risks_json' => $parsedJson['risks'] ?? [],
                'obligations_json' => $parsedJson['obligations'] ?? [],
                'deadlines_json' => $parsedJson['deadlines'] ?? [],
                'questions_json' => $parsedJson['questions_for_lawyer'] ?? [],
                'model_name' => 'gemini-2.5-flash',
                'raw_ai_response' => $rawResponse,
            ]);

            $document->update(['processing_status' => 'analyzed']);

            return $insight;
        } catch (\Exception $e) {
            Log::error('AI Analysis failed: ' . $e->getMessage());
            $document->update([
                'processing_status' => 'failed',
                'error_message' => 'Analysis failed. Please try again.',
            ]);
            
            return null;
        }
    }

    /**
     * Parse the raw AI response and fallback to regex extraction if 
     * the model hallucinated markdown fences or prose around the JSON.
     */
    public function parseInsightJson(string $rawResponse): array
    {
        // Try direct decode first
        $decoded = json_decode($rawResponse, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        // Fallback: Strip markdown fences or surrounding prose using regex
        // Looks for the first { and the last }
        if (preg_match('/\{.*\}/s', $rawResponse, $matches)) {
            $cleaned = $matches[0];
            $decoded = json_decode($cleaned, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }

        throw new \RuntimeException('Failed to parse valid JSON from AI response.');
    }
}
