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
        // 1. Hapus semua user dengan role ketua_bidang
        DB::table('users')->where('role', 'ketua_bidang')->delete();
        
        // 2. Drop table ketua_bidang_logs jika ada
        Schema::dropIfExists('ketua_bidang_logs');
        
        // 3. Hapus kolom auto_approved_ketua_at dari tabel izin jika ada
        if (Schema::hasColumn('izin', 'auto_approved_ketua_at')) {
            Schema::table('izin', function (Blueprint $table) {
                $table->dropColumn('auto_approved_ketua_at');
            });
        }
        
        // 4. Update enum role di users table - hapus ketua_bidang
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('peserta_magang', 'hr') DEFAULT 'peserta_magang'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan enum role dengan ketua_bidang
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('peserta_magang', 'ketua_bidang', 'hr') DEFAULT 'peserta_magang'");
        
        // Kembalikan kolom auto_approved_ketua_at di izin
        if (!Schema::hasColumn('izin', 'auto_approved_ketua_at')) {
            Schema::table('izin', function (Blueprint $table) {
                $table->timestamp('auto_approved_ketua_at')->nullable()->after('keterangan_hr');
            });
        }
        
        // Recreate ketua_bidang_logs table
        Schema::create('ketua_bidang_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('bidang_id')->constrained()->onDelete('cascade');
            $table->enum('action', ['assigned', 'removed']);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }
};
