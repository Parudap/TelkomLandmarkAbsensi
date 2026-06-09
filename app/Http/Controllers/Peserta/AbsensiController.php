<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Izin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Services\TimeService;
use Illuminate\Pagination\LengthAwarePaginator;

class AbsensiController extends Controller
{
    const LATITUDE = -7.057658609410659;
    const LONGITUDE = 110.4446595953723;
    const MAX_RADIUS_METERS = 50;
    const JAM_MASUK_STANDAR = '08:00:00';
    const JAM_PULANG_STANDAR = '17:00:00';

    public function __construct()
    {
        Carbon::setLocale('id');
    }

    private function getCurrentTime()
    {
        $currentTime = TimeService::now();
        $this->autoCloseSkippedDates($currentTime);
        return $currentTime;
    }

    private function resolveApprovedIzinTidakMasukToday($user, $today)
    {
        $absensiToday = Absensi::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->first();

        if ($absensiToday && !in_array($absensiToday->status_harian, ['IZIN_TIDAK_MASUK', 'IZIN_PULANG_CEPAT'])) {
            return null;
        }

        return Izin::where('user_id', $user->id)
            ->whereIn('status_approval', ['approved_hr', 'auto_approved'])
            ->where('jenis_izin', '!=', 'pulang_cepat')
            ->where(function ($q) use ($today) {
                $q->whereDate('tanggal', $today)
                  ->orWhere(function ($sub) use ($today) {
                      $sub->whereDate('tanggal_mulai', '<=', $today)
                          ->whereDate('tanggal_selesai', '>=', $today);
                  });
            })
            ->first();
    }
    
    public function autoCloseSkippedDates($currentTestTime)
    {
        if (!Auth::check()) {
            return;
        }
        
        $user = Auth::user();
        
        if ($user->role !== 'peserta_magang') {
            return;
        }
        
        $periodeStart = $user->periode_magang_mulai ? Carbon::parse($user->periode_magang_mulai) : null;
        $periodeEnd = $user->periode_magang_selesai ? Carbon::parse($user->periode_magang_selesai) : null;
        
        if (!$periodeStart || !$periodeEnd) {
            return;
        }
        
        $today = $currentTestTime->copy()->startOfDay();
        $checkDate = $periodeStart->copy();
        $yesterday = $today->copy()->subDay();
        $alphaCount = 0;
        while ($checkDate->lte($yesterday)) {
            if (!$checkDate->isWeekend()) {
                $absensi = Absensi::where('user_id', $user->id)
                    ->whereDate('tanggal', $checkDate)
                    ->first();
                $hasIzin = Izin::where('user_id', $user->id)
                    ->whereIn('status_approval', ['approved_hr', 'auto_approved'])
                    ->where(function($q) use ($checkDate) {
                        $q->whereDate('tanggal', $checkDate)
                          ->orWhere(function($sub) use ($checkDate) {
                              $sub->whereDate('tanggal_mulai', '<=', $checkDate)
                                  ->whereDate('tanggal_selesai', '>=', $checkDate);
                          });
                    })
                    ->exists();
                $jam17 = $checkDate->copy()->setTime(17,0,0);
                $jam2359 = $checkDate->copy()->setTime(23,59,59);
                $now = $currentTestTime;
                // Jika tidak ada absensi sama sekali dan hari sudah lewat jam 17:00
                if (!$absensi && !$hasIzin && $now->greaterThan($jam17)) {
                    Absensi::create([
                        'user_id' => $user->id,
                        'tanggal' => $checkDate->copy(),
                        'jam_masuk' => null,
                        'jam_pulang' => null,
                        'status' => 'ALPHA',
                        'status_harian' => 'ALPHA',
                        'catatan_sistem' => 'Auto-closed: Tidak hadir pada ' . $checkDate->format('d/m/Y'),
                    ]);
                    $alphaCount++;
                }
                // Jika hanya absen masuk (belum absen pulang) dan sudah lewat jam 23:59
                if ($absensi && $absensi->jam_masuk && !$absensi->jam_pulang && !$hasIzin && $now->greaterThan($jam2359)) {
                    $absensi->update([
                        'status' => 'ALPHA',
                        'status_harian' => 'ALPHA',
                        'catatan_sistem' => 'Auto-closed: Tidak absen pulang pada ' . $checkDate->format('d/m/Y'),
                    ]);
                    $alphaCount++;
                }
            }
            $checkDate->addDay();
        }
        if ($alphaCount > 0) {
            \Log::info("AUTO-CLOSE: {$alphaCount} ALPHA records for user {$user->id}");
        }
    }
    
    public function index()
    {
        $user = Auth::user();
        $today = TimeService::today();

        $absensiToday = Absensi::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->first();
        $absensiList = Absensi::where('user_id', $user->id)
            ->whereMonth('tanggal', $today->month)
            ->whereYear('tanggal', $today->year)
            ->orderBy('tanggal', 'desc')
            ->paginate(15);
        return view('peserta.absensi.index', compact('absensiToday', 'absensiList'));
    }
    
    public function showMasuk()
    {
        $user = Auth::user();
        $now = $this->getCurrentTime();
        $today = $now->copy()->startOfDay();
        
        if ($now->isWeekend()) {
            return redirect()->route('peserta.dashboard')
                ->with('error', 'Tidak dapat absensi di hari ' . $now->translatedFormat('l') . '.');
        }
        
        // Cek apakah ada izin TIDAK MASUK yang disetujui untuk hari ini (EXCLUDE pulang_cepat)
        $hasIzin = $this->resolveApprovedIzinTidakMasukToday($user, $today);
        
        if ($hasIzin) {
            $jenisIzin = ucwords(str_replace('_', ' ', $hasIzin->jenis_izin));
            return redirect()->route('peserta.dashboard')
                ->with('error', 'Tidak dapat absensi karena Anda memiliki izin yang disetujui hari ini (' . $jenisIzin . ').');
        }
        
        $existing = Absensi::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->first();
            
        if ($existing && $existing->jam_masuk) {
            return redirect()->route('peserta.dashboard')
                ->with('error', 'Anda sudah melakukan absensi masuk hari ini pada jam ' . Carbon::parse($existing->jam_masuk)->format('H:i'));
        }
        
        return view('peserta.absensi.masuk', [
            'jamSekarang' => $now->format('H:i'),
            'tanggal' => $now->translatedFormat('l, d F Y'),
            'coordinates' => [
                'lat' => self::LATITUDE,
                'lng' => self::LONGITUDE,
                'radius' => self::MAX_RADIUS_METERS
            ]
        ]);
    }

    public function masuk(Request $request)
    {
        $user = Auth::user();
        $now = $this->getCurrentTime();
        $today = $now->copy()->startOfDay();

        if ($now->isWeekend()) {
            return back()->with('error', 'Tidak dapat absensi di hari libur.');
        }

        // Cek apakah ada izin TIDAK MASUK yang disetujui untuk hari ini (EXCLUDE pulang_cepat)
        $hasIzin = $this->resolveApprovedIzinTidakMasukToday($user, $today);
        
        if ($hasIzin) {
            $jenisIzin = ucwords(str_replace('_', ' ', $hasIzin->jenis_izin));
            return back()->with('error', 'Tidak dapat absensi karena Anda memiliki izin yang disetujui hari ini (' . $jenisIzin . ').');
        }

        $existing = Absensi::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->first();

        if ($existing && $existing->jam_masuk) {
            return back()->with('error', 'Anda sudah absensi masuk hari ini.');
        }

        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'foto' => 'required|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        $fotoPath = $request->file('foto')->store('absensi/masuk/' . $today->format('Y/m'), 'public');

        // Tentukan STATUS MASUK (bukan status harian final)
        $jamMasuk = $now->format('H:i:s');
        $statusMasuk = ($jamMasuk <= self::JAM_MASUK_STANDAR) ? 'TEPAT_WAKTU' : 'TELAT';

        if ($existing) {
            $existing->update([
                'jam_masuk' => $now,
                'foto_masuk' => $fotoPath,
                'latitude_masuk' => $request->latitude,
                'longitude_masuk' => $request->longitude,
                'status_masuk' => $statusMasuk,
                'status_harian' => 'BELUM_FINAL',
                'updated_at' => $now,
            ]);
        } else {
            Absensi::create([
                'user_id' => $user->id,
                'tanggal' => $today,
                'jam_masuk' => $now,
                'foto_masuk' => $fotoPath,
                'latitude_masuk' => $request->latitude,
                'longitude_masuk' => $request->longitude,
                'status_masuk' => $statusMasuk,
                'status_harian' => 'BELUM_FINAL',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $message = $statusMasuk === 'TEPAT_WAKTU' 
            ? 'Absensi masuk berhasil! Selamat bekerja!' 
            : 'Absensi masuk berhasil. Anda terlambat (' . $now->format('H:i') . ').';

        return redirect()->route('peserta.dashboard')->with('success', $message);
    }

    public function showPulang()
    {
        $user = Auth::user();
        $now = $this->getCurrentTime();
        $today = $now->copy()->startOfDay();
        
        $absensi = Absensi::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->first();
            
        if (!$absensi) {
            return redirect()->route('peserta.dashboard')
                ->with('error', 'Anda belum absensi masuk hari ini.');
        }

        if (!$absensi->jam_masuk) {
            return redirect()->route('peserta.dashboard')
                ->with('error', 'Anda belum absensi masuk hari ini.');
        }
        
        if ($absensi->jam_pulang) {
            $message = 'Anda sudah absensi pulang hari ini.';
            
            // Jika ditutup karena izin, berikan informasi lebih detail
            if ($absensi->izin_id && $absensi->catatan_sistem) {
                $message = 'Absensi Anda sudah ditutup otomatis. ' . $absensi->catatan_sistem;
            }
            
            return redirect()->route('peserta.dashboard')
                ->with('error', $message);
        }
        
        // Cek apakah ada izin TIDAK MASUK yang disetujui untuk hari ini (EXCLUDE pulang_cepat)
        $hasIzinTidakMasuk = $this->resolveApprovedIzinTidakMasukToday($user, $today);
        
        if ($hasIzinTidakMasuk) {
            $jenisIzin = ucwords(str_replace('_', ' ', $hasIzinTidakMasuk->jenis_izin));
            return redirect()->route('peserta.dashboard')
                ->with('error', 'Tidak dapat absensi pulang karena Anda memiliki izin yang disetujui hari ini (' . $jenisIzin . ').');
        }
        
        // Cek apakah ada izin pulang cepat yang disetujui hari ini
        $izinPulangCepat = Izin::where('user_id', $user->id)
            ->where('jenis_izin', 'pulang_cepat')
            ->whereDate('tanggal_mulai', $today)
            ->where('status_approval', 'approved_hr')
            ->first();
        
        // Validasi jam pulang - cegah akses halaman absen pulang jika belum waktunya
        if (!$izinPulangCepat) {
            $jamSekarang = Carbon::createFromFormat('H:i:s', $now->format('H:i:s'));
            $jamStandar = Carbon::createFromFormat('H:i:s', self::JAM_PULANG_STANDAR);
            
            if ($jamSekarang->lt($jamStandar)) {
                return redirect()->route('peserta.dashboard')
                    ->with('error', 'Absen pulang hanya dapat dilakukan mulai jam ' . substr(self::JAM_PULANG_STANDAR, 0, 5) . '. Jika ingin pulang lebih awal, silakan ajukan izin pulang cepat.');
            }
        }
        
        return view('peserta.absensi.pulang', [
            'jamSekarang' => $now->format('H:i'),
            'jamMasuk' => Carbon::parse($absensi->jam_masuk)->format('H:i'),
            'tanggal' => $now->translatedFormat('l, d F Y'),
            'izinPulangCepat' => $izinPulangCepat,
            'coordinates' => [
                'lat' => self::LATITUDE,
                'lng' => self::LONGITUDE,
                'radius' => self::MAX_RADIUS_METERS
            ]
        ]);
    }

    // Return server current time for client-side validation
    public function time()
    {
        $now = $this->getCurrentTime();
        return response()->json([
            'time' => $now->format('H:i'),
            'hour' => (int) $now->format('H'),
            'minute' => (int) $now->format('i')
        ]);
    }

    public function pulang(Request $request)
    {
        $user = Auth::user();
        $now = $this->getCurrentTime();
        $today = $now->copy()->startOfDay();

        $absensi = Absensi::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->first();

        if (!$absensi) {
            return back()->with('error', 'Anda belum absensi masuk hari ini.');
        }

        if ($absensi->jam_pulang) {
            return back()->with('error', 'Anda sudah absensi pulang hari ini.');
        }

        // Cek apakah ada izin TIDAK MASUK yang disetujui untuk hari ini (EXCLUDE pulang_cepat)
        $hasIzinTidakMasuk = $this->resolveApprovedIzinTidakMasukToday($user, $today);
        
        if ($hasIzinTidakMasuk) {
            $jenisIzin = ucwords(str_replace('_', ' ', $hasIzinTidakMasuk->jenis_izin));
            return back()->with('error', 'Tidak dapat absensi pulang karena Anda memiliki izin yang disetujui hari ini (' . $jenisIzin . ').');
        }

        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'foto' => 'required',
        ]);

        $jarak = $this->hitungJarak(
            $request->latitude, 
            $request->longitude, 
            self::LATITUDE, 
            self::LONGITUDE
        );
        
        if ($jarak > self::MAX_RADIUS_METERS) {
            return back()->with('error', 'Lokasi terlalu jauh dari kantor (' . round($jarak) . ' meter).');
        }

        // Cek apakah ada izin pulang cepat yang disetujui hari ini
        $izinPulangCepat = Izin::where('user_id', $user->id)
            ->where('jenis_izin', 'pulang_cepat')
            ->whereDate('tanggal_mulai', $today)
            ->where('status_approval', 'approved_hr')
            ->first();

        if ($izinPulangCepat) {
            // Jika ada izin pulang cepat, validasi jam absen pulang
            $jamPulangDiajukan = Carbon::createFromFormat('H:i:s', $izinPulangCepat->jam_pulang_diajukan);
            $jamSekarang = Carbon::createFromFormat('H:i:s', $now->format('H:i:s'));
            
            if ($jamSekarang->lt($jamPulangDiajukan)) {
                return back()->with('error', 
                    'Anda memiliki izin pulang cepat yang disetujui pada jam ' . $jamPulangDiajukan->format('H:i') . 
                    '. Silakan absen pulang pada atau setelah jam tersebut. (Jam sekarang: ' . $jamSekarang->format('H:i') . ')'
                );
            }
            // Jika jam sekarang >= jam izin, lanjutkan proses absen normal dengan jam real
        } else {
            // Jika tidak ada izin pulang cepat, cek jam standar
            $jamSekarang = Carbon::createFromFormat('H:i:s', $now->format('H:i:s'));
            $jamStandar = Carbon::createFromFormat('H:i:s', self::JAM_PULANG_STANDAR);
            
            \Log::info('Validasi jam pulang:', [
                'jam_sekarang' => $jamSekarang->format('H:i:s'),
                'jam_standar' => $jamStandar->format('H:i:s'),
                'is_before' => $jamSekarang->lt($jamStandar)
            ]);
            
            if ($jamSekarang->lt($jamStandar)) {
                return back()->with('error', 
                    'Absen pulang hanya dapat dilakukan mulai jam ' . substr(self::JAM_PULANG_STANDAR, 0, 5) . '. Jika ingin pulang lebih awal, silakan ajukan izin pulang cepat.'
                );
            }
        }

        $fotoBase64 = $request->foto;
        
        if (preg_match('/^data:image\/(\w+);base64,/', $fotoBase64, $type)) {
            $fotoBase64 = substr($fotoBase64, strpos($fotoBase64, ',') + 1);
            $type = strtolower($type[1]);
            
            if (!in_array($type, ['jpg', 'jpeg', 'png'])) {
                return back()->with('error', 'Format foto tidak valid.');
            }
            
            $fotoBase64 = base64_decode($fotoBase64);
            
            if ($fotoBase64 === false) {
                return back()->with('error', 'Gagal memproses foto.');
            }
        } else {
            return back()->with('error', 'Format foto tidak valid.');
        }
        
        $fileName = 'pulang_' . $user->id . '_' . $today->format('Ymd_His') . '.jpg';
        $filePath = 'absensi/pulang/' . $today->format('Y/m') . '/' . $fileName;
        Storage::disk('public')->put($filePath, $fotoBase64);

        $jamMasukCarbon = Carbon::createFromFormat('Y-m-d H:i:s', $today->format('Y-m-d') . ' ' . $absensi->jam_masuk);
        $jamPulangCarbon = Carbon::createFromFormat('Y-m-d H:i:s', $today->format('Y-m-d') . ' ' . $now->format('H:i:s'));
        $durasiMenit = $jamMasukCarbon->diffInMinutes($jamPulangCarbon);

        // Tentukan status harian final
        if ($izinPulangCepat) {
            // Jika ada izin pulang cepat yang disetujui, status = IZIN
            $statusHarianFinal = 'IZIN';
        } else {
            // Jika tidak ada izin, gunakan logic normal
            $statusHarianFinal = $absensi->status_masuk === 'TELAT' ? 'HADIR_TELAT' : 'HADIR_TEPAT_WAKTU';
        }

        $absensi->update([
            'jam_pulang' => $now,
            'foto_pulang' => $filePath,
            'latitude_pulang' => $request->latitude,
            'longitude_pulang' => $request->longitude,
            'durasi_kerja' => $durasiMenit,
            'status' => $statusHarianFinal,
            'status_harian' => $statusHarianFinal,
        ]);

        return redirect()->route('peserta.dashboard')
            ->with('success', 'Absensi pulang berhasil! Terima kasih atas kerja keras Anda.');
    }
    
    private function hitungJarak($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon/2) * sin($dLon/2);

        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $earthRadius * $c;
    }

    public function riwayat()
    {
        $user = Auth::user();
        
        // Cek periode magang
        $periodeStart = $user->periode_magang_mulai ? Carbon::parse($user->periode_magang_mulai) : null;
        $periodeEnd = $user->periode_magang_selesai ? Carbon::parse($user->periode_magang_selesai) : null;
        
        // Jika tidak ada periode magang, fallback ke logic lama (hanya existing records)
        if (!$periodeStart || !$periodeEnd) {
            $query = Absensi::where('user_id', $user->id);
            if (request('bulan')) {
                $query->whereMonth('tanggal', request('bulan'));
            }
            if (request('tahun')) {
                $query->whereYear('tanggal', request('tahun'));
            }
            $absensiList = $query->orderBy('tanggal', 'desc')->paginate(20);
            
            $stats = [
                'total_hadir' => Absensi::where('user_id', $user->id)->whereIn('status_harian', ['HADIR_TEPAT_WAKTU', 'HADIR_TELAT'])->count(),
                'tepat_waktu' => Absensi::where('user_id', $user->id)->where('status_harian', 'HADIR_TEPAT_WAKTU')->count(),
                'terlambat' => Absensi::where('user_id', $user->id)->where('status_harian', 'HADIR_TELAT')->count(),
                'alpha' => Absensi::where('user_id', $user->id)->where('status_harian', 'ALPHA')->count(),
                'izin_tidak_masuk' => Absensi::where('user_id', $user->id)->where('status_harian', 'IZIN_TIDAK_MASUK')->count(),
                'izin_pulang_cepat' => Absensi::where('user_id', $user->id)->where('status_harian', 'IZIN_PULANG_CEPAT')->count(),
            ];
            return view('peserta.absensi.riwayat', compact('absensiList', 'stats', 'monthsList', 'filterBulan', 'filterTahun'));
        }
        
        // Generate semua tanggal dari start sampai end
        $allDates = [];
        
        // Generate list of months for filter dropdown
        $monthsList = [];
        $monthIterator = $periodeStart->copy()->startOfMonth();
        $endMonth = $periodeEnd->copy()->endOfMonth();
        
        while ($monthIterator->lte($endMonth)) {
            $monthsList[] = [
                'month' => $monthIterator->month,
                'year' => $monthIterator->year,
                'label' => $monthIterator->translatedFormat('F Y')
            ];
            $monthIterator->addMonth();
        }

        $currentDate = $periodeStart->copy();
        
        // Filter bulan/tahun jika ada request
        $filterBulan = request('bulan');
        $filterTahun = request('tahun');
        
        while ($currentDate->lte($periodeEnd)) {
            // Terapkan filter jika ada
            if (($filterBulan && $currentDate->month != $filterBulan) || 
                ($filterTahun && $currentDate->year != $filterTahun)) {
                $currentDate->addDay();
                continue;
            }
            
            $allDates[] = $currentDate->copy();
            $currentDate->addDay();
        }
        
        // Ambil data absensi yang ada
        $existingAbsensi = Absensi::where('user_id', $user->id)
            ->whereBetween('tanggal', [$periodeStart->format('Y-m-d'), $periodeEnd->format('Y-m-d')])
            ->get()
            ->keyBy(function($item) {
                return $item->tanggal->format('Y-m-d');
            });
            
        // Ambil data izin yang approved (Approved HR)
        $approvedIzin = Izin::where('user_id', $user->id)
            ->whereIn('status_approval', ['approved_hr', 'auto_approved'])
            ->get();
            
        // Map izin ke tanggal
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
        
        // Merge data
        $mergedData = collect($allDates)->map(function($date) use ($existingAbsensi, $izinByDate, $user) {
            $dateStr = $date->format('Y-m-d');
            
            if (isset($existingAbsensi[$dateStr])) {
                $record = $existingAbsensi[$dateStr];
                // Update ke database jika ada izin approved tapi status masih ALPHA
                if (isset($izinByDate[$dateStr]) && ($record->status_harian === 'ALPHA' || $record->status_harian === '-' || $record->status_harian === null || $record->status === 'ALPHA')) {
                    $statusHarian = $izinByDate[$dateStr]->jenis_izin == 'tidak_masuk' ? 'IZIN_TIDAK_MASUK' : 'IZIN_PULANG_CEPAT';
                    $jenisIzinText = $izinByDate[$dateStr]->jenis_izin == 'tidak_masuk' ? 'Izin tidak masuk' : 'Izin pulang cepat';
                    $record->update([
                        'status' => $statusHarian,
                        'status_harian' => $statusHarian,
                        'catatan_sistem' => $jenisIzinText
                    ]);
                }
                return $record;
            }
            
            // Buat dummy object untuk view
            $absensi = new Absensi();
            $absensi->user_id = $user->id;
            $absensi->tanggal = $date;
            
            if (isset($izinByDate[$dateStr])) {
                // Jika ada izin, langsung create record IZIN
                $existingRecord = Absensi::where('user_id', $user->id)
                    ->whereDate('tanggal', $date)
                    ->first();
                    
                if (!$existingRecord && !$date->isWeekend() && $izinByDate[$dateStr]->jenis_izin !== 'pulang_cepat') {
                    $statusHarian = $izinByDate[$dateStr]->jenis_izin == 'tidak_masuk' ? 'IZIN_TIDAK_MASUK' : 'IZIN_PULANG_CEPAT';
                    $jenisIzinText = $izinByDate[$dateStr]->jenis_izin == 'tidak_masuk' ? 'Izin tidak masuk' : 'Izin pulang cepat';
                    $absensi = Absensi::create([
                        'user_id' => $user->id,
                        'tanggal' => $date,
                        'status' => $statusHarian,
                        'status_harian' => $statusHarian,
                        'catatan_sistem' => $jenisIzinText,
                        'created_at' => TimeService::now(),
                        'updated_at' => TimeService::now(),
                    ]);
                } elseif ($existingRecord) {
                    $absensi = $existingRecord;
                } else {
                    $statusHarian = $izinByDate[$dateStr]->jenis_izin == 'tidak_masuk' ? 'IZIN_TIDAK_MASUK' : 'IZIN_PULANG_CEPAT';
                    $jenisIzinText = $izinByDate[$dateStr]->jenis_izin == 'tidak_masuk' ? 'Izin tidak masuk' : 'Izin pulang cepat';
                    $absensi->status_harian = $statusHarian;
                    $absensi->catatan_sistem = $jenisIzinText;
                }
            } elseif ($date->isWeekend()) {
                $absensi->status_harian = 'LIBUR';
            } elseif ($date->gt(TimeService::now())) {
                $absensi->status_harian = '-'; // Belum waktunya
            } else {
                // Tanggal lewat tapi tidak ada data absensi
                // Bisa jadi belum auto-close, atau user belum login
                $absensi->status_harian = '-'; 
            }
            
            return $absensi;
        })->sortByDesc('tanggal'); // Urutkan terbaru -> terlama
        
        // Manual Pagination
        $page = request()->get('page', 1);
        $perPage = 20;
        $slicedData = $mergedData->slice(($page - 1) * $perPage, $perPage)->values();
        
        $absensiList = new LengthAwarePaginator(
                $slicedData,
                $mergedData->count(),
                $perPage,
                $page,
                ['path' => request()->url(), 'query' => request()->query()]
            );
            
            $stats = [
                'total_hadir' => $mergedData->whereIn('status_harian', ['HADIR_TEPAT_WAKTU', 'HADIR_TELAT'])->count(),
                'tepat_waktu' => $mergedData->where('status_harian', 'HADIR_TEPAT_WAKTU')->count(),
                'terlambat' => $mergedData->where('status_harian', 'HADIR_TELAT')->count(),
                'alpha' => $mergedData->where('status_harian', 'ALPHA')->count(),
                'izin_tidak_masuk' => $mergedData->where('status_harian', 'IZIN_TIDAK_MASUK')->count(),
                'izin_pulang_cepat' => $mergedData->where('status_harian', 'IZIN_PULANG_CEPAT')->count(),
            ];
            
            return view('peserta.absensi.riwayat', compact('absensiList', 'stats', 'monthsList', 'filterBulan', 'filterTahun'));
        }
}
