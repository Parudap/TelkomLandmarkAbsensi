<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Bidang extends Model
{
    protected $table = 'bidang';

    protected $fillable = [
        'nama_bidang',
        'kode_bidang',
        // 'deskripsi',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // Relasi dengan Users (peserta magang)
    public function peserta(): HasMany
    {
        return $this->hasMany(User::class, 'bidang_id')->where('role', 'peserta_magang');
    }

    // Relasi ketuaBidang dihapus

    // Semua user di bidang ini
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'bidang_id');
    }
}
