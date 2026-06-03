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
            $table->enum('status_masuk', ['TEPAT_WAKTU', 'TELAT'])->nullable()->after('status');
            $table->string('catatan')->nullable()->after('durasi_kerja');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensi', function (Blueprint $table) {
            $table->dropColumn(['status_masuk', 'catatan']);
        });
    }
};
