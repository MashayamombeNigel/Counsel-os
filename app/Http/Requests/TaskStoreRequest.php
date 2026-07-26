<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'matter_id' => ['required', 'integer', 'exists:matters,id'],
            'source_document_id' => ['nullable', 'integer', 'exists:documents,id'],
            'title' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'due_date' => ['nullable', 'date'],
            'status' => ['required', 'in:open,in_progress,done'],
            'priority' => ['required', 'in:low,medium,high'],
            'created_by' => ['required'],
            'completed_at' => ['nullable'],
        ];
    }
}
