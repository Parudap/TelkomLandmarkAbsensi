<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'tanggal_lahir',
        'alamat',
        'no_telepon',
        'instansi_asal',
        'periode_magang_mulai',
        'periode_magang_selesai',
        'surat_magang',
        'bidang_id',
        'status_approval',
        'alasan_penolakan',
        'approved_at',
        'approved_by',
        'is_active',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'tanggal_lahir' => 'date',
            'periode_magang_mulai' => 'date',
            'periode_magang_selesai' => 'date',
            'approved_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    // Relasi dengan Bidang
    public function bidang(): BelongsTo
    {
        return $this->belongsTo(Bidang::class);
    }

    // Relasi dengan Absensi
    public function absensi(): HasMany
    {
        return $this->hasMany(Absensi::class);
    }

    // Relasi dengan Izin
    public function izin(): HasMany
    {
        return $this->hasMany(Izin::class);
    }

    // Relasi dengan Approval Logs (sebagai approver)
    public function approvalLogs(): HasMany
    {
        return $this->hasMany(ApprovalLog::class, 'approver_id');
    }

    // Relasi polymorphic untuk approval user registration
    public function registrationApprovals(): MorphMany
    {
        return $this->morphMany(ApprovalLog::class, 'approvable');
    }

    // User yang menyetujui registrasi
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scope untuk filter role
    public function scopePesertaMagang($query)
    {
        return $query->where('role', 'peserta_magang');
    }

    public function scopeHr($query)
    {
        return $query->where('role', 'hr');
    }

    // Scope untuk status approval
    public function scopePending($query)
    {
        return $query->where('status_approval', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status_approval', 'approved');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
