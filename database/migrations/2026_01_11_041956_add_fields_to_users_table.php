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
        Schema::table('users', function (Blueprint $table) {
            // Role: peserta_magang, hr
            $table->enum('role', ['peserta_magang', 'hr'])->default('peserta_magang')->after('password');
            
            // Data Peserta Magang
            $table->date('tanggal_lahir')->nullable()->after('role');
            $table->text('alamat')->nullable()->after('tanggal_lahir');
            $table->string('no_telepon', 20)->nullable()->after('alamat');
            $table->string('instansi_asal')->nullable()->after('no_telepon');
            $table->date('periode_magang_mulai')->nullable()->after('instansi_asal');
            $table->date('periode_magang_selesai')->nullable()->after('periode_magang_mulai');
            $table->string('surat_magang')->nullable()->after('periode_magang_selesai'); // path file
            
            // Bidang
            $table->foreignId('bidang_id')->nullable()->constrained('bidang')->onDelete('set null')->after('surat_magang');
            
            // Status Approval
            $table->enum('status_approval', ['pending', 'approved', 'rejected'])->default('pending')->after('bidang_id');
            $table->text('alasan_penolakan')->nullable()->after('status_approval');
            $table->timestamp('approved_at')->nullable()->after('alasan_penolakan');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null')->after('approved_at');
            
            // Status Akun
            $table->boolean('is_active')->default(false)->after('approved_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['bidang_id']);
            $table->dropForeign(['approved_by']);
            $table->dropColumn([
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
                'is_active'
            ]);
        });
    }
};
