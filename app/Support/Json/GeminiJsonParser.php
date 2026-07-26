<?php

namespace App\Support\Json;

use RuntimeException;

class GeminiJsonParser
{
    // Only 'summary' is required — a document with no risks or deadlines is a
    // legitimate result (e.g. a simple letter). Missing optional keys default to
    // empty arrays rather than failing the whole analysis.
    protected const DEFAULTS = [
        'summary' => '',
        'key_parties' => [],
        'key_clauses' => [],
        'risks' => [],
        'obligations' => [],
        'deadlines' => [],
        'questions_for_lawyer' => [],
    ];

    /**
     * Strips markdown fences before decoding — Gemini wraps JSON in ```json ... ```
     * blocks despite being instructed to return plain JSON.
     * Throws if the result is not valid JSON or if 'summary' is missing.
     */
    public static function parse(string $rawResponse): array
    {
        $cleaned = self::stripMarkdownFences($rawResponse);

        $decoded = json_decode($cleaned, true);

        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($decoded)) {
            throw new RuntimeException(
                'Gemini response was not valid JSON after fence-stripping: ' . json_last_error_msg()
            );
        }

        if (empty($decoded['summary'])) {
            throw new RuntimeException('Gemini response is missing a summary - treating as a failed analysis.');
        }

        return array_merge(self::DEFAULTS, array_intersect_key($decoded, self::DEFAULTS));
    }

    protected static function stripMarkdownFences(string $text): string
    {
        $trimmed = trim($text);

        if (preg_match('/^```(?:json)?\s*(.*?)\s*```$/s', $trimmed, $matches)) {
            return $matches[1];
        }

        return $trimmed;
    }
}
