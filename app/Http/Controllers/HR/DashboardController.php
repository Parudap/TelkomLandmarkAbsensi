<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Absensi;
use App\Models\Izin;
use App\Models\Bidang;
use Carbon\Carbon;
use App\Services\TimeService;
use App\Services\IzinAutoApproveService;

class DashboardController extends Controller
{
    public function index()
    {
        // Auto-approve izin yang sudah lebih dari 24 jam (on-demand)
        IzinAutoApproveService::processAutoApproval();

        $thisMonth = TimeService::now()->month;
        $thisYear = TimeService::now()->year;

        // Total statistik
        $stats = [
            'total_peserta' => User::where('role', 'peserta_magang')
                ->count(),
            
            'peserta_aktif' => User::where('role', 'peserta_magang')
                ->where('is_active', true)
                ->count(),
            
            'pending_approval' => User::where('role', 'peserta_magang')
                ->where('status_approval', 'pending')
                ->count(),
            
            'hadir_today' => Absensi::whereDate('tanggal', TimeService::today())
                ->whereIn('status_harian', ['HADIR_TEPAT_WAKTU', 'HADIR_TELAT'])
                ->count(),
            
            'izin_pending' => Izin::where('status_approval', 'pending')
                ->count(),
        ];

        // Pendaftar baru yang perlu di-approve
        $pendaftarBaru = User::where('role', 'peserta_magang')
            ->where('status_approval', 'pending')
            ->with('bidang')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Izin yang menunggu approval HR
        $izinPending = Izin::with(['user.bidang'])
            ->where('status_approval', 'pending')
            ->orderBy('created_at', 'asc')
            ->take(5)
            ->get();

        // Statistik per bidang
        $statsBidang = Bidang::query()
            ->withCount([
                'users as total_peserta' => function($q) {
                    $q->where('role', 'peserta_magang');
                },
                'users as peserta_aktif' => function($q) {
                    $q->where('role', 'peserta_magang')
                      ->where('is_active', true);
                },
                'users as hadir_today' => function($q) {
                    $q->where('role', 'peserta_magang')
                      ->whereHas('absensi', function($q2) {
                          $q2->whereDate('tanggal', TimeService::today());
                      });
                }
            ])
            ->addSelect([
                'izin_pending' => Izin::query()
                    ->selectRaw('COUNT(*)')
                    ->join('users', 'users.id', '=', 'izin.user_id')
                    ->whereColumn('users.bidang_id', 'bidang.id')
                    ->where('users.role', 'peserta_magang')
                    ->where('izin.status_approval', 'pending')
            ])
            ->get();

        return view('hr.dashboard', compact(
            'stats',
            'pendaftarBaru',
            'izinPending',
            'statsBidang'
        ));
    }
}
