<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Document extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'matter_id',
        'uploaded_by',
        'filename',
        'original_name',
        'storage_path',
        'mime_type',
        'file_size',
        'document_type',
        'extracted_text',
        'processing_status',
        'error_message',
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
            'matter_id' => 'integer',
            'uploaded_by' => 'integer',
        ];
    }

    public function documentInsight(): HasOne
    {
        return $this->hasOne(DocumentInsight::class);
    }

    public function matter(): BelongsTo
    {
        return $this->belongsTo(Matter::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
