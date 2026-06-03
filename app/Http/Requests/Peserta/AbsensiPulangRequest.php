<?php

namespace App\Http\Requests\Peserta;

use Illuminate\Foundation\Http\FormRequest;

class AbsensiPulangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'latitude' => [
                'required',
                'numeric',
                'between:-90,90'
            ],
            'longitude' => [
                'required',
                'numeric',
                'between:-180,180'
            ],
            'foto' => [
                'required',
                'image',
                'mimes:jpeg,jpg,png',
                'max:2048' // 2MB
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'latitude.required' => 'Data lokasi GPS diperlukan.',
            'longitude.required' => 'Data lokasi GPS diperlukan.',
            'foto.required' => 'Foto selfie wajib diunggah.',
            'foto.image' => 'File harus berupa gambar.',
            'foto.mimes' => 'Format foto harus JPG, JPEG, atau PNG.',
            'foto.max' => 'Ukuran foto maksimal 2MB.',
        ];
    }
}
