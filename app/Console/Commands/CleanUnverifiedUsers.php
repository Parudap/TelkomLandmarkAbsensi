<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanUnverifiedUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:clean-unverified {--force : Force delete without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all unverified users (email not verified)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Cari semua user yang belum verify email
        $unverifiedUsers = User::whereNull('email_verified_at')
            ->where('role', 'peserta_magang')
            ->get();

        if ($unverifiedUsers->isEmpty()) {
            $this->info('✅ Tidak ada user yang belum terverifikasi.');
            return 0;
        }

        $this->newLine();
        $this->info("🔍 Ditemukan {$unverifiedUsers->count()} user yang belum terverifikasi:");
        $this->newLine();

        $this->table(
            ['ID', 'Name', 'Email', 'Registered At'],
            $unverifiedUsers->map(function ($user) {
                return [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->created_at->format('d M Y H:i'),
                ];
            })
        );

        if (!$this->option('force')) {
            if (!$this->confirm('Hapus semua user di atas?')) {
                $this->info('❌ Dibatalkan.');
                return 0;
            }
        }

        $deleted = 0;
        foreach ($unverifiedUsers as $user) {
            // Hapus file surat magang jika ada
            if ($user->surat_magang) {
                Storage::disk('public')->delete($user->surat_magang);
            }

            $user->delete();
            $deleted++;
        }

        $this->newLine();
        $this->info("✅ Berhasil menghapus {$deleted} user yang belum terverifikasi!");

        return 0;
    }
}
