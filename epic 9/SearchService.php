<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Document;
use App\Models\Matter;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SearchService
{
    /**
     * Uses unlikely-to-collide placeholder tokens instead of raw HTML
     * tags in ts_headline's StartSel/StopSel. This lets us HTML-escape
     * the entire snippet first (neutralizing anything in the source
     * document text that happens to look like markup) and THEN swap
     * the safe placeholder tokens for real <mark> tags - escaping
     * after highlighting would double-escape our own tags; escaping
     * before is the only order that's actually safe.
     */
    protected const HIGHLIGHT_START = '@@HL_START@@';
    protected const HIGHLIGHT_END = '@@HL_END@@';

    public function search(string $term): array
    {
        return [
            'clients' => $this->searchClients($term),
            'matters' => $this->searchMatters($term),
            'documents' => $this->searchDocuments($term),
        ];
    }

    protected function searchClients(string $term): Collection
    {
        return Client::query()
            ->where('name', 'ilike', "%{$term}%")
            ->orWhere('organization', 'ilike', "%{$term}%")
            ->limit(10)
            ->get();
    }

    protected function searchMatters(string $term): Collection
    {
        return Matter::query()
            ->with('client')
            ->where('title', 'ilike', "%{$term}%")
            ->limit(10)
            ->get();
    }

    /**
     * Real full-text search against the generated tsvector column -
     * ranks by relevance (ts_rank) rather than just "contains the
     * substring", and returns a highlighted snippet of the matching
     * passage rather than the full document text.
     */
    protected function searchDocuments(string $term): Collection
    {
        $headlineOptions = sprintf(
            'StartSel=%s,StopSel=%s,MaxWords=40,MinWords=15,MaxFragments=2',
            self::HIGHLIGHT_START,
            self::HIGHLIGHT_END,
        );

        $results = Document::query()
            ->with('matter')
            ->whereNotNull('search_vector')
            ->whereRaw("search_vector @@ plainto_tsquery('english', ?)", [$term])
            ->select('documents.*')
            ->selectRaw(
                "ts_rank(search_vector, plainto_tsquery('english', ?)) as rank",
                [$term]
            )
            ->selectRaw(
                "ts_headline('english', coalesce(extracted_text, ''), plainto_tsquery('english', ?), ?) as raw_snippet",
                [$term, $headlineOptions]
            )
            ->orderByDesc('rank')
            ->limit(10)
            ->get();

        // Escape first, THEN inject real <mark> tags - see class docblock.
        return $results->map(function ($document) {
            $escaped = e($document->raw_snippet ?? '');
            $document->safe_snippet = str_replace(
                [self::HIGHLIGHT_START, self::HIGHLIGHT_END],
                ['<mark>', '</mark>'],
                $escaped
            );

            return $document;
        });
    }
}
