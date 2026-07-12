<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'mimes:pdf,docx', 'max:20480'], // 20MB in KB
            'document_type' => [
                'required',
                'in:contract,lease,title_deed,correspondence,research,other',
            ],
        ];
    }
}
