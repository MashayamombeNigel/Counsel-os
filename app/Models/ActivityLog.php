<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'matter_id',
        'subject_type',
        'subject_id',
        'action',
        'description',
        'metadata_json',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'user_id' => 'integer',
            'matter_id' => 'integer',
            'subject_id' => 'integer',
            'metadata_json' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function matter(): BelongsTo
    {
        return $this->belongsTo(Matter::class);
    }
}
