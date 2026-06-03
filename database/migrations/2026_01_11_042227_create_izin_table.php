<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('izin', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Jenis izin: tidak_masuk, pulang_cepat
            $table->enum('jenis_izin', ['tidak_masuk', 'pulang_cepat']);
            
            // Untuk izin tidak masuk
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            
            // Untuk izin pulang cepat
            $table->date('tanggal')->nullable();
            $table->time('jam_pulang_diajukan')->nullable();
            
            // Alasan dan bukti
            $table->text('alasan');
            $table->string('bukti_file')->nullable(); // path file bukti (untuk sakit/cuti)
            
            // Status approval: pending, approved_hr, rejected_hr, auto_approved
            $table->enum('status_approval', [
                'pending',
                'approved_hr',
                'rejected_hr',
                'auto_approved'
            ])->default('pending');
            
            // Approval Layer 2 (HR)
            $table->foreignId('approved_by_hr')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at_hr')->nullable();
            $table->text('keterangan_hr')->nullable();
            
            // Auto approval tracking
            $table->timestamp('auto_approved_hr_at')->nullable();
            
            $table->timestamps();
            
            // Index
            $table->index(['user_id', 'status_approval']);
            $table->index('tanggal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('izin');
    }
};
