<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\Izin;
use App\Models\ApprovalLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Services\TimeService;
use App\Services\IzinAutoApproveService;

class IzinController extends Controller
{
    public function index()
    {
        // Auto-approve izin yang sudah lebih dari 24 jam (on-demand)
        IzinAutoApproveService::processAutoApproval();

        $user = Auth::user();

        $query = Izin::where('user_id', $user->id)
            ->with(['approvalLogs']);

        if (request('status')) {
            $query->where('status_approval', request('status'));
        }

        $izinList = $query->orderBy('created_at', 'desc')->paginate(15);

        $stats = [
            'pending' => Izin::where('user_id', $user->id)->where('status_approval', 'pending')->count(),
            'approved' => Izin::where('user_id', $user->id)->where('status_approval', 'approved_hr')->count(),
            'rejected' => Izin::where('user_id', $user->id)->where('status_approval', 'rejected_hr')->count(),
        ];

        return view('peserta.izin.index', compact('izinList', 'stats'));
    }

    public function create()
    {
        $today = TimeService::today()->format('Y-m-d');
        $tomorrow = TimeService::today()->copy()->addDay()->format('Y-m-d');

        return view('peserta.izin.create')->with([
            'today' => $today,
            'tomorrow' => $tomorrow,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $now = TimeService::now();

        $request->validate([
            'jenis_izin' => 'required|in:tidak_masuk,pulang_cepat',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan' => 'required|string|max:500',
            'bukti' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'jam_pulang_diajukan' => 'required_if:jenis_izin,pulang_cepat|nullable|date_format:H:i',
        ]);

        // Validasi weekend - tidak boleh mengajukan izin di Sabtu/Minggu
        $tanggalMulai = Carbon::parse($request->tanggal_mulai);
        $tanggalSelesai = Carbon::parse($request->tanggal_selesai);
        
        if ($tanggalMulai->isWeekend() || $tanggalSelesai->isWeekend()) {
            return back()->with('error', 
                'Tidak dapat mengajukan izin di hari Sabtu atau Minggu.'
            )->withInput();
        }

        // Prevent 'tidak_masuk' being applied for today — use 'pulang_cepat' for same-day requests
        if ($request->jenis_izin == 'tidak_masuk') {
            $today = TimeService::today()->startOfDay();
            $tomorrow = $today->copy()->addDay();
            $start = Carbon::parse($request->tanggal_mulai)->startOfDay();
            $end = Carbon::parse($request->tanggal_selesai)->startOfDay();

            if ($start->lt($tomorrow) || $end->lt($tomorrow)) {
                return back()->with('error', 
                    'Izin "Tidak Masuk" hanya dapat diajukan mulai besok. Gunakan "Pulang Cepat" untuk hari ini.'
                )->withInput();
            }
        }
        
        // Cek range tanggal untuk izin tidak masuk
        if ($request->jenis_izin == 'tidak_masuk') {
            $currentDate = $tanggalMulai->copy();
            while ($currentDate->lte($tanggalSelesai)) {
                if ($currentDate->isWeekend()) {
                    return back()->with('error', 
                        'Tidak dapat mengajukan izin di hari Sabtu atau Minggu (' . $currentDate->format('d/m/Y') . ').'
                    )->withInput();
                }
                $currentDate->addDay();
            }
        }

        if ($request->jenis_izin == 'tidak_masuk') {
            $tanggalMulai = Carbon::parse($request->tanggal_mulai);
            $tanggalSelesai = Carbon::parse($request->tanggal_selesai);
            
            $currentDate = $tanggalMulai->copy();
            while ($currentDate->lte($tanggalSelesai)) {
                if ($currentDate->isWeekend()) {
                    $currentDate->addDay();
                    continue;
                }
                
                $absensi = \App\Models\Absensi::where('user_id', $user->id)
                    ->whereDate('tanggal', $currentDate)
                    ->first();
                
                if ($absensi && $absensi->jam_pulang) {
                    $jamPulang = Carbon::parse($absensi->jam_pulang);
                    if ($jamPulang->format('H:i') >= '17:00') {
                        return back()->with('error', 
                            'Tidak dapat mengajukan izin untuk tanggal ' . $currentDate->format('d/m/Y') . 
                            '. Anda sudah pulang jam ' . $jamPulang->format('H:i') . '.'
                        )->withInput();
                    }
                }
                
                $currentDate->addDay();
            }
        } elseif ($request->jenis_izin == 'pulang_cepat') {
            $tanggal = Carbon::parse($request->tanggal_mulai);
            $today = TimeService::today();
            
            // Validasi: izin pulang cepat hanya untuk hari ini
            if (!$tanggal->isSameDay($today)) {
                return back()->with('error', 
                    'Izin pulang cepat hanya dapat digunakan untuk hari ini. ' .
                    'Untuk hari lain, gunakan izin tidak masuk.'
                )->withInput();
            }
            
            // Validasi: harus sudah absen masuk di hari ini
            $absensi = \App\Models\Absensi::where('user_id', $user->id)
                ->whereDate('tanggal', $tanggal)
                ->first();
            
            if (!$absensi || !$absensi->jam_masuk) {
                return back()->with('error', 
                    'Izin pulang cepat hanya dapat digunakan setelah Anda melakukan absen masuk di hari ini.'
                )->withInput();
            }
            
            if ($absensi && $absensi->jam_pulang) {
                return back()->with('error', 
                    'Anda sudah pulang pada jam ' . 
                    Carbon::parse($absensi->jam_pulang)->format('H:i') . '.'
                )->withInput();
            }
            
            if ($request->jam_pulang_diajukan && $request->jam_pulang_diajukan >= '17:00') {
                return back()->with('error', 
                    'Jam pulang yang diajukan (' . $request->jam_pulang_diajukan . ') harus sebelum 17:00.'
                )->withInput();
            }
        }

        $overlap = Izin::where('user_id', $user->id)
            ->where('status_approval', '!=', 'rejected_hr')
            ->where(function($q) use ($request) {
                $q->whereBetween('tanggal_mulai', [$request->tanggal_mulai, $request->tanggal_selesai])
                  ->orWhereBetween('tanggal_selesai', [$request->tanggal_mulai, $request->tanggal_selesai])
                  ->orWhere(function($q2) use ($request) {
                      $q2->where('tanggal_mulai', '<=', $request->tanggal_mulai)
                         ->where('tanggal_selesai', '>=', $request->tanggal_selesai);
                  });
            })
            ->exists();

        if ($overlap) {
            return back()->with('error', 'Anda sudah memiliki izin pada periode tersebut.')
                ->withInput();
        }

        $buktiPath = null;
        if ($request->hasFile('bukti')) {
            $buktiPath = $request->file('bukti')->store('bukti_izin', 'public');
        }

        $tanggalMulai = Carbon::parse($request->tanggal_mulai);
        $tanggalSelesai = Carbon::parse($request->tanggal_selesai);
        $jumlahHari = $tanggalMulai->diffInDays($tanggalSelesai) + 1;

        $izinData = [
            'user_id' => $user->id,
            'jenis_izin' => $request->jenis_izin,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'alasan' => $request->alasan,
            'bukti_file' => $buktiPath,
            'status_approval' => 'pending',
        ];
        
        if ($request->jenis_izin == 'pulang_cepat') {
            $izinData['jam_pulang_diajukan'] = $request->jam_pulang_diajukan;
            $izinData['tanggal'] = $request->tanggal_mulai;
        }
        
        // Persist izin using centralized time for created_at/updated_at
        $izinData['created_at'] = $now;
        $izinData['updated_at'] = $now;
        $izin = Izin::create($izinData);

        return redirect()->route('peserta.izin.index')
            ->with('success', 'Pengajuan izin berhasil dikirim. Menunggu persetujuan Ketua Bidang.');
    }

    public function show(Izin $izin)
    {
        if ($izin->user_id !== Auth::id()) {
            abort(403);
        }

        $izin->load(['approvalLogs.approver', 'user.bidang']);

        return view('peserta.izin.show', compact('izin'));
    }

    public function destroy(Izin $izin)
    {
        if ($izin->user_id !== Auth::id()) {
            abort(403);
        }

        if ($izin->status_approval !== 'pending') {
            return back()->with('error', 'Tidak dapat membatalkan izin yang sudah diproses.');
        }

        if ($izin->bukti_file) {
            Storage::disk('public')->delete($izin->bukti_file);
        }

        $izin->delete();

        return redirect()->route('peserta.izin.index')
            ->with('success', 'Pengajuan izin berhasil dibatalkan.');
    }
}
