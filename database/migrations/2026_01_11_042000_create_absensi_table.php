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
        Schema::create('absensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('tanggal');
            
            // Absen Masuk
            $table->time('jam_masuk')->nullable();
            $table->string('foto_masuk')->nullable(); // path foto selfie
            $table->decimal('latitude_masuk', 10, 8)->nullable();
            $table->decimal('longitude_masuk', 11, 8)->nullable();
            
            // Absen Pulang
            $table->time('jam_pulang')->nullable();
            $table->string('foto_pulang')->nullable(); // path foto selfie
            $table->decimal('latitude_pulang', 10, 8)->nullable();
            $table->decimal('longitude_pulang', 11, 8)->nullable();
            
            // Status: HADIR_TEPAT_WAKTU, HADIR_TELAT, IZIN, ALPHA, LIBUR
            $table->enum('status', ['HADIR_TEPAT_WAKTU', 'HADIR_TELAT', 'IZIN', 'ALPHA', 'LIBUR'])->default('ALPHA');
            
            // Durasi kerja (dalam menit)
            $table->integer('durasi_kerja')->nullable();
            
            // Keterangan tambahan
            $table->text('keterangan')->nullable();
            
            $table->timestamps();
            
            // Index untuk query cepat
            $table->index(['user_id', 'tanggal']);
            $table->unique(['user_id', 'tanggal']); // Satu user hanya bisa absen sekali per hari
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi');
    }
};
