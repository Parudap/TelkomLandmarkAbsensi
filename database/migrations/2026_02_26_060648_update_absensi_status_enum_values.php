<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing 'IZIN' records first based on catatan_sistem
        DB::statement("
            UPDATE absensi 
            SET status_harian = 'IZIN_TIDAK_MASUK', 
                status = 'IZIN_TIDAK_MASUK'
            WHERE (status = 'IZIN' OR status_harian = 'IZIN')
            AND catatan_sistem LIKE '%tidak masuk%'
        ");
        
        DB::statement("
            UPDATE absensi 
            SET status_harian = 'IZIN_PULANG_CEPAT', 
                status = 'IZIN_PULANG_CEPAT'
            WHERE (status = 'IZIN' OR status_harian = 'IZIN')
            AND catatan_sistem LIKE '%pulang cepat%'
        ");
        
        // Modify ENUM to include new values and remove old IZIN
        DB::statement("
            ALTER TABLE absensi 
            MODIFY COLUMN status ENUM('HADIR_TEPAT_WAKTU','HADIR_TELAT','IZIN_TIDAK_MASUK','IZIN_PULANG_CEPAT','ALPHA','LIBUR') 
            NOT NULL DEFAULT 'ALPHA'
        ");
        
        DB::statement("
            ALTER TABLE absensi 
            MODIFY COLUMN status_harian ENUM('BELUM_FINAL','HADIR_TEPAT_WAKTU','HADIR_TELAT','IZIN_TIDAK_MASUK','IZIN_PULANG_CEPAT','ALPHA','LIBUR') 
            NOT NULL DEFAULT 'BELUM_FINAL'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert new values back to old IZIN
        DB::statement("
            UPDATE absensi 
            SET status_harian = 'IZIN', status = 'IZIN'
            WHERE status_harian IN ('IZIN_TIDAK_MASUK', 'IZIN_PULANG_CEPAT')
            OR status IN ('IZIN_TIDAK_MASUK', 'IZIN_PULANG_CEPAT')
        ");
        
        // Revert ENUM to old values
        DB::statement("
            ALTER TABLE absensi 
            MODIFY COLUMN status ENUM('HADIR_TEPAT_WAKTU','HADIR_TELAT','IZIN','ALPHA','LIBUR') 
            NOT NULL DEFAULT 'ALPHA'
        ");
        
        DB::statement("
            ALTER TABLE absensi 
            MODIFY COLUMN status_harian ENUM('BELUM_FINAL','HADIR_TEPAT_WAKTU','HADIR_TELAT','ALPHA','IZIN','LIBUR') 
            NOT NULL DEFAULT 'BELUM_FINAL'
        ");
    }
};
