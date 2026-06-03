<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Izin extends Model
{
    protected $table = 'izin';

    protected $fillable = [
        'user_id',
        'jenis_izin',
        'tanggal_mulai',
        'tanggal_selesai',
        'tanggal',
        'jam_pulang_diajukan',
        'alasan',
        'bukti_file',
        'status_approval',
        'approved_by_hr',
        'approved_at_hr',
        'keterangan_hr',
        'auto_approved_hr_at',
        'created_at',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'tanggal' => 'date',
            'approved_at_hr' => 'datetime',
            'auto_approved_hr_at' => 'datetime',
        ];
    }

    // Relasi dengan User (pengaju)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relasi approvedByKetuaBidang dihapus

    // Relasi dengan Approver HR
    public function approvedByHr(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_hr');
    }

    // Relasi polymorphic dengan approval logs
    public function approvalLogs(): MorphMany
    {
        return $this->morphMany(ApprovalLog::class, 'approvable');
    }

    // Scope
    public function scopePending($query)
    {
        return $query->where('status_approval', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status_approval', 'approved_hr');
    }
}
