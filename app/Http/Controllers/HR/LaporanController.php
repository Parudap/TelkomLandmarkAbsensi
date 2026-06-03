<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Absensi;
use App\Models\Izin;
use App\Models\Bidang;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Services\TimeService;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    // Admin/HR: Update absensi status (except LIBUR)
    public function updateAbsensiStatus(Request $request, $absensiId)
    {
        $request->validate([
            'status_harian' => 'required|in:HADIR_TEPAT_WAKTU,HADIR_TELAT,ALPHA,IZIN_TIDAK_MASUK,IZIN_PULANG_CEPAT,STATUS_AWAL',
            'user_id' => 'nullable|exists:users,id',
            'tanggal' => 'nullable|date',
        ]);

        $absensi = \App\Models\Absensi::with(['izin', 'user'])->find($absensiId);

        // Jika baris kosong (dummy) di tabel admin diubah, buat record absensi terlebih dahulu.
        if (!$absensi) {
            if (!$request->filled('user_id') || !$request->filled('tanggal')) {
                return back()->with('error', 'Data absensi tidak valid untuk diubah.');
            }

            $absensi = \App\Models\Absensi::create([
                'user_id' => $request->user_id,
                'tanggal' => $request->tanggal,
                // status mengikuti default DB, status_harian akan di-set di bawah.
                'created_at' => TimeService::now(),
                'updated_at' => TimeService::now(),
            ]);

            $absensi->load(['izin', 'user']);
        }

        if ($absensi->status_harian === 'LIBUR') {
            return back()->with('error', 'Status LIBUR tidak dapat diubah.');
        }

        if ($request->status_harian === 'STATUS_AWAL') {
            $tanggalAbsensi = Carbon::parse($absensi->tanggal)->startOfDay();
            if (!$tanggalAbsensi->gt(TimeService::today())) {
                return back()->with('error', 'Status Awal hanya tersedia untuk tanggal mendatang.');
            }

            // Status awal = hasil absensi manual peserta, bukan status override izin.
            if ($absensi->jam_masuk && $absensi->jam_pulang) {
                $statusMasuk = $absensi->status_masuk;
                if (!$statusMasuk && $absensi->jam_masuk) {
                    $statusMasuk = Carbon::parse($absensi->jam_masuk)->format('H:i:s') <= '08:00:00'
                        ? 'TEPAT_WAKTU'
                        : 'TELAT';
                }

                $absensi->status_harian = $statusMasuk === 'TELAT'
                    ? 'HADIR_TELAT'
                    : 'HADIR_TEPAT_WAKTU';
            } elseif ($absensi->jam_masuk && !$absensi->jam_pulang) {
                $absensi->status_harian = 'BELUM_FINAL';
            } else {
                $today = TimeService::today();
                $now = TimeService::now();

                $startMagang = $absensi->user?->periode_magang_mulai
                    ? Carbon::parse($absensi->user->periode_magang_mulai)->startOfDay()
                    : null;
                $endMagang = $absensi->user?->periode_magang_selesai
                    ? Carbon::parse($absensi->user->periode_magang_selesai)->startOfDay()
                    : null;

                if ($tanggalAbsensi->isWeekend()) {
                    $absensi->status_harian = 'LIBUR';
                } elseif ($startMagang && $tanggalAbsensi->lt($startMagang)) {
                    $absensi->status_harian = 'BELUM_FINAL';
                } elseif ($endMagang && $tanggalAbsensi->gt($endMagang)) {
                    $absensi->status_harian = 'BELUM_FINAL';
                } elseif ($tanggalAbsensi->gt($today)) {
                    $absensi->status_harian = 'BELUM_FINAL';
                } elseif ($tanggalAbsensi->isSameDay($today)) {
                    $cutoff = $tanggalAbsensi->copy()->setTime(17, 0, 0);
                    $absensi->status_harian = $now->gt($cutoff) ? 'ALPHA' : 'BELUM_FINAL';
                } else {
                    $absensi->status_harian = 'ALPHA';
                }
            }

            // Lepas link izin agar status manual tidak kembali terikat izin.
            $absensi->izin_id = null;
            if ($absensi->catatan_sistem && str_contains(strtolower($absensi->catatan_sistem), 'izin')) {
                $absensi->catatan_sistem = null;
            }
        } else {
            $absensi->status_harian = $request->status_harian;
        }

        $absensi->save();

        return back()->with('success', 'Status absensi berhasil diubah.');
    }

    // Admin/HR: Update status aktif peserta
    public function updateStatusPeserta(Request $request, $pesertaId)
    {
        $request->validate([
            'is_active' => 'required|in:0,1',
        ]);

        $peserta = User::where('role', 'peserta_magang')->findOrFail($pesertaId);
        $peserta->is_active = $request->is_active;
        $peserta->save();

        $statusText = $request->is_active ? 'Aktif' : 'Tidak Aktif';
        return back()->with('success', "Status peserta berhasil diubah menjadi {$statusText}.");
    }

    // Admin/HR: Reset password peserta
    public function resetPassword(Request $request, $pesertaId)
    {
        $request->validate([
            'new_password' => 'required|min:6',
        ]);

        $peserta = User::where('role', 'peserta_magang')->findOrFail($pesertaId);
        $peserta->password = bcrypt($request->new_password);
        $peserta->save();

        return back()->with('success', "Password peserta {$peserta->name} berhasil direset.");
    }

    public function index()
    {
        return view('hr.laporan.index');
    }

    public function absensi(Request $request)
    {
        // Default kosong: saat pertama dibuka tampilkan semua tanggal
        $tanggalMulai = $request->tanggal_mulai;
        $tanggalSelesai = $request->tanggal_selesai;
        
        $request->validate([
            'bidang_id' => 'nullable|exists:bidang,id',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
        ]);


        $bidangId = $request->bidang_id;
        $statusHarian = $request->status_harian;

        // Query absensi
        $query = Absensi::with(['user.bidang']);

        if ($tanggalMulai) {
            $query->whereDate('tanggal', '>=', $tanggalMulai);
        }

        if ($tanggalSelesai) {
            $query->whereDate('tanggal', '<=', $tanggalSelesai);
        }

        if ($bidangId) {
            $query->whereHas('user', function($q) use ($bidangId) {
                $q->where('bidang_id', $bidangId);
            });
        }
        if ($statusHarian) {
            $query->where('status_harian', $statusHarian);
        }

        $absensiData = $query->orderBy('tanggal', 'desc')->get();

        // Statistik
        $stats = [
            'total_absensi' => $absensiData->count(),
            'tepat_waktu' => $absensiData->where('status_harian', 'HADIR_TEPAT_WAKTU')->count(),
            'terlambat' => $absensiData->where('status_harian', 'HADIR_TELAT')->count(),
            'alpha' => $absensiData->where('status_harian', 'ALPHA')->count(),
            'izin_tidak_masuk' => $absensiData->where('status_harian', 'IZIN_TIDAK_MASUK')->count(),
            'izin_pulang_cepat' => $absensiData->where('status_harian', 'IZIN_PULANG_CEPAT')->count(),
        ];

        $bidangList = Bidang::orderBy('nama_bidang')->get();

        return view('hr.laporan.absensi', compact('absensiData', 'stats', 'bidangList', 'bidangId', 'tanggalMulai', 'tanggalSelesai', 'statusHarian'));
    }

    public function peserta()
    {
        $bidangId = request('bidang_id');
        $isActive = request('is_active');
        $searchName = request('search_name');

        $query = User::where('role', 'peserta_magang')
            ->where('status_approval', 'approved')
            ->with(['bidang']);

        if ($bidangId) {
            $query->where('bidang_id', $bidangId);
        }
        if ($isActive !== null && $isActive !== '') {
            $query->where('is_active', $isActive);
        }
           if ($searchName) {
               $query->where(function($q) use ($searchName) {
                   $q->where('name', 'like', '%' . $searchName . '%')
                     ->orWhere('email', 'like', '%' . $searchName . '%');
               });
           }

        $pesertaList = $query->orderBy('name')->get();
        $bidangList = Bidang::orderBy('nama_bidang')->get();

        return view('hr.laporan.peserta', compact('pesertaList', 'bidangList', 'bidangId', 'isActive', 'searchName'));
    }

    public function exportPeserta()
    {
        $bidangId = request('bidang_id');
        $isActive = request('is_active');
        $searchName = request('search_name');

        $query = User::where('role', 'peserta_magang')
            ->where('status_approval', 'approved')
            ->with(['bidang']);

        if ($bidangId) {
            $query->where('bidang_id', $bidangId);
        }
        if ($isActive !== null && $isActive !== '') {
            $query->where('is_active', $isActive);
        }
           if ($searchName) {
               $query->where(function($q) use ($searchName) {
                   $q->where('name', 'like', '%' . $searchName . '%')
                     ->orWhere('email', 'like', '%' . $searchName . '%');
               });
           }

        $pesertaList = $query->orderBy('name')->get();
        
        // Get filter info for header
        $filterBidang = $bidangId ? Bidang::find($bidangId)->nama_bidang : 'Semua Bidang';
        $filterStatus = $isActive === '1' ? 'Aktif' : ($isActive === '0' ? 'Tidak Aktif' : 'Semua Status');

        // Generate PDF
        $pdf = Pdf::loadView('hr.laporan.export-peserta', compact('pesertaList', 'filterBidang', 'filterStatus'))
            ->setPaper('a4', 'portrait');
        
        $filename = 'Data_Peserta_Magang_' . date('Ymd_His') . '.pdf';
        return $pdf->download($filename);
    }

    public function exportPesertaCSV()
    {
        $bidangId = request('bidang_id');
        $isActive = request('is_active');
        $searchName = request('search_name');

        $query = User::where('role', 'peserta_magang')
            ->where('status_approval', 'approved')
            ->with(['bidang']);

        if ($bidangId) {
            $query->where('bidang_id', $bidangId);
        }
        if ($isActive !== null && $isActive !== '') {
            $query->where('is_active', $isActive);
        }
           if ($searchName) {
               $query->where(function($q) use ($searchName) {
                   $q->where('name', 'like', '%' . $searchName . '%')
                     ->orWhere('email', 'like', '%' . $searchName . '%');
               });
           }

        $pesertaList = $query->orderBy('name')->get();
        
        // Get filter info
        $filterBidang = $bidangId ? Bidang::find($bidangId)->nama_bidang : 'Semua Bidang';
        $filterStatus = $isActive === '1' ? 'Aktif' : ($isActive === '0' ? 'Tidak Aktif' : 'Semua Status');

        $filename = 'data_peserta_magang_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($pesertaList, $filterBidang, $filterStatus) {
            $file = fopen('php://output', 'w');
            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            // Specify separator for Excel
            fwrite($file, "sep=;\n");
            
            // Header info
            fputcsv($file, ['Data Peserta Magang'], ';');
            fputcsv($file, ['Tanggal Cetak', "\t" . date('d/m/Y H:i')], ';');
            fputcsv($file, ['Filter Bidang', $filterBidang], ';');
            fputcsv($file, ['Filter Status', $filterStatus], ';');
            fputcsv($file, [], ';'); // Empty row
            
            // Header columns
            fputcsv($file, [
                'No',
                'Nama',
                'Email',
                'No. Telepon',
                'Bidang',
                'Periode Magang Mulai',
                'Periode Magang Selesai',
                'Status'
            ], ';');
            
            // Data
            $no = 1;
            foreach ($pesertaList as $peserta) {
                // Tambahkan tab prefix untuk tanggal agar Excel treat sebagai text
                $periodeMulai = $peserta->periode_magang_mulai ? "\t" . Carbon::parse($peserta->periode_magang_mulai)->format('d/m/Y') : '-';
                $periodeSelesai = $peserta->periode_magang_selesai ? "\t" . Carbon::parse($peserta->periode_magang_selesai)->format('d/m/Y') : '-';
                $status = $peserta->is_active ? 'Aktif' : 'Tidak Aktif';
                
                $fields = [
                    $no++,
                    $peserta->name ?? '',
                    $peserta->email ?? '',
                    $peserta->no_telepon ?? '-',
                    $peserta->bidang->nama_bidang ?? '-',
                    $periodeMulai,
                    $periodeSelesai,
                    $status
                ];
                
                // Sanitize all fields except dates (index 5 and 6)
                $fields = array_map(function($v, $index) {
                    if ($index === 5 || $index === 6) return $v; // Keep date with tab prefix
                    $v = str_replace(["\r", "\n", "\t"], ' ', $v);
                    return trim($v);
                }, $fields, array_keys($fields));
                
                fputcsv($file, $fields, ';');
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function detailPeserta($id)
    {
        $peserta = User::where('role', 'peserta_magang')
            ->with(['bidang'])
            ->findOrFail($id);

        // Tanggal filter tampil kosong saat awal buka halaman.
        $filterTanggalMulai = request('tanggal_mulai');
        $filterTanggalSelesai = request('tanggal_selesai');

        // Untuk data, tetap gunakan rentang efektif agar semua data periode tampil saat filter kosong.
        [$tanggalMulaiEfektif, $tanggalSelesaiEfektif] = $this->resolveDateRangeForPeserta(
            $peserta,
            $filterTanggalMulai,
            $filterTanggalSelesai
        );

        $absensiList = $this->getMergedAbsensiData($peserta, $tanggalMulaiEfektif, $tanggalSelesaiEfektif)->sortByDesc('tanggal');

        $statusHarian = request('status_harian');
        if ($statusHarian) {
            $absensiList = $absensiList->where('status_harian', $statusHarian)->values();
        }

        $stats = [
            'total_hari' => $absensiList->filter(function($a) { return $a->status_harian != '-' && $a->status_harian != 'LIBUR'; })->count(),
            'hadir_tepat' => $absensiList->where('status_harian', 'HADIR_TEPAT_WAKTU')->count(),
            'hadir_telat' => $absensiList->where('status_harian', 'HADIR_TELAT')->count(),
            'alpha' => $absensiList->where('status_harian', 'ALPHA')->count(),
            'izin_tidak_masuk' => $absensiList->where('status_harian', 'IZIN_TIDAK_MASUK')->count(),
            'izin_pulang_cepat' => $absensiList->where('status_harian', 'IZIN_PULANG_CEPAT')->count(),
        ];

        $tanggalMulai = $filterTanggalMulai;
        $tanggalSelesai = $filterTanggalSelesai;

        return view('hr.laporan.detail-peserta', compact('peserta', 'absensiList', 'stats', 'tanggalMulai', 'tanggalSelesai', 'statusHarian'));
    }

    public function exportDetailPeserta($id)
    {
        $peserta = User::where('role', 'peserta_magang')
            ->with(['bidang'])
            ->findOrFail($id);

        $tanggalMulai = request('tanggal_mulai');
        $tanggalSelesai = request('tanggal_selesai');

        [$tanggalMulaiEfektif, $tanggalSelesaiEfektif] = $this->resolveDateRangeForPeserta(
            $peserta,
            $tanggalMulai,
            $tanggalSelesai
        );

        $absensiList = $this->getMergedAbsensiData($peserta, $tanggalMulaiEfektif, $tanggalSelesaiEfektif)->sortBy('tanggal');

        $statusHarian = request('status_harian');
        if ($statusHarian) {
            $absensiList = $absensiList->where('status_harian', $statusHarian)->values();
        }

        $stats = [
            'total_hari' => $absensiList->filter(function($a) { return $a->status_harian != '-' && $a->status_harian != 'LIBUR'; })->count(),
            'hadir_tepat' => $absensiList->where('status_harian', 'HADIR_TEPAT_WAKTU')->count(),
            'hadir_telat' => $absensiList->where('status_harian', 'HADIR_TELAT')->count(),
            'alpha' => $absensiList->where('status_harian', 'ALPHA')->count(),
            'izin_tidak_masuk' => $absensiList->where('status_harian', 'IZIN_TIDAK_MASUK')->count(),
            'izin_pulang_cepat' => $absensiList->where('status_harian', 'IZIN_PULANG_CEPAT')->count(),
        ];

        // Generate PDF menggunakan DomPDF
        $tanggalMulai = $tanggalMulai ?: $tanggalMulaiEfektif;
        $tanggalSelesai = $tanggalSelesai ?: $tanggalSelesaiEfektif;

        $pdf = Pdf::loadView('hr.laporan.export-detail-peserta', compact('peserta', 'absensiList', 'stats', 'tanggalMulai', 'tanggalSelesai'))
            ->setPaper('a4', 'landscape');
        
        // Download PDF dengan nama file yang descriptive
        $filename = 'Detail_Absensi_' . str_replace(' ', '_', $peserta->name) . '_' . date('Ymd_His') . '.pdf';
        return $pdf->download($filename);
    }

    public function exportDetailPesertaCSV($id)
    {
        $peserta = User::where('role', 'peserta_magang')
            ->with(['bidang'])
            ->findOrFail($id);

        $tanggalMulai = request('tanggal_mulai');
        $tanggalSelesai = request('tanggal_selesai');

        [$tanggalMulaiEfektif, $tanggalSelesaiEfektif] = $this->resolveDateRangeForPeserta(
            $peserta,
            $tanggalMulai,
            $tanggalSelesai
        );

        $absensiList = $this->getMergedAbsensiData($peserta, $tanggalMulaiEfektif, $tanggalSelesaiEfektif)->sortBy('tanggal');

        $statusHarian = request('status_harian');
        if ($statusHarian) {
            $absensiList = $absensiList->where('status_harian', $statusHarian)->values();
        }

        $filename = 'detail_absensi_' . str_replace(' ', '_', $peserta->name) . '_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($absensiList, $peserta) {
            $file = fopen('php://output', 'w');
            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            // Specify separator for Excel
            fwrite($file, "sep=;\n");
            // Info Peserta
            fputcsv($file, ['Detail Absensi Peserta'], ';');
            fputcsv($file, ['Nama', $peserta->name], ';');
            fputcsv($file, ['Email', $peserta->email], ';');
            fputcsv($file, ['Bidang', $peserta->bidang->nama_bidang ?? ''], ';');
            fputcsv($file, [], ';'); // Empty row
            
            // Header
            fputcsv($file, [
                'No',
                'Tanggal',
                'Hari',
                'Jam Masuk',
                'Jam Pulang',
                'Durasi',
                'Status'
            ], ';');
            
            // Data
            $no = 1;
            foreach ($absensiList as $absensi) {
                $durasi = '';
                if ($absensi->jam_masuk && $absensi->jam_pulang) {
                    $masuk = Carbon::parse($absensi->jam_masuk);
                    $pulang = Carbon::parse($absensi->jam_pulang);
                    $diff = $masuk->diff($pulang);
                    $durasi = $diff->h . 'j ' . $diff->i . 'm';
                }
                
                $status = match($absensi->status_harian) {
                    'HADIR_TEPAT_WAKTU' => 'Tepat Waktu',
                    'HADIR_TELAT' => 'Terlambat',
                    'ALPHA' => 'Alpha',
                    'IZIN' => 'Izin',
                    'LIBUR' => 'Libur',
                    '-' => '-',
                    default => $absensi->status_harian ?? ''
                };
                
                $hariInggris = Carbon::parse($absensi->tanggal)->format('l');
                $hariIndonesia = match($hariInggris) {
                    'Monday' => 'Senin',
                    'Tuesday' => 'Selasa',
                    'Wednesday' => 'Rabu',
                    'Thursday' => 'Kamis',
                    'Friday' => 'Jumat',
                    'Saturday' => 'Sabtu',
                    'Sunday' => 'Minggu',
                    default => $hariInggris
                };
                
                // Tambahkan prefix tab untuk tanggal agar Excel treat sebagai text
                $tanggalFormatted = "\t" . Carbon::parse($absensi->tanggal)->format('d/m/Y');
                
                fputcsv($file, [
                    $no++, 
                    $tanggalFormatted, // format dd/mm/yyyy dengan prefix tab
                    $hariIndonesia,
                    $absensi->jam_masuk ? Carbon::parse($absensi->jam_masuk)->format('H:i') : '',
                    $absensi->jam_pulang ? Carbon::parse($absensi->jam_pulang)->format('H:i') : '',
                    $durasi,
                    $status
                ], ';');
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function resolveDateRangeForPeserta($peserta, $tanggalMulai = null, $tanggalSelesai = null)
    {
        $tanggalMulai = $tanggalMulai ?: null;
        $tanggalSelesai = $tanggalSelesai ?: null;

        if ($tanggalMulai && !$tanggalSelesai) {
            $tanggalSelesai = $tanggalMulai;
        }

        if (!$tanggalMulai && $tanggalSelesai) {
            $tanggalMulai = $tanggalSelesai;
        }

        if (!$tanggalMulai && !$tanggalSelesai) {
            $minAbsensi = Absensi::where('user_id', $peserta->id)->min('tanggal');
            $maxAbsensi = Absensi::where('user_id', $peserta->id)->max('tanggal');

            $tanggalMulai = $peserta->periode_magang_mulai
                ? Carbon::parse($peserta->periode_magang_mulai)->format('Y-m-d')
                : ($minAbsensi ? Carbon::parse($minAbsensi)->format('Y-m-d') : TimeService::today()->format('Y-m-d'));

            $tanggalSelesai = $peserta->periode_magang_selesai
                ? Carbon::parse($peserta->periode_magang_selesai)->format('Y-m-d')
                : ($maxAbsensi ? Carbon::parse($maxAbsensi)->format('Y-m-d') : TimeService::today()->format('Y-m-d'));
        }

        if ($tanggalMulai > $tanggalSelesai) {
            [$tanggalMulai, $tanggalSelesai] = [$tanggalSelesai, $tanggalMulai];
        }

        return [$tanggalMulai, $tanggalSelesai];
    }

    private function getMergedAbsensiData($peserta, $tanggalMulai, $tanggalSelesai)
    {
        // Generate all dates
        $period = \Carbon\CarbonPeriod::create($tanggalMulai, $tanggalSelesai);
        $dates = [];
        foreach ($period as $date) {
            $dates[] = $date;
        }

        // Fetch existing Absensi
        $existingAbsensi = Absensi::where('user_id', $peserta->id)
            ->whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai])
            ->get()
            ->keyBy(function($item) {
                return $item->tanggal->format('Y-m-d');
            });
            
        // Fetch approved Izin
        $approvedIzin = Izin::where('user_id', $peserta->id)
            ->where('status_approval', 'approved_hr')
            ->get();
            
        $izinByDate = [];
        foreach ($approvedIzin as $izin) {
            if ($izin->tanggal) {
                $izinByDate[$izin->tanggal->format('Y-m-d')] = $izin;
            } else if ($izin->tanggal_mulai && $izin->tanggal_selesai) {
                $start = $izin->tanggal_mulai->copy();
                $end = $izin->tanggal_selesai->copy();
                while ($start->lte($end)) {
                    $izinByDate[$start->format('Y-m-d')] = $izin;
                    $start->addDay();
                }
            }
        }

        // Jika peserta belum pernah absen sama sekali, return kosong
        if ($existingAbsensi->count() === 0) {
            return collect([]);
        }
        return collect($dates)->map(function($date) use ($existingAbsensi, $izinByDate, $peserta) {
            $dateStr = $date->format('Y-m-d');
            if (isset($existingAbsensi[$dateStr])) {
                return $existingAbsensi[$dateStr];
            }
            $absensi = new Absensi();
            $absensi->user_id = $peserta->id;
            $absensi->tanggal = $date;
            if (isset($izinByDate[$dateStr])) {
                $jenisIzin = $izinByDate[$dateStr]->jenis_izin;
                if ($jenisIzin == 'tidak_masuk') {
                    $absensi->status_harian = 'IZIN_TIDAK_MASUK';
                    $absensi->catatan_sistem = 'Izin tidak masuk';
                } else {
                    $absensi->status_harian = 'IZIN_PULANG_CEPAT';
                    $absensi->catatan_sistem = 'Izin pulang cepat';
                }
            } elseif ($date->isWeekend()) {
                $absensi->status_harian = 'LIBUR';
            } else {
                $startMagang = $peserta->periode_magang_mulai ? Carbon::parse($peserta->periode_magang_mulai) : null;
                $endMagang = $peserta->periode_magang_selesai ? Carbon::parse($peserta->periode_magang_selesai) : null;
                if ($startMagang && $date->lt($startMagang)) {
                     $absensi->status_harian = '-';
                } elseif ($endMagang && $date->gt($endMagang)) {
                     $absensi->status_harian = '-';
                } elseif ($date->gt(TimeService::now())) {
                     $absensi->status_harian = '-';
                } else {
                     $absensi->status_harian = 'ALPHA';
                }
            }
            return $absensi;
        });
    }


    public function exportAbsensi(Request $request)
    {
        $request->validate([
            'bidang_id' => 'nullable|exists:bidang,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date',
        ]);

        $bidangId = $request->bidang_id;
        $tanggalMulai = $request->tanggal_mulai;
        $tanggalSelesai = $request->tanggal_selesai;

        $query = Absensi::with(['user.bidang'])
            ->whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai]);

        if ($bidangId) {
            $query->whereHas('user', function($q) use ($bidangId) {
                $q->where('bidang_id', $bidangId);
            });
        }

        $absensiData = $query->orderBy('tanggal', 'desc')->get();

        $stats = [
            'total_absensi' => $absensiData->count(),
            'tepat_waktu' => $absensiData->where('status_harian', 'HADIR_TEPAT_WAKTU')->count(),
            'terlambat' => $absensiData->where('status_harian', 'HADIR_TELAT')->count(),
            'alpha' => $absensiData->where('status_harian', 'ALPHA')->count(),
            'izin_tidak_masuk' => $absensiData->where('status_harian', 'IZIN_TIDAK_MASUK')->count(),
            'izin_pulang_cepat' => $absensiData->where('status_harian', 'IZIN_PULANG_CEPAT')->count(),
        ];

        $bidang = $bidangId ? Bidang::find($bidangId) : null;

        // Generate PDF menggunakan DomPDF
        $pdf = Pdf::loadView('hr.laporan.export-absensi', compact('absensiData', 'stats', 'tanggalMulai', 'tanggalSelesai', 'bidang'))
            ->setPaper('a4', 'landscape');
        
        // Download PDF dengan nama file yang descriptive
        $filename = 'Laporan_Absensi_' . date('Ymd_His') . '.pdf';
        return $pdf->download($filename);
    }

    public function exportAbsensiCSV(Request $request)
    {
        $request->validate([
            'bidang_id' => 'nullable|exists:bidang,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date',
        ]);


        $bidangId = $request->bidang_id;
        $tanggalMulai = $request->tanggal_mulai;
        $tanggalSelesai = $request->tanggal_selesai;
        $statusHarian = $request->status_harian;

        $query = Absensi::with(['user.bidang'])
            ->whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai]);

        if ($bidangId) {
            $query->whereHas('user', function($q) use ($bidangId) {
                $q->where('bidang_id', $bidangId);
            });
        }
        if ($statusHarian) {
            $query->where('status_harian', $statusHarian);
        }

        $absensiData = $query->orderBy('tanggal', 'asc')->get();

        $filename = 'laporan_absensi_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($absensiData) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Specify separator for Excel
            fwrite($file, "sep=;\n");
            
            // Header - using semicolon delimiter for better Excel compatibility
            fputcsv($file, [
                'No',
                'Tanggal',
                'Hari',
                'Nama Peserta',
                'Email',
                'Bidang',
                'Jam Masuk',
                'Jam Pulang',
                'Durasi',
                'Status'
            ], ';');
            
            // Data
            $no = 1;
            foreach ($absensiData as $absensi) {
                $durasi = '';
                if ($absensi->jam_masuk && $absensi->jam_pulang) {
                    $masuk = Carbon::parse($absensi->jam_masuk);
                    $pulang = Carbon::parse($absensi->jam_pulang);
                    $diff = $masuk->diff($pulang);
                    $durasi = $diff->h . 'j ' . $diff->i . 'm';
                }

                $status = match($absensi->status_harian) {
                    'HADIR_TEPAT_WAKTU' => 'Tepat Waktu',
                    'HADIR_TELAT' => 'Terlambat',
                    'ALPHA' => 'Alpha',
                    default => $absensi->status_harian ?? ''
                };

                // Konversi nama hari ke Bahasa Indonesia
                $hariInggris = Carbon::parse($absensi->tanggal)->format('l');
                $hariIndonesia = match($hariInggris) {
                    'Monday' => 'Senin',
                    'Tuesday' => 'Selasa',
                    'Wednesday' => 'Rabu',
                    'Thursday' => 'Kamis',
                    'Friday' => 'Jumat',
                    'Saturday' => 'Sabtu',
                    'Sunday' => 'Minggu',
                    default => $hariInggris
                };

                // Sanitize all fields: remove newlines, tabs, trim (no forced quotes)
                // Tambahkan prefix tab untuk tanggal agar Excel treat sebagai text
                $tanggalFormatted = "\t" . Carbon::parse($absensi->tanggal)->format('d/m/Y');
                
                $fields = [
                    $no++,
                    $tanggalFormatted, // format dd/mm/yyyy dengan prefix tab agar tidak jadi date Excel
                    $hariIndonesia,
                    $absensi->user->name ?? '',
                    $absensi->user->email ?? '',
                    trim(preg_replace('/\s+/', ' ', $absensi->user->bidang->nama_bidang ?? '')), // bidang: hapus tab/newline
                    $absensi->jam_masuk ? Carbon::parse($absensi->jam_masuk)->format('H:i') : '',
                    $absensi->jam_pulang ? Carbon::parse($absensi->jam_pulang)->format('H:i') : '',
                    $durasi,
                    $status
                ];
                
                // Sanitize semua field kecuali tanggal (index 1)
                $fields = array_map(function($v, $index) {
                    if ($index === 1) return $v; // Keep tanggal dengan tab prefix
                    $v = str_replace(["\r", "\n", "\t"], ' ', $v);
                    return trim($v);
                }, $fields, array_keys($fields));
                fputcsv($file, $fields, ';');
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
