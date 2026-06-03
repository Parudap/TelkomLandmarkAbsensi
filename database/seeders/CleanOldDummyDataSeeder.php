<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class CleanOldDummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Membersihkan data dummy lama...');
        
        // Hapus user dengan email yang bukan dari domain example.com dan bukan HR
        $deleted = User::where('role', 'peserta_magang')
            ->where('email', 'not like', '%@example.com')
            ->delete();
        
        $this->command->info("✓ Berhasil menghapus {$deleted} data peserta lama");
        
        // Hitung data yang tersisa
        $remainingPeserta = User::where('role', 'peserta_magang')->count();
        $remainingHR = User::where('role', 'hr')->count();
        
        $this->command->info("✓ Data tersisa: {$remainingPeserta} peserta magang, {$remainingHR} HR");
    }
}
