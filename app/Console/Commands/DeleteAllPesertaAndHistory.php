<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Absensi;
use App\Models\Izin;

class DeleteAllPesertaAndHistory extends Command
{
    protected $signature = 'peserta:delete-all';
    protected $description = 'Hapus semua akun peserta magang beserta seluruh histori absensi dan izin mereka';

    public function handle()
    {
        $this->info('Menghapus semua histori absensi...');
        Absensi::truncate();
        $this->info('Menghapus semua histori izin...');
        Izin::truncate();
        $this->info('Menghapus semua akun peserta magang...');
        User::where('role', 'peserta_magang')->delete();
        $this->info('Selesai! Semua peserta dan histori sudah dihapus.');
        return 0;
    }
}
