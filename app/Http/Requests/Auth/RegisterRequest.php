<?php

namespace App\Http\Requests\Auth;

use App\Models\SystemSetting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Registrasi terbuka untuk publik
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $maxFileSize = SystemSetting::get('max_file_size', 2048); // KB
        $allowedTypes = explode(',', SystemSetting::get('allowed_file_types', 'pdf,jpg,jpeg,png'));
        
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
            'tanggal_lahir' => ['required', 'date', 'before_or_equal:today'],
            'alamat' => ['required', 'string', 'max:1000'],
            'no_telepon' => ['required', 'string', 'regex:/^[0-9]{10,15}$/'],
            'instansi_asal' => ['required', 'string', 'max:255'],
            'periode_magang_mulai' => ['required', 'date', 'after_or_equal:today'],
            'periode_magang_selesai' => ['required', 'date', 'after:periode_magang_mulai'],
            'bidang_id' => ['required', 'exists:bidang,id'],
            'surat_magang' => [
                'required', 
                'file', 
                'mimes:' . implode(',', $allowedTypes),
                'max:' . $maxFileSize
            ],
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'nama lengkap',
            'email' => 'email',
            'password' => 'password',
            'tanggal_lahir' => 'tanggal lahir',
            'alamat' => 'alamat',
            'no_telepon' => 'nomor telepon',
            'instansi_asal' => 'instansi asal',
            'periode_magang_mulai' => 'tanggal mulai magang',
            'periode_magang_selesai' => 'tanggal selesai magang',
            'bidang_id' => 'bidang',
            'surat_magang' => 'surat konfirmasi magang',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'no_telepon.regex' => 'Nomor telepon harus berisi 10-15 digit angka.',
            'password.letters' => 'Password harus mengandung huruf.',
            'password.numbers' => 'Password harus mengandung angka.',
            'periode_magang_selesai.after' => 'Tanggal selesai harus setelah tanggal mulai.',
            'surat_magang.mimes' => 'Surat magang harus berformat PDF, JPG, JPEG, atau PNG.',
        ];
    }
}
