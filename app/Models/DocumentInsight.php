<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentInsight extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'document_id' => 'integer',
            'key_parties_json' => 'array',
            'key_clauses_json' => 'array',
            'risks_json' => 'array',
            'obligations_json' => 'array',
            'deadlines_json' => 'array',
            'questions_json' => 'array',
        ];
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }
}
