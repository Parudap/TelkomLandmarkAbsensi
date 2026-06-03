<?php

namespace App\Http\Requests\Peserta;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class IzinRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'jenis_izin' => [
                'required',
                'in:sakit,izin,cuti'
            ],
            'tanggal_mulai' => [
                'required',
                'date',
                'after_or_equal:today'
            ],
            'tanggal_selesai' => [
                'required',
                'date',
                'after_or_equal:tanggal_mulai'
            ],
            'keterangan' => [
                'required',
                'string',
                'max:500'
            ],
            'bukti' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:2048' // 2MB
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'jenis_izin.required' => 'Jenis izin harus dipilih.',
            'jenis_izin.in' => 'Jenis izin tidak valid.',
            'tanggal_mulai.required' => 'Tanggal mulai harus diisi.',
            'tanggal_mulai.after_or_equal' => 'Tanggal mulai tidak boleh kurang dari hari ini.',
            'tanggal_selesai.required' => 'Tanggal selesai harus diisi.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh kurang dari tanggal mulai.',
            'keterangan.required' => 'Keterangan izin harus diisi.',
            'keterangan.max' => 'Keterangan maksimal 500 karakter.',
            'bukti.file' => 'Bukti harus berupa file.',
            'bukti.mimes' => 'Format file harus PDF, JPG, JPEG, atau PNG.',
            'bukti.max' => 'Ukuran file maksimal 2MB.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validasi maksimal durasi izin
            $tanggalMulai = Carbon::parse($this->tanggal_mulai);
            $tanggalSelesai = Carbon::parse($this->tanggal_selesai);
            $durasi = $tanggalMulai->diffInDays($tanggalSelesai) + 1;

            if ($durasi > 14) {
                $validator->errors()->add('tanggal_selesai', 'Durasi izin maksimal 14 hari.');
            }

            // Validasi bukti wajib untuk sakit > 2 hari
            if ($this->jenis_izin === 'sakit' && $durasi > 2 && !$this->hasFile('bukti')) {
                $validator->errors()->add('bukti', 'Surat keterangan dokter wajib diunggah untuk sakit lebih dari 2 hari.');
            }
        });
    }
}
