<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Absensi;
use App\Models\Izin;

class DeleteNedulaxHistory extends Command
{
    protected $signature = 'peserta:delete-nedulax';
    protected $description = 'Hapus user dan seluruh histori absensi/izin untuk email nedulax@gmail.com';

    public function handle()
    {
        $this->info('Menghapus histori absensi nedulax@gmail.com ...');
        $user = User::where('email', 'nedulax@gmail.com')->first();
        if ($user) {
            Absensi::where('user_id', $user->id)->delete();
            Izin::where('user_id', $user->id)->delete();
            $user->delete();
            $this->info('Selesai! Semua histori dan akun sudah dihapus.');
        } else {
            $this->info('User tidak ditemukan.');
        }
        return 0;
    }
}
