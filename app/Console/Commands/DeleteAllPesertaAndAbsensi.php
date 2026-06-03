<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Absensi;

class DeleteAllPesertaAndAbsensi extends Command
{
    protected $signature = 'clean:peserta-absensi';
    protected $description = 'Hapus semua akun peserta magang dan seluruh riwayat absensi mereka';

    public function handle()
    {
        $this->info('Menghapus semua absensi peserta magang...');
        $absensiCount = Absensi::whereHas('user', function($q){
            $q->where('role', 'peserta_magang');
        })->delete();
        $this->info("Absensi peserta terhapus: $absensiCount");

        $this->info('Menghapus semua akun peserta magang...');
        $userCount = User::where('role', 'peserta_magang')->delete();
        $this->info("Akun peserta terhapus: $userCount");

        $this->info('Selesai!');
        return 0;
    }
}
