<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DeleteAllInterns extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'interns:delete-all {--force : Skip confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hapus semua akun peserta magang beserta data-datanya (absensi, izin, approval logs)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get confirmation unless --force flag is used
        if (!$this->option('force')) {
            $this->warn('⚠️  PERINGATAN: Operasi ini akan menghapus SEMUA akun peserta magang dan data terkaitnya!');
            $this->warn('Data yang akan dihapus:');
            $this->warn('  • Semua user dengan role "peserta_magang"');
            $this->warn('  • Semua data absensi mereka');
            $this->warn('  • Semua data izin mereka');
            $this->warn('  • Semua approval logs terkait');
            $this->warn('  • Semua file surat magang');
            $this->warn('  • Semua file bukti izin');
            
            if (!$this->confirm('Apakah Anda yakin ingin melanjutkan?')) {
                $this->info('Operasi dibatalkan.');
                return 0;
            }
        }

        try {
            DB::beginTransaction();

            // Get all intern users
            $interns = User::where('role', 'peserta_magang')->get();
            $internCount = $interns->count();

            if ($internCount === 0) {
                $this->info('Tidak ada peserta magang untuk dihapus.');
                return 0;
            }

            $this->info("Menemukan {$internCount} akun peserta magang untuk dihapus...");

            // Delete files for each intern
            $this->info('Menghapus file-file terkait...');
            foreach ($interns as $intern) {
                // Delete surat magang file
                if ($intern->surat_magang) {
                    try {
                        Storage::disk('public')->delete($intern->surat_magang);
                    } catch (\Exception $e) {
                        $this->warn("Gagal menghapus file surat magang untuk {$intern->name}: {$e->getMessage()}");
                    }
                }

                // Delete bukti file for izin
                $izinRecords = $intern->izin;
                foreach ($izinRecords as $izin) {
                    if ($izin->bukti_file) {
                        try {
                            Storage::disk('public')->delete($izin->bukti_file);
                        } catch (\Exception $e) {
                            $this->warn("Gagal menghapus file bukti izin untuk {$intern->name}: {$e->getMessage()}");
                        }
                    }
                }

                // Delete foto absensi files
                $absensiRecords = $intern->absensi;
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
            }

            // Get intern user IDs
            $internIds = $interns->pluck('id')->toArray();

            // Delete related data
            $this->info('Menghapus data absensi...');
            $absensiCount = DB::table('absensi')->whereIn('user_id', $internIds)->delete();
            $this->line("  ✓ Dihapus {$absensiCount} record absensi");

            $this->info('Menghapus data izin...');
            $izinCount = DB::table('izin')->whereIn('user_id', $internIds)->delete();
            $this->line("  ✓ Dihapus {$izinCount} record izin");

            // Delete approval logs related to interns (where approvable_type is Izin or User)
            $this->info('Menghapus approval logs...');
            // For Izin approvals (polymorphic)
            $approvalCount = DB::table('approval_logs')
                ->where('approvable_type', 'App\\Models\\Izin')
                ->whereIn('approvable_id', function ($query) use ($internIds) {
                    $query->select('id')->from('izin')->whereIn('user_id', $internIds);
                })
                ->delete();
            
            // For User registration approvals
            $approvalCount += DB::table('approval_logs')
                ->where('approvable_type', 'App\\Models\\User')
                ->whereIn('approvable_id', $internIds)
                ->delete();
            
            $this->line("  ✓ Dihapus {$approvalCount} record approval logs");

            // Delete the intern users
            $this->info('Menghapus akun peserta magang...');
            $deletedCount = User::whereIn('id', $internIds)->delete();
            $this->line("  ✓ Dihapus {$deletedCount} akun peserta magang");

            DB::commit();

            $this->info('✅ Operasi penghapusan berhasil!');
            $this->info("Ringkasan:");
            $this->info("  • Akun peserta magang dihapus: {$deletedCount}");
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
