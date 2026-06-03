<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Izin;
use App\Models\Absensi;
use App\Models\ApprovalLog;
use App\Models\Bidang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Services\TimeService;
use App\Services\IzinAutoApproveService;
use Barryvdh\DomPDF\Facade\Pdf;

class ApprovalIzinController extends Controller
{
    public function index()
    {
        // Auto-approve izin yang sudah lebih dari 24 jam (on-demand)
        IzinAutoApproveService::processAutoApproval();

        $request = request();
        $request->validate([
            'status' => 'nullable|in:pending,approved,rejected',
            'bidang_id' => 'nullable|exists:bidang,id',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'search_nama' => 'nullable|string|max:100',
        ]);

        $query = Izin::with(['user.bidang']);
        $this->applyIzinFilters($query, $request);

        $izinList = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
        $bidangList = Bidang::where('is_active', true)->orderBy('nama_bidang')->get();

        $stats = [
            'pending' => Izin::where('status_approval', 'pending')->count(),
            'approved' => Izin::where('status_approval', 'approved_hr')->count(),
            'rejected' => Izin::where('status_approval', 'rejected_hr')->count(),
        ];

        return view('hr.izin.index', compact('izinList', 'stats', 'bidangList'));
    }

    public function show(Izin $izin)
    {
        $izin->load(['user.bidang', 'approvalLogs.approver']);

        return view('hr.izin.show', compact('izin'));
    }

    public function approve(Request $request, Izin $izin)
    {
        if ($izin->status_approval !== 'pending') {
            return back()->with('error', 'Izin ini sudah diproses sebelumnya.');
        }

        $request->validate([
            'keterangan' => 'nullable|string|max:500',
        ]);

        $izin->update([
            'status_approval' => 'approved_hr',
            'approved_at_hr' => TimeService::now(),
        ]);

        // START: Update/Create Absensi records for the approved dates
        $datesToUpdate = [];
        if ($izin->tanggal) {
            $datesToUpdate[] = $izin->tanggal;
        } elseif ($izin->tanggal_mulai && $izin->tanggal_selesai) {
            $start = $izin->tanggal_mulai->copy();
            $end = $izin->tanggal_selesai->copy();
            while ($start->lte($end)) {
                $datesToUpdate[] = $start->copy();
                $start->addDay();
            }
        }

        foreach ($datesToUpdate as $date) {
            // Skip weekend
            if ($date->isWeekend()) {
                continue;
            }
            
            // Handle pulang_cepat differently - auto close absensi if already clocked in
            if ($izin->jenis_izin === 'pulang_cepat') {
                $absensi = Absensi::where('user_id', $izin->user_id)
                    ->whereDate('tanggal', $date)
                    ->first();

                if ($absensi) {
                    // Jika sudah absen masuk tapi belum pulang - auto tutup
                    if ($absensi->jam_masuk && !$absensi->jam_pulang) {
                        $jamPulangIzin = Carbon::parse($date->format('Y-m-d') . ' ' . $izin->jam_pulang_diajukan);
                        $jamMasukCarbon = Carbon::parse($absensi->tanggal->format('Y-m-d') . ' ' . $absensi->jam_masuk);
                        $durasiMenit = $jamMasukCarbon->diffInMinutes($jamPulangIzin);

                        $absensi->update([
                            'jam_pulang' => $jamPulangIzin,
                            'status_harian' => 'IZIN_PULANG_CEPAT',
                            'durasi_kerja' => $durasiMenit,
                            'izin_id' => $izin->id,
                            'catatan_sistem' => 'Izin pulang cepat disetujui pada jam ' . $izin->jam_pulang_diajukan,
                        ]);
                    }
                    // Jika status ALPHA (sudah di-autoclose sebelumnya) - backfill to IZIN_PULANG_CEPAT
                    elseif ($absensi->status_harian === 'ALPHA') {
                        $absensi->update([
                            'status_harian' => 'IZIN_PULANG_CEPAT',
                            'izin_id' => $izin->id,
                            'catatan_sistem' => 'Izin pulang cepat disetujui (backfilled dari ALPHA)',
                        ]);
                    }
                }
                continue;
            }

            // Handle izin tidak masuk - create/update absensi dengan status IZIN
            $absensi = Absensi::where('user_id', $izin->user_id)
                ->whereDate('tanggal', $date)
                ->first();

            if ($absensi) {
                // Jika sudah ada absensi masuk tapi belum pulang - tutup dengan status sesuai jenis izin
                if ($absensi->jam_masuk && !$absensi->jam_pulang) {
                    $jamPulangStandar = Carbon::parse($date->format('Y-m-d') . ' 17:00:00');
                    $jamMasukCarbon = Carbon::parse($absensi->tanggal->format('Y-m-d') . ' ' . $absensi->jam_masuk);
                    $durasiMenit = $jamMasukCarbon->diffInMinutes($jamPulangStandar);

                    $statusHarian = $izin->jenis_izin == 'tidak_masuk' ? 'IZIN_TIDAK_MASUK' : 'IZIN_PULANG_CEPAT';
                    $jenisIzinText = $izin->jenis_izin == 'tidak_masuk' ? 'Izin tidak masuk' : 'Izin pulang cepat';
                    $absensi->update([
                        'jam_pulang' => $jamPulangStandar,
                        'status' => $statusHarian,
                        'status_harian' => $statusHarian,
                        'durasi_kerja' => $durasiMenit,
                        'izin_id' => $izin->id,
                        'catatan_sistem' => $jenisIzinText,
                    ]);
                }
                // Update jika bukan HADIR (termasuk ALPHA atau masih kosong)
                elseif (!in_array($absensi->status_harian, ['HADIR_TEPAT_WAKTU', 'HADIR_TELAT'])) {
                     $statusHarian = $izin->jenis_izin == 'tidak_masuk' ? 'IZIN_TIDAK_MASUK' : 'IZIN_PULANG_CEPAT';
                     $jenisIzinText = $izin->jenis_izin == 'tidak_masuk' ? 'Izin tidak masuk' : 'Izin pulang cepat';
                     $absensi->update([
                        'status' => $statusHarian,
                        'status_harian' => $statusHarian,
                        'izin_id' => $izin->id,
                        'catatan_sistem' => $jenisIzinText,
                    ]);
                }
            } else {
                // Create new record jika belum ada
                $statusHarian = $izin->jenis_izin == 'tidak_masuk' ? 'IZIN_TIDAK_MASUK' : 'IZIN_PULANG_CEPAT';
                $jenisIzinText = $izin->jenis_izin == 'tidak_masuk' ? 'Izin tidak masuk' : 'Izin pulang cepat';
                Absensi::create([
                    'user_id' => $izin->user_id,
                    'tanggal' => $date,
                    'status' => $statusHarian,
                    'status_harian' => $statusHarian,
                    'izin_id' => $izin->id,
                    'catatan_sistem' => $jenisIzinText,
                    'created_at' => TimeService::now(),
                    'updated_at' => TimeService::now(),
                ]);
            }
        }
        // END: Update/Create Absensi records

        ApprovalLog::create([
            'approvable_type' => Izin::class,
            'approvable_id' => $izin->id,
            'tipe_approval' => 'izin_layer2',
            'status' => 'approved',
            'approver_id' => Auth::id(),
            'approver_role' => 'hr',
            'keterangan' => $request->keterangan ?? 'Disetujui oleh HR',
            'approved_at' => TimeService::now(),
            'created_at' => TimeService::now(),
            'updated_at' => TimeService::now(),
        ]);

        return redirect()->route('hr.izin.index')
            ->with('success', 'Izin disetujui.');
    }

    public function reject(Request $request, Izin $izin)
    {
        if ($izin->status_approval !== 'pending') {
            return back()->with('error', 'Izin ini sudah diproses sebelumnya.');
        }

        $request->validate([
            'keterangan' => 'required|string|max:500',
        ]);

        $izin->update([
            'status_approval' => 'rejected_hr',
            'approved_at_hr' => TimeService::now(),
        ]);

        ApprovalLog::create([
            'approvable_type' => Izin::class,
            'approvable_id' => $izin->id,
            'tipe_approval' => 'izin_layer2',
            'status' => 'rejected',
            'approver_id' => Auth::id(),
            'approver_role' => 'hr',
            'keterangan' => $request->keterangan,
            'approved_at' => TimeService::now(),
            'created_at' => TimeService::now(),
            'updated_at' => TimeService::now(),
        ]);

        return redirect()->route('hr.izin.index')
            ->with('success', 'Izin berhasil ditolak.');
    }

    public function exportCsv(Request $request)
    {
        $request->validate([
            'status' => 'nullable|in:pending,approved,rejected',
            'bidang_id' => 'nullable|exists:bidang,id',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'search_nama' => 'nullable|string|max:100',
        ]);

        $query = Izin::with(['user.bidang']);
        $this->applyIzinFilters($query, $request);

        $izinList = $query->orderBy('created_at', 'desc')->get();

        $filename = 'approval_izin_' . ($request->status ?? 'all') . '_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function() use ($izinList) {
            $file = fopen('php://output', 'w');
            
            // BOM untuk Excel UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header CSV
            fputcsv($file, [
                'No',
                'Nama Peserta',
                'Email',
                'Bidang',
                'Jenis Izin',
                'Tanggal Mulai',
                'Tanggal Selesai',
                'Jam Pulang',
                'Alasan',
                'Tanggal Pengajuan',
                'Status',
                'Disetujui Pada',
                'Auto Approve'
            ], ';');
            
            // Data rows
            $no = 1;
            foreach ($izinList as $izin) {
                $status = match($izin->status_approval) {
                    'pending' => 'Menunggu Approval',
                    'approved_hr' => 'Disetujui',
                    'rejected_hr' => 'Ditolak',
                    default => $izin->status_approval
                };

                // Format tanggal dengan prefix tab untuk Excel
                $tanggalMulai = $izin->tanggal_mulai ? "\t" . $izin->tanggal_mulai->format('d/m/Y') : ($izin->tanggal ? "\t" . $izin->tanggal->format('d/m/Y') : '-');
                $tanggalSelesai = $izin->tanggal_selesai ? "\t" . $izin->tanggal_selesai->format('d/m/Y') : ($izin->tanggal ? "\t" . $izin->tanggal->format('d/m/Y') : '-');
                $tanggalPengajuan = "\t" . $izin->created_at->format('d/m/Y H:i');
                $disetujuiPada = $izin->approved_at_hr ? "\t" . $izin->approved_at_hr->format('d/m/Y H:i') : '-';
                $autoApprove = $izin->auto_approved_hr_at ? 'Ya' : 'Tidak';

                fputcsv($file, [
                    $no++,
                    $izin->user->name ?? '-',
                    $izin->user->email ?? '-',
                    $izin->user->bidang->nama_bidang ?? '-',
                    $izin->jenis_izin == 'tidak_masuk' ? 'Tidak Masuk' : 'Pulang Cepat',
                    $tanggalMulai,
                    $tanggalSelesai,
                    $izin->jam_pulang_diajukan ?? '-',
                    $izin->alasan ?? '-',
                    $tanggalPengajuan,
                    $status,
                    $disetujuiPada,
                    $autoApprove
                ], ';');
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        // Auto-approve izin yang sudah lebih dari 24 jam (on-demand)
        IzinAutoApproveService::processAutoApproval();

        $request->validate([
            'status' => 'nullable|in:pending,approved,rejected',
            'bidang_id' => 'nullable|exists:bidang,id',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'search_nama' => 'nullable|string|max:100',
        ]);

        $query = Izin::with(['user.bidang']);
        $this->applyIzinFilters($query, $request);

        $statusLabel = $this->getStatusLabel($request->status);

        $izinList = $query->orderBy('created_at', 'desc')->get();

        $stats = [
            'pending' => Izin::where('status_approval', 'pending')->count(),
            'approved' => Izin::where('status_approval', 'approved_hr')->count(),
            'rejected' => Izin::where('status_approval', 'rejected_hr')->count(),
        ];

        $bidangLabel = '-';
        if ($request->filled('bidang_id')) {
            $bidangLabel = optional(Bidang::find($request->bidang_id))->nama_bidang ?? '-';
        }

        $tanggalMulai = $request->tanggal_mulai ?: '-';
        $tanggalSelesai = $request->tanggal_selesai ?: '-';

        $pdf = Pdf::loadView('hr.izin.export-pdf', compact('izinList', 'stats', 'statusLabel', 'bidangLabel', 'tanggalMulai', 'tanggalSelesai'))
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'isRemoteEnabled' => true,
                'defaultFont' => 'Arial'
            ]);

        $filename = 'approval_izin_' . ($request->status ?? 'all') . '_' . date('Y-m-d_His') . '.pdf';
        return $pdf->download($filename);
    }

    private function applyIzinFilters($query, Request $request): void
    {
        if ($request->status === 'pending') {
            $query->where('status_approval', 'pending');
        } elseif ($request->status === 'approved') {
            $query->where('status_approval', 'approved_hr');
        } elseif ($request->status === 'rejected') {
            $query->where('status_approval', 'rejected_hr');
        }

        if ($request->filled('bidang_id')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('bidang_id', $request->bidang_id);
            });
        }

        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('created_at', '>=', $request->tanggal_mulai);
        }

        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('created_at', '<=', $request->tanggal_selesai);
        }

        if ($request->filled('search_nama')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search_nama . '%')
                  ->orWhere('email', 'like', '%' . $request->search_nama . '%');
            });
        }
    }

    private function getStatusLabel(?string $status): string
    {
        return match ($status) {
            'pending' => 'Menunggu Approval',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default => 'Semua Status',
        };
    }
}
