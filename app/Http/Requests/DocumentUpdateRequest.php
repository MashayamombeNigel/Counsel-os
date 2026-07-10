<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DocumentUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'matter_id' => ['required', 'integer', 'exists:matters,id'],
            'uploaded_by' => ['required'],
            'filename' => ['required', 'string'],
            'original_name' => ['required', 'string'],
            'storage_path' => ['required', 'string'],
            'mime_type' => ['required', 'string'],
            'file_size' => ['required', 'integer'],
            'document_type' => ['required', 'in:contract,lease,title_deed,correspondence,research,other'],
            'extracted_text' => ['nullable', 'string'],
            'processing_status' => ['required', 'in:uploaded,extracting,analysis_pending,analyzed,failed'],
            'error_message' => ['nullable', 'string'],
        ];
    }
}
