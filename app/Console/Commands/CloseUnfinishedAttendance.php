<?php

namespace App\Console\Commands;

use App\Models\Absensi;
use App\Models\User;
use App\Models\Izin;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Services\TimeService;

class CloseUnfinishedAttendance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:close-unfinished
                            {--date= : Tanggal yang akan di-close (format: Y-m-d). Default: kemarin}
                            {--dry-run : Tampilkan data tanpa mengubah database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto-close absensi: (1) Tidak masuk sama sekali → ALPHA, (2) Masuk tapi tidak pulang → ALPHA';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        // Tentukan tanggal yang akan di-close (default: kemarin)
        $targetDate = $this->option('date') 
            ? Carbon::parse($this->option('date'))
            : Carbon::yesterday();

        // VALIDASI 1: Cek apakah hari kerja (Senin-Jumat)
        if ($targetDate->isSaturday() || $targetDate->isSunday()) {
            $this->info("⏭️  Tanggal {$targetDate->format('d F Y')} adalah hari {$targetDate->translatedFormat('l')} (weekend).");
            $this->info("✅ Tidak perlu diproses.");
            return Command::SUCCESS;
        }

        $this->info("🔍 Memproses auto-close untuk tanggal: {$targetDate->format('d F Y')} ({$targetDate->translatedFormat('l')})");
        $this->newLine();

        // ========================================================================
        // KASUS 1: TIDAK ABSEN MASUK SAMA SEKALI → CREATE RECORD ALPHA
        // ========================================================================
        $this->comment("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->comment("📋 KASUS 1: Peserta yang tidak absen masuk sama sekali");
        $this->comment("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");

        // Ambil semua peserta magang yang aktif
        $activePeserta = User::where('role', 'peserta_magang')
            ->where('status_approval', 'approved')
            ->where('is_active', true)
            ->get();

        $this->info("👥 Total peserta magang aktif: {$activePeserta->count()}");

        $noAttendanceList = [];
        
        foreach ($activePeserta as $peserta) {
            // Cek apakah sudah ada record absensi untuk tanggal ini
            $existingAbsensi = Absensi::where('user_id', $peserta->id)
                ->whereDate('tanggal', $targetDate)
                ->first();

            if ($existingAbsensi) {
                continue; // Sudah ada record, skip
            }

            // Cek apakah ada izin yang disetujui untuk tanggal ini (cek semua jenis izin)
            $izin = Izin::where('user_id', $peserta->id)
                ->whereIn('status_approval', ['approved_hr', 'auto_approved'])
                ->where(function($query) use ($targetDate) {
                    // Izin single date
                    $query->whereDate('tanggal', $targetDate)
                        // Atau izin range date
                        ->orWhere(function($q) use ($targetDate) {
                            $q->whereDate('tanggal_mulai', '<=', $targetDate)
                              ->whereDate('tanggal_selesai', '>=', $targetDate);
                        });
                })
                ->first();

            if ($izin) {
                // Jika ada izin tapi tidak ada record absensi, buatkan record sesuai jenis izin
                // Ini untuk backup jika saat approval record tidak terbuat
                if (!$isDryRun) {
                    $statusHarian = $izin->jenis_izin == 'tidak_masuk' ? 'IZIN_TIDAK_MASUK' : 'IZIN_PULANG_CEPAT';
                    $jenisIzinText = $izin->jenis_izin == 'tidak_masuk' ? 'Izin tidak masuk' : 'Izin pulang cepat';
                    Absensi::create([
                        'user_id' => $peserta->id,
                        'tanggal' => $targetDate,
                        'status' => $statusHarian,
                        'status_harian' => $statusHarian,
                        'catatan_sistem' => $jenisIzinText,
                        'created_at' => TimeService::now(),
                        'updated_at' => TimeService::now(),
                    ]);
                    $this->info("✅ Created missing {$statusHarian} record for user {$peserta->id} (Auto-close check)");
                }
                continue; // Skip, jangan buat ALPHA
            }

            // Tidak ada absensi DAN tidak ada izin → ALPHA!
            $noAttendanceList[] = $peserta;
        }

        if (empty($noAttendanceList)) {
            $this->info("✅ Semua peserta sudah absen atau memiliki izin.");
        } else {
            $this->warn("⚠️  Ditemukan {count($noAttendanceList)} peserta yang tidak absen:");
            $this->newLine();

            $tableData = [];
            foreach ($noAttendanceList as $peserta) {
                $tableData[] = [
                    'ID' => $peserta->id,
                    'Nama' => $peserta->name,
                    'Email' => $peserta->email,
                    'Bidang' => $peserta->bidang->nama_bidang ?? '-',
                ];
            }

            $this->table(['ID', 'Nama', 'Email', 'Bidang'], $tableData);

            if (!$isDryRun) {
                $created = 0;
                foreach ($noAttendanceList as $peserta) {
                    Absensi::create([
                        'user_id' => $peserta->id,
                        'tanggal' => $targetDate,
                        'jam_masuk' => null,
                        'jam_pulang' => null,
                        'status' => 'ALPHA',
                        'status_harian' => 'ALPHA',
                        'catatan_sistem' => 'Auto-closed: Tidak melakukan absensi sama sekali. Diproses tanggal ' . TimeService::now()->format('d/m/Y H:i:s'),
                        'created_at' => TimeService::now(),
                        'updated_at' => TimeService::now(),
                    ]);
                    $created++;
                }
                }
                $this->info("✅ Berhasil membuat {$created} record ALPHA untuk yang tidak absen masuk.");
            } else {
                $this->comment("🧪 DRY RUN: Akan membuat " . count($noAttendanceList) . " record ALPHA.");
            }
        }

        $this->newLine();

        // ========================================================================
        // KASUS 2: SUDAH ABSEN MASUK TAPI LUPA ABSEN PULANG → UPDATE KE ALPHA
        // ========================================================================
        $this->comment("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->comment("📋 KASUS 2: Peserta yang absen masuk tapi lupa absen pulang");
        $this->comment("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");

        // Ambil data yang belum selesai
        $unfinishedAbsensiRaw = Absensi::with('user')
            ->whereDate('tanggal', $targetDate)
            ->where('status_harian', 'BELUM_FINAL')
            ->whereNotNull('jam_masuk')  // Sudah absen masuk
            ->whereNull('jam_pulang')    // Tapi belum absen pulang
            ->get();

        // Filter: Hanya yang TIDAK memiliki izin approved
        $unfinishedAttendances = $unfinishedAbsensiRaw->filter(function($absensi) use ($targetDate) {
            $izin = Izin::where('user_id', $absensi->user_id)
                ->whereIn('status_approval', ['approved_hr', 'auto_approved'])
                ->where(function($query) use ($targetDate) {
                    $query->whereDate('tanggal', $targetDate)
                        ->orWhere(function($q) use ($targetDate) {
                            $q->whereDate('tanggal_mulai', '<=', $targetDate)
                              ->whereDate('tanggal_selesai', '>=', $targetDate);
                        });
                })
                ->exists();
            return !$izin; // Hanya kembalikan yang TIDAK punya izin
        });

        if ($unfinishedAttendances->isEmpty()) {
            $this->info('✅ Tidak ada absensi yang lupa pulang.');
        } else {
            $this->warn("⚠️  Ditemukan {$unfinishedAttendances->count()} absensi yang belum selesai:");
            $this->newLine();

            $tableData = [];
            foreach ($unfinishedAttendances as $absensi) {
                $tableData[] = [
                    'ID' => $absensi->id,
                    'Nama' => $absensi->user->name,
                    'Email' => $absensi->user->email,
                    'Tanggal' => $absensi->tanggal->format('d/m/Y'),
                    'Jam Masuk' => $absensi->jam_masuk,
                    'Status Masuk' => $absensi->status_masuk,
                ];
            }

            $this->table(
                ['ID', 'Nama', 'Email', 'Tanggal', 'Jam Masuk', 'Status Masuk'],
                $tableData
            );

            if (!$isDryRun) {
                $updated = 0;
                foreach ($unfinishedAttendances as $absensi) {
                    $absensi->update([
                        'status' => 'ALPHA',
                        'status_harian' => 'ALPHA',
                        'catatan_sistem' => 'Auto-closed: Tidak melakukan absen pulang. Diproses tanggal ' . TimeService::now()->format('d/m/Y H:i:s'),
                    ]);
                    $updated++;
                }
                $this->info("✅ Berhasil mengubah {$updated} absensi menjadi ALPHA.");
            } else {
                $this->comment("🧪 DRY RUN: Akan mengubah {$unfinishedAttendances->count()} record ke ALPHA.");
            }
        }

        $this->newLine();

        // ========================================================================
        // SUMMARY
        // ========================================================================
        if ($isDryRun) {
            $this->newLine();
            $this->comment('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
            $this->comment('🧪 DRY RUN MODE - Data tidak akan diubah.');
            $this->comment('Jalankan tanpa --dry-run untuk mengubah database.');
            $this->comment('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        } else {
            $this->newLine();
            $this->comment("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
            $this->info("✅ SELESAI! Auto-close untuk tanggal {$targetDate->format('d F Y')} berhasil dijalankan.");
            $this->comment("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
            $this->newLine();
            $this->comment("📝 Ringkasan:");
            $this->comment("   - Tanggal diproses: {$targetDate->format('d F Y')}");
            $this->comment("   - Tidak absen masuk: " . count($noAttendanceList) . " record dibuat");
            $this->comment("   - Lupa absen pulang: {$unfinishedAttendances->count()} record diupdate");
            $this->comment("   - Total ALPHA: " . (count($noAttendanceList) + $unfinishedAttendances->count()));
        }

        return Command::SUCCESS;
    }
}
