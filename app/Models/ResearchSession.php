<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResearchSession extends Model
{
    protected $fillable = [
        'matter_id',
        'user_id',
        'query',
        'response',
        'sources_json',
        'model_name',
    ];

    protected $casts = [
        'sources_json' => 'array',
    ];

    public function matter(): BelongsTo
    {
        return $this->belongsTo(Matter::class);
    }
}
