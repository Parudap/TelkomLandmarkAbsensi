<?php

namespace App\Services;

use App\Models\Izin;
use App\Models\Absensi;
use App\Services\TimeService;
use Carbon\Carbon;

class IzinAutoApproveService
{
    /**
     * Auto-approve izin yang sudah lebih dari 24 jam
     * Dipanggil otomatis saat halaman tertentu dibuka
     * 
     * @return int Jumlah izin yang di-auto approve
     */
    public static function processAutoApproval(): int
    {
        $now = TimeService::now();
        $cutoffTime = $now->copy()->subHours(24);

        // Ambil semua izin yang masih pending dan sudah lebih dari 24 jam
        $pendingIzin = Izin::where('status_approval', 'pending')
            ->where('created_at', '<=', $cutoffTime)
            ->get();

        if ($pendingIzin->isEmpty()) {
            return 0;
        }

        $count = 0;
        foreach ($pendingIzin as $izin) {
            // Auto approve
            $izin->status_approval = 'approved_hr';
            $izin->auto_approved_at = $now;
            $izin->approved_by_hr = null; // Tidak ada user yang approve (otomatis)
            $izin->approved_at_hr = $now;
            $izin->save();

            // Backfill absensi jika perlu
            self::backfillAbsensi($izin);

            $count++;
        }

        return $count;
    }

    /**
     * Backfill absensi dari ALPHA menjadi IZIN jika izin disetujui
     */
    private static function backfillAbsensi(Izin $izin)
    {
        if ($izin->jenis_izin == 'pulang_cepat') {
            // Untuk pulang cepat, cek absensi di tanggal
            $tanggal = $izin->tanggal ?: $izin->tanggal_mulai;
            $absensi = Absensi::where('user_id', $izin->user_id)
                ->whereDate('tanggal', $tanggal)
                ->first();

            if ($absensi) {
                // Jika sudah absen masuk tapi belum pulang - auto tutup
                if ($absensi->jam_masuk && !$absensi->jam_pulang) {
                    $jamPulangIzin = Carbon::parse($tanggal->format('Y-m-d') . ' ' . $izin->jam_pulang_diajukan);
                    $jamMasukCarbon = Carbon::parse($absensi->tanggal->format('Y-m-d') . ' ' . $absensi->jam_masuk);
                    $durasiMenit = $jamMasukCarbon->diffInMinutes($jamPulangIzin);

                    $absensi->jam_pulang = $jamPulangIzin;
                    $absensi->status_harian = 'IZIN_PULANG_CEPAT';
                    $absensi->status = 'IZIN_PULANG_CEPAT';
                    $absensi->durasi_kerja = $durasiMenit;
                    $absensi->izin_id = $izin->id;
                    $absensi->catatan_sistem = 'Izin pulang cepat disetujui otomatis pada jam ' . $izin->jam_pulang_diajukan;
                    $absensi->save();
                }
                // Jika status ALPHA (sudah di-autoclose sebelumnya) - backfill to IZIN_PULANG_CEPAT
                elseif ($absensi->status_harian == 'ALPHA') {
                    $absensi->status_harian = 'IZIN_PULANG_CEPAT';
                    $absensi->status = 'IZIN_PULANG_CEPAT';
                    $absensi->izin_id = $izin->id;
                    $absensi->catatan_sistem = 'Izin pulang cepat disetujui otomatis (backfilled dari ALPHA)';
                    $absensi->save();
                }
            }
        } else {
            // Untuk izin tidak masuk (sakit/izin), backfill semua tanggal dalam range
            $tanggalMulai = Carbon::parse($izin->tanggal_mulai);
            $tanggalSelesai = Carbon::parse($izin->tanggal_selesai);

            while ($tanggalMulai->lte($tanggalSelesai)) {
                if (!$tanggalMulai->isWeekend()) {
                    $absensi = Absensi::where('user_id', $izin->user_id)
                        ->whereDate('tanggal', $tanggalMulai)
                        ->first();

                    if ($absensi) {
                        // Jika sudah absen masuk tapi belum pulang - tutup dengan status sesuai jenis izin
                        if ($absensi->jam_masuk && !$absensi->jam_pulang) {
                            $jamPulangStandar = Carbon::parse($tanggalMulai->format('Y-m-d') . ' 17:00:00');
                            $jamMasukCarbon = Carbon::parse($absensi->tanggal->format('Y-m-d') . ' ' . $absensi->jam_masuk);
                            $durasiMenit = $jamMasukCarbon->diffInMinutes($jamPulangStandar);

                            $statusHarian = $izin->jenis_izin == 'tidak_masuk' ? 'IZIN_TIDAK_MASUK' : 'IZIN_PULANG_CEPAT';
                            $jenisIzinText = $izin->jenis_izin == 'tidak_masuk' ? 'Izin tidak masuk' : 'Izin pulang cepat';
                            $absensi->jam_pulang = $jamPulangStandar;
                            $absensi->status = $statusHarian;
                            $absensi->status_harian = $statusHarian;
                            $absensi->durasi_kerja = $durasiMenit;
                            $absensi->izin_id = $izin->id;
                            $absensi->catatan_sistem = $jenisIzinText;
                            $absensi->save();
                        }
                        // Jika status ALPHA atau belum final
                        elseif (!in_array($absensi->status_harian, ['HADIR_TEPAT_WAKTU', 'HADIR_TELAT'])) {
                            $statusHarian = $izin->jenis_izin == 'tidak_masuk' ? 'IZIN_TIDAK_MASUK' : 'IZIN_PULANG_CEPAT';
                            $jenisIzinText = $izin->jenis_izin == 'tidak_masuk' ? 'Izin tidak masuk' : 'Izin pulang cepat';
                            $absensi->status = $statusHarian;
                            $absensi->status_harian = $statusHarian;
                            $absensi->izin_id = $izin->id;
                            $absensi->catatan_sistem = $jenisIzinText;
                            $absensi->save();
                        }
                    } else {
                        // Jika belum ada record absensi, buat baru dengan status sesuai jenis izin
                        $statusHarian = $izin->jenis_izin == 'tidak_masuk' ? 'IZIN_TIDAK_MASUK' : 'IZIN_PULANG_CEPAT';
                        $jenisIzinText = $izin->jenis_izin == 'tidak_masuk' ? 'Izin tidak masuk' : 'Izin pulang cepat';
                        Absensi::create([
                            'user_id' => $izin->user_id,
                            'tanggal' => $tanggalMulai,
                            'status' => $statusHarian,
                            'status_harian' => $statusHarian,
                            'izin_id' => $izin->id,
                            'catatan_sistem' => $jenisIzinText,
                        ]);
                    }
                }
                $tanggalMulai->addDay();
            }
        }
    }
}
