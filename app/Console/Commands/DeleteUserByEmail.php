<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DeleteUserByEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:delete-by-email {email} {--force : Skip confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hapus user beserta data-datanya berdasarkan email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User dengan email {$email} tidak ditemukan.");
            return 1;
        }

        if (!$this->option('force')) {
            $this->warn("⚠️  PERINGATAN: Operasi ini akan menghapus user {$user->name} ({$user->email}) dan semua data terkait!");
            if (!$this->confirm('Apakah Anda yakin ingin melanjutkan?')) {
                $this->info('Operasi dibatalkan.');
                return 0;
            }
        }

        try {
            DB::beginTransaction();

            // Delete files
            if ($user->surat_magang) {
                try {
                    Storage::disk('public')->delete($user->surat_magang);
                } catch (\Exception $e) {
                    $this->warn("Gagal menghapus file surat magang: {$e->getMessage()}");
                }
            }
            $izinRecords = $user->izin;
            foreach ($izinRecords as $izin) {
                if ($izin->bukti_file) {
                    try {
                        Storage::disk('public')->delete($izin->bukti_file);
                    } catch (\Exception $e) {
                        $this->warn("Gagal menghapus file bukti izin: {$e->getMessage()}");
                    }
                }
            }
            $absensiRecords = $user->absensi;
            foreach ($absensiRecords as $absensi) {
                if ($absensi->foto_masuk) {
                    try {
                        Storage::disk('public')->delete($absensi->foto_masuk);
                    } catch (\Exception $e) {
                        $this->warn("Gagal menghapus foto masuk: {$e->getMessage()}");
                    }
                }
                if ($absensi->foto_pulang) {
                    try {
                        Storage::disk('public')->delete($absensi->foto_pulang);
                    } catch (\Exception $e) {
                        $this->warn("Gagal menghapus foto pulang: {$e->getMessage()}");
                    }
                }
            }

            // Delete related data
            $absensiCount = DB::table('absensi')->where('user_id', $user->id)->delete();
            $izinCount = DB::table('izin')->where('user_id', $user->id)->delete();
            $approvalCount = DB::table('approval_logs')
                ->where(function($q) use ($user) {
                    $q->where(function($q2) use ($user) {
                        $q2->where('approvable_type', 'App\\Models\\User')
                           ->where('approvable_id', $user->id);
                    })
                    ->orWhere(function($q2) use ($user) {
                        $izinIds = DB::table('izin')->where('user_id', $user->id)->pluck('id');
                        $q2->where('approvable_type', 'App\\Models\\Izin')
                           ->whereIn('approvable_id', $izinIds);
                    });
                })
                ->delete();

            $deletedCount = $user->delete();

            DB::commit();

            $this->info('✅ User dan seluruh data terkait berhasil dihapus!');
            $this->info("Ringkasan:");
            $this->info("  • User dihapus: {$user->name} ({$user->email})");
            $this->info("  • Record absensi dihapus: {$absensiCount}");
            $this->info("  • Record izin dihapus: {$izinCount}");
            $this->info("  • Approval logs dihapus: {$approvalCount}");
            return 0;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Terjadi kesalahan: ' . $e->getMessage());
            $this->error('Semua perubahan telah dibatalkan (rollback).');
            return 1;
        }
    }
}
