<?php

namespace App\Services;

use App\Models\Document;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class DocumentService
{
    /**
     * Store an uploaded document and create the database record.
     */
    public function storeUpload(UploadedFile $file, array $data): Document
    {
        $path = $file->store('documents', 'local');

        return Document::create([
            'matter_id' => $data['matter_id'],
            'uploaded_by' => $data['uploaded_by'],
            'filename' => basename($path),
            'original_name' => $file->getClientOriginalName(),
            'storage_path' => $path,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'document_type' => $data['document_type'] ?? 'other',
            'processing_status' => 'uploaded',
        ]);
    }
}
