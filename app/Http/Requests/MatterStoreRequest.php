<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MatterStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_id' => ['required', 'integer', 'exists:clients,id'],
            'title' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'practice_area' => ['nullable', 'string'],
            'status' => ['required', 'in:open,in_review,waiting_client,closed'],
            'opened_at' => ['nullable'],
            'closed_at' => ['nullable'],
        ];
    }
}
