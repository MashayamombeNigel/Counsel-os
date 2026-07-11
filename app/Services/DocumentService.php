<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Matter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class DocumentService
{
    /**
     * Store an uploaded file to the private disk and create the
     * document record. Uses a generated filename to avoid collisions
     * and to avoid trusting user-supplied filenames as storage paths -
     * the original name is kept separately for display only.
     */
    public function storeUpload(Matter $matter, UploadedFile $file, string $documentType, int $uploadedBy): Document
    {
        $extension = $file->getClientOriginalExtension();
        $generatedName = Str::uuid() . '.' . $extension;

        $path = $file->storeAs(
            "matters/{$matter->id}/documents",
            $generatedName,
            'local', // private disk - not publicly accessible, per spec Section 17
        );

        return Document::create([
            'matter_id' => $matter->id,
            'uploaded_by' => $uploadedBy,
            'filename' => $generatedName,
            'original_name' => $file->getClientOriginalName(),
            'storage_path' => $path,
            'mime_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
            'document_type' => $documentType,
            'processing_status' => 'uploaded',
        ]);
    }

    public function delete(Document $document): void
    {
        Storage::disk('local')->delete($document->storage_path);
        $document->delete();
    }
}
