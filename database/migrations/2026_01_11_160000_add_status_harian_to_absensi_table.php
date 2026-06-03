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
        Schema::table('absensi', function (Blueprint $table) {
            // Status harian final (setelah selesai absen pulang atau auto-close)
            $table->enum('status_harian', [
                'BELUM_FINAL',          // Masih menunggu absen pulang (hari yang sama)
                'HADIR_TEPAT_WAKTU',    // Sudah absen masuk & pulang, masuk tepat waktu
                'HADIR_TELAT',          // Sudah absen masuk & pulang, masuk terlambat
                'ALPHA',                // Tidak absen masuk ATAU masuk tapi tidak absen pulang (auto-close H+1)
                'IZIN',                 // Ada izin yang disetujui
                'LIBUR'                 // Hari libur
            ])->default('BELUM_FINAL')->after('status');
            
            // Catatan untuk auto-close atau kasus khusus
            $table->text('catatan_sistem')->nullable()->after('catatan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensi', function (Blueprint $table) {
            $table->dropColumn(['status_harian', 'catatan_sistem']);
        });
    }
};
