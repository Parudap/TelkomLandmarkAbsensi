<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Absensi;
use App\Models\Izin;
use App\Models\ApprovalLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanUserData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:clean-data 
                            {identifier? : Email atau nama user}
                            {--all : Hapus semua data absensi & izin (untuk testing)}
                            {--force : Skip konfirmasi}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bersihkan data absensi dan izin untuk user tertentu';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $identifier = $this->argument('identifier');
        $all = $this->option('all');
        $force = $this->option('force');

        // Jika --all, hapus semua data
        if ($all) {
            return $this->cleanAllData($force);
        }

        // Jika tidak ada identifier, tanyakan
        if (!$identifier) {
            $identifier = $this->ask('Masukkan email atau nama user yang akan dibersihkan');
        }

        if (!$identifier) {
            $this->error('❌ Email atau nama user tidak boleh kosong!');
            return Command::FAILURE;
        }

        // Cari user
        $user = User::where('email', $identifier)
            ->orWhere('name', 'like', "%{$identifier}%")
            ->first();

        if (!$user) {
            $this->error("❌ User dengan identifier '{$identifier}' tidak ditemukan!");
            return Command::FAILURE;
        }

        // Tampilkan info user
        $this->info("📋 User ditemukan:");
        $this->newLine();
        $this->table(
            ['Field', 'Value'],
            [
                ['ID', $user->id],
                ['Nama', $user->name],
                ['Email', $user->email],
                ['Role', $user->role],
                ['Bidang', $user->bidang?->nama_bidang ?? '-'],
            ]
        );

        // Hitung data yang akan dihapus
        $absensiCount = Absensi::where('user_id', $user->id)->count();
        $izinCount = Izin::where('user_id', $user->id)->count();

        $this->newLine();
        $this->warn("⚠️  Data yang akan dihapus:");
        $this->comment("   - Absensi: {$absensiCount} record");
        $this->comment("   - Izin: {$izinCount} record");
        $this->newLine();

        // Konfirmasi
        if (!$force && !$this->confirm("Apakah Anda yakin ingin menghapus semua data untuk user '{$user->name}'?")) {
            $this->info('❌ Operasi dibatalkan.');
            return Command::FAILURE;
        }

        // Mulai transaksi
        DB::beginTransaction();
        
        try {
            // Hapus data
            $deletedAbsensi = Absensi::where('user_id', $user->id)->delete();
            $deletedIzin = Izin::where('user_id', $user->id)->delete();
            
            DB::commit();

            $this->newLine();
            $this->info('✅ Data berhasil dibersihkan!');
            $this->newLine();
            $this->comment("📊 Summary:");
            $this->comment("   - Absensi dihapus: {$deletedAbsensi} record");
            $this->comment("   - Izin dihapus: {$deletedIzin} record");

            return Command::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();
            
            $this->error('❌ Error saat menghapus data: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Hapus semua data absensi & izin (untuk testing)
     */
    private function cleanAllData($force)
    {
        $this->warn('⚠️⚠️⚠️  MODE DANGER: HAPUS SEMUA DATA  ⚠️⚠️⚠️');
        $this->newLine();

        $absensiCount = Absensi::count();
        $izinCount = Izin::count();
        $approvalCount = ApprovalLog::whereIn('approvable_type', ['App\Models\Izin'])->count();

        $this->error("Data yang akan DIHAPUS PERMANEN:");
        $this->comment("   - Absensi: {$absensiCount} record");
        $this->comment("   - Izin: {$izinCount} record");
        $this->comment("   - Approval Logs: {$approvalCount} record");
        $this->newLine();

        if (!$force) {
            $this->error('⚠️  PERINGATAN: Ini akan menghapus SEMUA data absensi dan izin!');
            $this->newLine();
            
            $confirmation = $this->ask('Ketik "HAPUS SEMUA" untuk konfirmasi (case-sensitive)');
            
            if ($confirmation !== 'HAPUS SEMUA') {
                $this->info('❌ Operasi dibatalkan.');
                return Command::FAILURE;
            }
        }

        DB::beginTransaction();
        
        try {
            // Hapus approval logs untuk izin
            $deletedApprovals = ApprovalLog::whereIn('approvable_type', ['App\Models\Izin'])->delete();
            
            // Hapus izin
            $deletedIzin = Izin::query()->delete();
            
            // Hapus absensi
            $deletedAbsensi = Absensi::query()->delete();
            
            DB::commit();

            $this->newLine();
            $this->info('✅ SEMUA DATA BERHASIL DIHAPUS!');
            $this->newLine();
            $this->comment("📊 Summary:");
            $this->comment("   - Absensi: {$deletedAbsensi} record");
            $this->comment("   - Izin: {$deletedIzin} record");
            $this->comment("   - Approval Logs: {$deletedApprovals} record");

            return Command::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();
            
            $this->error('❌ Error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
