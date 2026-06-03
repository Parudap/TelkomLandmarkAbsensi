<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HapusSemuaPesertaSeeder extends Seeder
{
    public function run()
    {
        // Hapus history izin
        DB::table('izin')->truncate();
        // Hapus history absensi jika ada
        if (Schema::hasTable('absensi')) {
            DB::table('absensi')->truncate();
        }
        // Hapus peserta (user dengan role peserta)
        DB::table('users')->where('role', 'peserta')->delete();
    }
}
