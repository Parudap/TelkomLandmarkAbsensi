<?php

namespace App\Http\Requests\HR;

use Illuminate\Foundation\Http\FormRequest;

class RejectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'keterangan' => [
                'required',
                'string',
                'max:500'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'keterangan.required' => 'Alasan penolakan harus diisi.',
            'keterangan.max' => 'Alasan penolakan maksimal 500 karakter.',
        ];
    }
}
