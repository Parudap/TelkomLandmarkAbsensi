<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Izin;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Services\TimeService;

class DashboardController extends Controller
{
    /**
     * Get current time (sync dengan AbsensiController)
     */
    private function getCurrentTime()
    {
        // Centralized time provider (supports .env testing mode and cache override)
        return TimeService::now();
    }
    
    public function index()
    {
        Carbon::setLocale('id'); // Set locale Indonesia
        $user = Auth::user();
        $now = $this->getCurrentTime();
        // Panggil auto-close skipped dates agar tanggal yang terlewat otomatis ALPHA
        (new \App\Http\Controllers\Peserta\AbsensiController)->autoCloseSkippedDates($now);
        $today = $now->copy()->startOfDay();
        $thisMonth = $now->month;
        $thisYear = $now->year;

        // Absensi hari ini
        $absensiToday = Absensi::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->first();

        // ========================================================
        // PERIODE MAGANG & PROGRESS
        // ========================================================
        $periodeMulai = Carbon::parse($user->periode_magang_mulai);
        $periodeSelesai = Carbon::parse($user->periode_magang_selesai);
        
        // Hitung total hari kerja (Senin-Jumat) dalam periode magang
        $totalHariKerja = 0;
        $currentDate = $periodeMulai->copy();
        while ($currentDate->lte($periodeSelesai)) {
            if ($currentDate->isWeekday()) { // Senin-Jumat
                $totalHariKerja++;
            }
            $currentDate->addDay();
        }
        
        // Hitung hari yang sudah berlalu (hanya hari kerja SEBELUM hari ini)
        $hariKerjaBerlalu = 0;
        $currentDate = $periodeMulai->copy();
        $yesterday = $today->copy()->subDay(); // Hari sebelum hari ini
        
        while ($currentDate->lte($yesterday) && $currentDate->lte($periodeSelesai)) {
            if ($currentDate->isWeekday()) {
                $hariKerjaBerlalu++;
            }
            $currentDate->addDay();
        }
        
        // Hitung sisa hari kerja
        $sisaHariKerja = max(0, $totalHariKerja - $hariKerjaBerlalu);
        
        // Persentase progress BERDASARKAN ABSENSI LENGKAP (masuk + pulang)
        // Hitung berapa hari yang sudah absen lengkap
        $hariSudahAbsen = Absensi::where('user_id', $user->id)
            ->whereBetween('tanggal', [$periodeMulai, $periodeSelesai])
            ->whereNotNull('jam_masuk')
            ->whereNotNull('jam_pulang')
            ->whereIn('status_harian', ['HADIR_TEPAT_WAKTU', 'HADIR_TELAT'])
            ->count();
        
        $progressPersen = $totalHariKerja > 0 ? round(($hariSudahAbsen / $totalHariKerja) * 100, 1) : 0;

        // Statistik keseluruhan periode magang
        $stats = [
            'total_hadir' => Absensi::where('user_id', $user->id)
                ->whereBetween('tanggal', [$periodeMulai, $periodeSelesai])
                ->whereIn('status_harian', ['HADIR_TEPAT_WAKTU', 'HADIR_TELAT'])
                ->count(),
            
            'total_tepat_waktu' => Absensi::where('user_id', $user->id)
                ->whereBetween('tanggal', [$periodeMulai, $periodeSelesai])
                ->where('status_harian', 'HADIR_TEPAT_WAKTU')
                ->count(),
            
            'total_telat' => Absensi::where('user_id', $user->id)
                ->whereBetween('tanggal', [$periodeMulai, $periodeSelesai])
                ->where('status_harian', 'HADIR_TELAT')
                ->count(),
            
            'total_izin' => Izin::where('user_id', $user->id)
                ->whereBetween('tanggal_mulai', [$periodeMulai, $periodeSelesai])
                ->whereIn('status_approval', ['approved_hr', 'auto_approved'])
                ->count(),
            
            'total_alpha' => Absensi::where('user_id', $user->id)
                ->whereBetween('tanggal', [$periodeMulai, $periodeSelesai])
                ->where('status_harian', 'ALPHA')
                ->count(),
            
            'izin_pending' => Izin::where('user_id', $user->id)
                ->where('status_approval', 'pending')
                ->count(),
        ];

        // Riwayat absensi 7 hari terakhir (tampilkan ALPHA hanya jika hari sudah lewat jam 17:00)
        $riwayatAbsensi = collect();
        $adaAbsensi = Absensi::where('user_id', $user->id)->exists();
        if ($adaAbsensi) {
            $startDate = $today->copy()->subDays(7);
            if ($startDate->lt($periodeMulai)) {
                $startDate = $periodeMulai->copy();
            }
            $now = \App\Services\TimeService::now();
            for ($date = $today->copy(); $date->gte($startDate); $date->subDay()) {
                if ($date->lt($periodeMulai)) {
                    break;
                }
                $absensi = Absensi::where('user_id', $user->id)
                    ->whereDate('tanggal', $date)
                    ->first();
                if ($absensi) {
                    $riwayatAbsensi->push($absensi);
                } else {
                    $dummy = new \App\Models\Absensi();
                    $dummy->tanggal = $date->copy();
                    $dummy->jam_masuk = null;
                    $dummy->jam_pulang = null;
                    if ($date->isWeekend()) {
                        $dummy->status_harian = 'LIBUR';
                    } else {
                        // Hanya tampilkan ALPHA jika hari sudah lewat jam 17:00
                        $jam17 = $date->copy()->setTime(17,0,0);
                        if ($now->greaterThan($jam17)) {
                            $dummy->status_harian = 'ALPHA';
                        } else {
                            $dummy->status_harian = '-'; // Belum final, tidak tampilkan ALPHA
                        }
                    }
                    $riwayatAbsensi->push($dummy);
                }
            }
            $riwayatAbsensi = $riwayatAbsensi->sortByDesc('tanggal')->values();
        }

        // Izin pending approval
        $izinPending = Izin::where('user_id', $user->id)
            ->where('status_approval', 'pending')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        // Cek izin pulang cepat yang disetujui hari ini
        $izinPulangCepatToday = Izin::where('user_id', $user->id)
            ->where('jenis_izin', 'pulang_cepat')
            ->whereDate('tanggal_mulai', $today)
            ->where('status_approval', 'approved_hr')
            ->first();

        // Cek apakah ada izin TIDAK MASUK yang disetujui untuk hari ini (EXCLUDE pulang_cepat)
        $hasIzinToday = Izin::where('user_id', $user->id)
            ->whereIn('status_approval', ['approved_hr', 'auto_approved'])
            ->where('jenis_izin', '!=', 'pulang_cepat')
            ->where(function($q) use ($today) {
                $q->whereDate('tanggal', $today)
                  ->orWhere(function($sub) use ($today) {
                      $sub->whereDate('tanggal_mulai', '<=', $today)
                          ->whereDate('tanggal_selesai', '>=', $today);
                  });
            })
            ->first();

        return view('peserta.dashboard', compact(
            'user',
            'absensiToday',
            'stats',
            'riwayatAbsensi',
            'izinPending',
            'izinPulangCepatToday',
            'hasIzinToday'
        ))->with([
            'currentDate' => $now->translatedFormat('l, d F Y'),
            'currentTime' => $now->format('H:i'),
            'periodeMulai' => $periodeMulai,
            'periodeSelesai' => $periodeSelesai,
            'totalHariKerja' => $totalHariKerja,
            'hariKerjaBerlalu' => $hariKerjaBerlalu,
            'sisaHariKerja' => $sisaHariKerja,
            'progressPersen' => $progressPersen
        ]);
    }
}
