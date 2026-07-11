<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentInsight extends Model
{
    protected $fillable = [
        'document_id',
        'summary',
        'key_parties_json',
        'key_clauses_json',
        'risks_json',
        'obligations_json',
        'deadlines_json',
        'questions_json',
        'model_name',
        'raw_ai_response',
    ];

    /**
     * Without these casts, passing PHP arrays (from GeminiJsonParser)
     * straight into json columns via updateOrCreate() would fail or
     * silently store malformed data. These casts handle the array
     * <-> JSON conversion transparently in both directions.
     */
    protected $casts = [
        'key_parties_json' => 'array',
        'key_clauses_json' => 'array',
        'risks_json' => 'array',
        'obligations_json' => 'array',
        'deadlines_json' => 'array',
        'questions_json' => 'array',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }
}
