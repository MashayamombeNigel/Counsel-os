<?php

namespace App\Support\Json;

use RuntimeException;

class GeminiJsonParser
{
    /**
     * Keys the document analysis schema expects. Only 'summary' is
     * strictly required per the lenient validation decision - a
     * document with no risks or deadlines is a legitimate result
     * (e.g. a simple correspondence letter), not a parsing failure.
     * Missing optional keys default to an empty array/string rather
     * than causing the whole analysis to fail.
     */
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
     * Parses Gemini's raw text response into the expected structured
     * array. Handles the common failure mode where the model wraps
     * JSON in markdown fences (```json ... ```) despite being told
     * to return only JSON.
     *
     * Throws if the text isn't valid JSON at all, or if 'summary' is
     * missing/empty - those are the only cases treated as a real
     * parsing failure under the lenient policy.
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

        // Merge decoded values over defaults so any missing optional
        // key is filled in rather than causing a null-access error
        // later when the AI Insights view renders these fields.
        return array_merge(self::DEFAULTS, array_intersect_key($decoded, self::DEFAULTS));
    }

    protected static function stripMarkdownFences(string $text): string
    {
        $trimmed = trim($text);

        // Matches ```json ... ``` or plain ``` ... ``` wrapping.
        if (preg_match('/^```(?:json)?\s*(.*?)\s*```$/s', $trimmed, $matches)) {
            return $matches[1];
        }

        return $trimmed;
    }
}
