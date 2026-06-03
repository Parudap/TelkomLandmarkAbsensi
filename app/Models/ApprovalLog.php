<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ApprovalLog extends Model
{
    protected $fillable = [
        'approvable_id',
        'approvable_type',
        'tipe_approval',
        'status',
        'approver_id',
        'approver_role',
        'keterangan',
        'approved_at',
        'created_at',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
        ];
    }

    // Relasi polymorphic
    public function approvable(): MorphTo
    {
        return $this->morphTo();
    }

    // Relasi dengan Approver
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    // Relasi dengan User (jika ada kolom user_id di approval_logs)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
