<?php

namespace App\Http\Requests\HR;

use Illuminate\Foundation\Http\FormRequest;

class ApprovalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'keterangan' => [
                'nullable',
                'string',
                'max:500'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'keterangan.max' => 'Keterangan maksimal 500 karakter.',
        ];
    }
}
