<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Absensi extends Model
{
    protected $table = 'absensi';

    protected $fillable = [
        'user_id',
        'izin_id',
        'tanggal',
        'jam_masuk',
        'foto_masuk',
        'latitude_masuk',
        'longitude_masuk',
        'jam_pulang',
        'foto_pulang',
        'latitude_pulang',
        'longitude_pulang',
        'status',
        'status_masuk',
        'status_harian',
        'durasi_kerja',
        'catatan',
        'catatan_sistem',
        'keterangan',
        'created_at',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            // jam_masuk dan jam_pulang tidak perlu cast datetime karena field di DB adalah TIME
            // Akan di-handle manual saat digunakan
            'latitude_masuk' => 'decimal:8',
            'longitude_masuk' => 'decimal:8',
            'latitude_pulang' => 'decimal:8',
            'longitude_pulang' => 'decimal:8',
            'durasi_kerja' => 'integer',
        ];
    }

    // Relasi dengan User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relasi dengan Izin
    public function izin(): BelongsTo
    {
        return $this->belongsTo(Izin::class);
    }

    // Scope untuk filter status
    public function scopeHadir($query)
    {
        return $query->whereIn('status_harian', ['HADIR_TEPAT_WAKTU', 'HADIR_TELAT']);
    }

    public function scopeTelat($query)
    {
        return $query->where('status_harian', 'HADIR_TELAT');
    }

    public function scopeAlpha($query)
    {
        return $query->where('status_harian', 'ALPHA');
    }
    
    public function scopeBelumFinal($query)
    {
        return $query->where('status_harian', 'BELUM_FINAL');
    }
    
    public function scopeIzin($query)
    {
        return $query->whereIn('status_harian', ['IZIN_TIDAK_MASUK', 'IZIN_PULANG_CEPAT']);
    }
}
